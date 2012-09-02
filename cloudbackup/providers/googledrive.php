<?php namespace CloudBackup\Providers;
/**
 *	Google Drive backup provider for GetSimple Cloud Backup plugin
 *
 *	@see	https://developers.google.com/drive/quickstart
 *	@author	@rjzaworski
 */

// include provider base class
require_once('base.php');

// include google client libraries
require_once('vendor/google-api-php-client/src/Google_Client.php');
require_once('vendor/google-api-php-client/src/contrib/Google_DriveService.php');
require_once('vendor/google-api-php-client/src/contrib/Google_Oauth2Service.php');


/**
 *	Backups via Google Drive
 *	@author	RJ Zaworski (@rjzaworski)
 */
class Googledrive extends Base {

	const client_id     = 'XXXXXXXXXXXXX';
	const client_secret = 'XXXXXXXXXXXXX';

	const token_setting = 'googledrive_access_token';

	protected
		$_client = NULL,
		$_service = NULL;

	public function __construct ($plugin) {

		parent::__construct($plugin);

		// set up client
		$client = new \Google_Client();
		$client->setClientId(self::client_id);
		$client->setClientSecret(self::client_secret);
		$client->setRedirectUri('urn:ietf:wg:oauth:2.0:oob');
		$client->setScopes(array('https://www.googleapis.com/auth/drive'));

		$this->_client = $client;

		// set up service
		$this->_service = new \Google_DriveService($client);
	}

	/**
	 *	Authorize with OAuth
	 */
	public function authorize () {

		if ($_SERVER['REQUEST_METHOD'] == 'POST') {

			// exchange an auth code for OAuth credentials
			$token = $_POST['auth_token'];

			try {

				$_GET['code'] = $token;

				if ($access_token = $this->_client->authenticate()) {
					$this->_plugin->setting(self::token_setting, $access_token);
					// redirect to admin page
					header('Location: ' . $this->_plugin->admin_url());
					exit;
				}

				// this shouldn't happen, but:
				die('failed retrieving OAuth credentials');

			} catch (\Google_AuthException $e) {
				die($e->getMessage());
			}

		} else {

			// construct auth url
			$data = array(
				'auth_url' => $this->_client->createAuthUrl()
			);

			$this->_plugin->use_view('provider_google_authorize', $data);
		}

	}

	/**
	 *	Provides an overview of this adapter
	 *	@return	array
	 */
	public function info () {
		return array_merge(parent::info(), array(
			'title' => 'Google Drive',
			'description' => 'Store backups to your Google Drive',
			'icon' => $this->_plugin->url('img/drive.png')
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

		$fail = false;

  		$this->_client->setAccessToken($this->_plugin->setting(self::token_setting));

		foreach ($files as $src => $filename) {

			$file = new \Google_DriveFile();
			$file->setTitle($filename);
			$file->setDescription('GetSimple site backup');

			$data = file_get_contents($src);

			try {
				$created_file = $this->_service->files->insert($file, array('data' => $data));
			} catch (Exception $e) {
				die($e->getMessage());
				break;
			}
		}

		return !$fail;
	}

	public function is_authorized () {
		return $this->_plugin->setting(self::token_setting) !== NULL;
	}
}
