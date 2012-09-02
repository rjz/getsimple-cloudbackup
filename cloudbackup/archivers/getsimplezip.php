<?php namespace CloudBackup\Archivers;

require_once('base.php');

/**
 *	Archiver strategy riffing on GetSimple's built-in zip backups, relying
 *	heavily on archive code extracted from admin/zip.php.
 *
 *	Not tested. Not approved. Should probably be avoided for now.
 *
 *	@author	RJ Zaworski (@rjzaworski)
 */
class Getsimplezip extends Base {

	/**
	 *	Provides an overview of this storage
	 *	@return	array
	 */
	public function info () {
		return array(
			'title' => 'GetSimple Zip',
			'description' => 'GetSimple-style zip backups (Under development)'
		);
	}

	/**
	 *	Grab a list of files that need to be backed up
	 *
	 *	Accepts a list of files and/or directories that need to
	 *	be backed up, e.g.:
	 *
	 *		$files = array(
	 *			'data',
	 *			'theme',
	 *			'plugins'
	 *		);
	 *
	 *		$result = $archiver->run($files);
	 *
	 *	Returns an associated array keyed from the current file
	 *	to the name that should be used in storage, e.g.:
	 *
	 *	    array(
	 *	      '/tmp/tmpfile.tgz' => 'getsimple-backup-2012-08.tgz'
	 *	    )
	 *
	 *	@param	array	a list of files to include
	 *	@return	array	an array containing a list of files
	 */
	public function run ($files) {

		global $GSADMIN;
		set_time_limit (0);
		ini_set("memory_limit","800M");

		$tmpfile = $this->_temp_file_name();

		if (!class_exists ( 'ZipArchive' , false)) {
			include_once('../../admin/inc/ZipArchive.php');
		}

		if (class_exists ( 'ZipArchive' , false)) {

			$archiv = new \ZipArchive();
			$archiv->open($tmpfile, \ZipArchive::CREATE);
		
			$omit = array('.', '..', 'admin', 'backups');
			$this->_add_dir($archiv, '', $omit);

			// save and close
			$status = $archiv->close();

			if ($status) {
				return array(
					$tmpfile => 'backup-' . time() . '.tgz'
				);
			}

		}

		return array();
	}

	protected function _add_dir(&$archiv, $path, $omit = NULL) {
	
		if (!($handle = opendir(GSROOTPATH . $path))) {
			die('couldnt open dir');
		}

		if (!is_array($omit)) {
			$omit = array('.', '..');
		}

		while (false !== ($entry = readdir($handle))) {

			if (!in_array($entry, $omit)) {
				$entry = $path . $entry;
				$entry_path = GSROOTPATH . $entry;

				if (is_dir($entry_path)) {
					// skip hidden directories
					if ($entry[strlen($path)] != '.') {
						$archiv->addEmptyDir($entry);
						$this->_add_dir($archiv, $entry . '/');
					}
				} else {
					$archiv->addFile($entry_path, $entry);
				}
			}
		}

		closedir($handle);
	}
}
