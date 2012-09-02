<?php namespace CloudBackup;

/**
 *	Notifier module for Cloud Backup plugin
 *	@author	RJ Zaworski (@rjzaworski)
 */
class Notifier {

	protected
		$_plugin;

	public function __construct ($plugin) {
		$this->_plugin = $plugin;
		$plugin->setting('schedule_enabled');
	}
}

