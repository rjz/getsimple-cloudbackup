<?php namespace CloudBackup\Providers;

require_once('base.php');

// Register an autoload function for vendor/Dropbox classes
spl_autoload_register(function ($classname) {
	if (strpos($classname, 'Dropbox') === 0) {
		$filename = str_replace('\\', '/', $classname) . '.php';
		include_once('vendor/Dropbox/' . $filename);
	}
});

/**
 *	Backups via Dropbox
 *	@author	RJ Zaworski (@rjzaworski)
 */
class Dropbox extends Base {

	const consumer_key    = 'XXXXXXXXXXX';
	const consumer_secret = 'XXXXXXXXXXX';

	const encrypter_secret = 'XXXXXXXXXXYXXXXXXXXXXXXXXXXXXXXX';

	const token_setting = 'dropbox_access_token';

	/**
	 *	Authorize with OAuth
	 */
	public function authorize () {

		$OAuth = $this->_getOAuth();

		// if authorization is completed, store the access token 
		// provided by dropbox for future requests
		if ($token = $OAuth->storage->get('access_token')) {
			$this->_plugin->setting(self::token_setting, $token);
			$this->_plugin->setting('provider', 'dropbox');
		}
	}

	/**
	 *	Provides an overview of this adapter
	 *	@return	array
	 */
	public function info () {
		return array_merge(parent::info(), array(
			'title' => 'Dropbox',
			'description' => 'Store backups to your Dropbox account',
			'icon' => $this->_plugin->url('img/dropbox.png')
		));
	}

	/**
	 *	Callback: unauthorize this adapter
	 */
	public function unauthorize () {
		$this->_plugin->setting(self::token_setting, NULL);
	}

	/**
	 *	Run the backup
	 */
	public function backup ($files) {

		$dropbox = new \Dropbox\API($this->_getOAuth());
		$fail = false;

		foreach ($files as $src => $filename) {
			$result = $dropbox->putFile($src, $filename);

			if ($result['code'] != 200) {
				$fail = true;
				break;
			}
		}

		return !$fail;
	}

	public function is_authorized () {
		return $this->_plugin->setting(self::token_setting) !== NULL;
	}

	/**
	 *	Hack: wrap the Dropbox library's OAuth object up to:
	 *	(1) prevent it from running on its own
	 *	(2) spoof OAuth tokens from the plugin's own data store
	 *
	 *	@return	Dropbox\OAuth\Consumer\ConsumerAbstract
	 */
	protected function _getOAuth () {

		$callback = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
		$encrypter = new \Dropbox\OAuth\Storage\Encrypter(self::encrypter_secret);
		$storage = new \Dropbox\OAuth\Storage\Session($encrypter);

		// spoof storage using token stored with plugin
		if ($this->is_authorized()) {
			$storage->set($this->_plugin->setting(self::token_setting), 'access_token');
		}

		$OAuth = new \Dropbox\OAuth\Consumer\Curl(self::consumer_key, self::consumer_secret, $storage, $callback);

		return $OAuth;
	}
}
