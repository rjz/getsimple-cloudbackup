<?php namespace CloudBackup\Providers;

/**
 *	Base class for developinp backup provider strategies
 *	@author	RJ Zaworski (@rjzaworski)
 */
abstract class Base {

	protected $_plugin;

	public function __construct ($plugin) {
		$this->_plugin = $plugin;
	}

	/**
	 *	Provides an overview of this adapter
	 *	@return	array
	 */
	public function info () {
		return array(
			'title' => 'Adapter',
			'description' => 'An adapter whose info function needs to be defined',
			'icon' => $this->_plugin->url('img/dropbox.png')
		);
	}

	abstract public function authorize ();
	abstract public function is_authorized ();
	abstract public function backup ($files);
	abstract public function unauthorize ();
}

