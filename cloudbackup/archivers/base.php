<?php namespace CloudBackup\Archivers;

require_once('base.php');

/**
 *	Base class for developing archiver strategies
 *	@author	RJ Zaworski (@rjzaworski)
 */
abstract class Base {

	protected 
		$_plugin,
		$_files = array();

	public function __construct ($plugin) {
		$this->_plugin = $plugin;
	}

	/**
	 *	Provides an overview of this storage
	 *	@return	array
	 */
	public function info () {
		return array(
			'title' => 'Archiver',
			'description' => 'An archiver whose info() function needs to be defined'
		);
	}
	
	/**
	 *	Clean files created by this archiver
	 */
	public function clean () {
		foreach ($this->_files as $file) {
			unlink($file);
		}
	}

	public function path_to ($dir) {
		return GSROOTPATH . $dir;
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
	abstract public function run ($files);

	/**
	 *	Get a temporary filename based on the name of the archiver
	 *	@return	string
	 *	@protected
	 */
	protected function _temp_file_name () {
		$tmpdir = sys_get_temp_dir();
		$archiver = explode('\\', get_class($this));
		$archiver = strtolower(array_pop($archiver));
		return tempnam($tmpdir, $archiver);
	}

	/**
	 *	Mark files for cleaning
	 *	@param	array
	 *	@protected
	 */
	protected function _clean_later ($files) {
		$this->_files = array_keys($files);
	}
}
