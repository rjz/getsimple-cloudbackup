<?php namespace CloudBackup;

/**
 *	Scheduler module for Cloud Backup plugin
 *	@author	RJ Zaworski (@rjzaworski)
 */
class Scheduler {

	protected
		$_plugin;

	/**
	 *	@constructer
	 */
	public function __construct ($plugin) {

	 	// Store a reference to the parent plugin
		$this->_plugin = $plugin;

		// Provide the next scheduled backup time
		$this->_plugin->setting('schedule_next', $this->next_scheduled());
	}

	/**
	 *	Helper: return a list of available backup frequencies
	 *	@return	array
	 */
	public function frequencies	() {
		return array(
			'daily' => 24 * 3600,
			'weekly' => 24 * 7 * 3600
		);
	}

	/**
	 *	Determine the last time that the backup should have run
	 *	Returns an epoch timestamp
	 *	@return	number
	 */
	public function last_scheduled () {

		$freq = $this->_plugin->setting('schedule_frequency');
		$start = $this->_plugin->setting('schedule_start');
		$start = strtotime($start);

		if ($freq < 3600) {
			return 0;
		}
		
		$now = time();

		return floor(($now - $start) / $freq) + $start;
	}

	/**
	 *	Determine the last time that the next backup will run
	 *	Returns an epoch timestamp
	 *	@return	number
	 */
	public function next_scheduled () {
		$freq = $this->_plugin->setting('schedule_frequency');
		return $this->last_scheduled() + $freq;
	}

	/**
	 *	Callback: check to see if a backup should be created.
	 *	If so, pass control back to the plugin's `backup()` 
	 *	function
	 *
	 *	@callback
	 */
	public function check_schedule () {

		$scheduled = $this->last_scheduled();

		if ($this->_plugin->setting('schedule_lastrun') < $scheduled) {
			// run_backup
			$this->_plugin->backup();
			$this->_plugin->setting('schedule_lastrun', $now);
		}
	}
}

