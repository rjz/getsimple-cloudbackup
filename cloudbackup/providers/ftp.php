<?php namespace CloudBackup\Providers;

require_once('base.php');

class Ftp extends Base {

	/**
	 *	Authorize with OAuth
	 */
	public function authorize () {

		if (!$this->is_authorized()) {

			$data = array();

			if ($_SERVER['REQUEST_METHOD'] == 'POST') {

				// FIXME: try login
				$data['error'] = 'FTP is not implemented yet.';
			}

			$this->_plugin->use_view('provider_ftp_authorize', $data);
		}
	}

	/**
	 *	Provides an overview of this adapter
	 *	@return	array
	 */
	public function info () {
		return array_merge(parent::info(), array(
			'title' => 'FTP',
			'description' => 'Store backups to your FTP server',
			'icon' => $this->_plugin->url('img/ftp.png')
		));
	}

	/**
	 *	Callback: unauthorize this adapter
	 */
	public function unauthorize () {
	}

	/**
	 *	Run the backup
	 */
	public function backup ($files) {
		return false;
	}

	public function is_authorized () {
		return false;
	}
}

