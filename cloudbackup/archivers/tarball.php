<?php namespace CloudBackup\Archivers;

require_once('base.php');

/**
 *	Tarball archiver strategy
 *
 *	Uses system's `tar` utility to archive a Getsimple website, 
 *	effectively returning the archive created by:
 *
 *		$ tar -czvf [files_to_backup]
 *
 *	@author	RJ Zaworski (@rjzaworski)
 */
class Tarball extends Base {

	/**
	 *	Provides an overview of this storage
	 *	@return	array
	 */
	public function info () {
		return array(
			'title' => 'Tarball',
			'description' => 'Collects backup into a single compressed tarball (.tgz)'
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

		$tmp = $this->_temp_file_name();

		$files = implode(' ', $files);

		$command = "cd .. && tar -czvf $tmp $files";
		exec($command, $output, $retval);

		if ($retval == 0) {

			$archives = array();
			$archives[$tmp] = 'backup-' . time() . '.tgz';

			$this->_clean_later($archives);
			return $archives;
		}

		return array();
	}
}

