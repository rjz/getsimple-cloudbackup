<?php
/*
Plugin Name: Cloud Backup Plugin
Description: Automated remote backups via Dropbox or FTP
Version: 1.0
Author: RJ Zaworski <rj@rjzaworski.com>
Author URI: http://rjzaworski.com/
*/

if (!class_exists('CloudBackupPlugin')):

require('cloudbackup/get_simple_plugin.php');

// provide debugging on request. Uncomment the next line to
// enable verbose error reporting:
//     define('DEBUG', 1);

if (defined('DEBUG')) {
	error_reporting(-1);
	ini_set('display_errors', 'On');
	ini_set('html_errors', 'On');
}

// Register an autoload function for CloudBackup classes
spl_autoload_register(function ($classname) {
	if (strpos($classname, 'CloudBackup') === 0) {
		$filename = strtolower(str_replace('\\', '/', $classname));
		include_once(__DIR__ . "/$filename.php");
	}
});

/**
 *	Cloud Backup Plugin
 *	@author	RJ Zaworski (@rjzaworski)
 */
class CloudBackupPlugin extends GetSimplePlugin {

	protected

		$_defaults = array(

			'provider' => 'dropbox',
			'archiver' => 'tarball',

			'notifier_email' => '',

			'schedule_enabled' => 0,
			'schedule_frequency' => 0,
			'schedule_lastrun' => 0,
			'schedule_next' => 0,
			'schedule_start' => 0
		),

		$_actions = array(
			'admin-pre-header'   => 'pre_route_handler',
			'backups-sidebar'    => 'admin_menu',
			'index-posttemplate' => 'run_scheduler'
		),
		
		$_scripts = array(
			'admin' => GSBACK
		),

		$_styles = array(
			'admin' => GSBACK
		),

		// white list directories that may be included in a backup
		$_whitelist = array(
			'data'
		);

	/**
	 *	Assign the correct plugin ID and call the parent constructor
	 *	@constructor
	 */
	public function __construct () {

		// by default, use today's date for scheduling
		$this->_defaults['schedule_start'] = date('m/j/y');

		// configure this plugin
		$this->_info = array(
			'id' =>             basename(__FILE__, '.php'),
			'name' =>           'Cloud Backup',
			'version' =>        '1.0',
			'author' =>         'RJ Zaworski <rj@rjzaworski.com>',
			'author_website' => 'http://rjzaworski.com/', 
			'description' =>    'Automated remote backups',
			'page_type' =>      'backups',
			'menu_callback' =>  'admin_view'
		);

		// initiate the plugin
		parent::__construct();

		// build provider, if one is selected
		if (isset($this->_settings['provider'])) {
			$class_name = 'CloudBackup\\Providers\\' . ucfirst($this->_settings['provider']);
			$this->provider = new $class_name($this);
		}

		// build backup strategy, if one is selected
		if (isset($this->_settings['archiver'])) {
			$class_name = 'CloudBackup\\Archivers\\' . ucfirst($this->_settings['archiver']);
			$this->archiver = new $class_name($this);
		}

		// build scheduler
		$this->scheduler = new CloudBackup\Scheduler($this);

		// build notifier
		$this->notifier = new CloudBackup\Notifier($this);

	}

	/**
	 *	Callback attached to `theme-sidebar`: create a menu entry
	 *	@callback
	 */
	public function admin_menu () {
		createSideMenu($this->_info['id'], 'Cloud Backups');
	}

	/**
	 *	Callback attached to `admin-pre-header`: check for OAuth request
	 *	and handle accordingly
	 *	@callback
	 */
	public function pre_route_handler () {

		// No alternate view has been supplied
		$this->_view = NULL;

		if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
			// FIXME: add AJAX request handling here
			exit;
		}

		if (isset($_GET['action'])) {

			$providers = $this->_scan_providers();

			// check for routes directed at provider
			if (strpos($_GET['action'], 'authorize_') === 0) {
				$provider_key = substr($_GET['action'], strlen('authorize_'));

				// set provider
				$this->setting('provider', $provider_key);

				// FIXME: need to update $this->provider whenever the setting
				//        is changed. This is NOT the way to do it:
				$class_name = 'CloudBackup\\Providers\\' . ucfirst($this->_settings['provider']);
				$this->provider = new $class_name($this);

				// check if provider is authorized
				if (isset($providers[$provider_key])) {
					$provider = $providers[$provider_key]['instance'];
					if (!$provider->is_authorized()) {
						$provider->authorize();
					}
				}
			} else if (strpos($_GET['action'], 'unauthorize_') === 0) {
				$provider_key = substr($_GET['action'], strlen('unauthorize_'));
				if (isset($providers[$provider_key])) {
					$provider = $providers[$provider_key]['instance'];
					if ($provider->is_authorized()) {
						$provider->unauthorize();
					}
				}
			}
		}
	}

	/**
	 *	Callback attached in `admin_menu`: admin settings
	 *	@callback
	 */
	public function admin_view () {

		$data = array(
			'archivers' => $this->_scan_archivers(),
			'providers' => $this->_scan_providers(),
			'frequencies' => $this->scheduler->frequencies()
		);

		$view = 'admin';

		if ($this->_view !== NULL) {
			// FIXME: this could be prettier.
			return $this->_load_view($this->_view[0], $this->_view[1]);
		}

		// force authorization before plugin may be used.
		if (!$this->is_authorized()) {
			// show "authorize me" page
			return $this->_load_view('authorize', $data);
		}

		if ($_SERVER['REQUEST_METHOD'] == 'POST') {

			$action = $_POST['_action'];
			$data['action'] = $action;

			// Obligatory CSRF check
			if (check_nonce($_POST['_nonce'], $action, $this->_info['id'])) {

				// Take action!
				switch ($action) {
				
				//
				//	Create a backup
				//
				case 'backup':

					if ($error = $this->backup()) {
						$data['error'] = $error;
					} else {
						$data['updated'] = 'Site backed up';
					}

					break;

				//
				//	Update/save settings
				//
				case 'settings':

					$this->_save($_POST);
					break;
				}

			} elseif ($action) {

				// Failed CSRF test
				$data['error'] = 'Request timed out';
			}
		}

		// show "admin" page
		$this->_load_view($view, $data);
	}

	/**
	 *	Callback: pass control to the scheduler to determine whether 
	 *	a backup needs to be run
	 *	@callback
	 */
	public function run_scheduler () {
		if ($this->_settings['schedule_enabled']) {
			$this->scheduler->check_schedule();
		}
	}

	/**
	 *	Create a backup using the current settings
	 *	Return an error message if an error takes place or NULL on success
	 *
	 *	@return	mixed
	 */
	public function backup () {

		$error = NULL;

		// get backup files
		$files = array(
			'data',
			'plugins',
			'theme',
			'gsconfig.php',
			'.htaccess'
		);

		$files = $this->archiver->run($files);

		// make sure backup includes *some* files
		if (!count($files)) {
			$error = 'No files marked for backup';
			return;
		}

		// try to backup files
		if (!$this->provider->backup($files)) {
			$error = 'Failed sending files to backup service';
		}

		// clean up any messes from the archiver
		$this->archiver->clean();

		return $error;
	}


	/**
	 *	Allow other plugins to provide a view during pre-routing
	 *	@param	string	$viewname to render
	 *	@param	array	(optional) $data to pass to view
	 */
	public function use_view ($viewname, $data = NULL) {

		if (!$data) {
			$data = array();
		}

		$this->_view = array($viewname, $data);
	}

	/**
	 *	Determine whether or not the app has been authorized
	 *	@return	boolean
	 */
	public function is_authorized () {
		return isset($this->provider) && $this->provider->is_authorized();
	}

	/**
	 *	Expose `GetSimplePlugin::_plugin_url()`
	 *	@param	string	anything to append to the url (e.g., 'js/myscript.js')
	 *	@return	string
	 */
	public function url ($append = '') {
		return $this->_plugin_url($append);
	}

	public function admin_url ($params = NULL) {

		global $SITEURL;

		$url = $SITEURL . 'admin/load.php?id=cloudbackup';

		return implode('&', array($url, http_build_query($params)));
	}
	
	/**
	 *	Scan a directory searching for class files
	 *
	 *	@protected
	 *	@param	string	$dir to search (relative to plugin_dir)
	 *	@return	array
	 */
	protected function _scan_klasses ($dir) {
		
		$klasses = array();
		$ns = ucfirst($dir);
		$dirp = opendir($this->_plugin_dir($dir));

		while (false !== ($fn = readdir($dirp))) {

			$fn = explode('.', $fn);
			
			if (count($fn) == 2 && $fn[0] !== 'base' && strtolower($fn[1]) == 'php') {

				$key = $fn[0];

				$class_name = "CloudBackup\\$ns\\" . ucfirst($key);
				$instance = new $class_name($this);
				$klasses[$key] = array_merge($instance->info(), array(
					'instance' => $instance
				));
			}
		}

		return $klasses;
	}

	/**
	 *	Scan the providers/ directory for providers
	 *
	 *	@protected
	 *	@return	array
	 */
	protected function _scan_providers () {
		return $this->_scan_klasses('providers');
	}

	/**
	 *	Scan the archivers/ directory for options
	 *
	 *	@protected
	 *	@return	array
	 */
	protected function _scan_archivers () {
		return $this->_scan_klasses('archivers');
	}
}

new CloudBackupPlugin();

endif;
