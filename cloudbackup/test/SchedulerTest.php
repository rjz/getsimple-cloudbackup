<?php namespace CloudBackup;

require_once('test_helper.php');
require_once('scheduler.php');

class SchedulerTest extends \GSTestCase {

	public function setUp () {
		$mock = $this->getMock('CloudBackupPlugin');
		$this->model = new Scheduler($mock);
	}

	public function test_settings () {
		$this->assertEquals(2+2, 4);
	}
}

