<?php namespace CloudBackup;

require_once('test_helper.php');
require_once('notifier.php');

class NotifierTest extends \GSTestCase {

	public function setUp () {
		$mock = $this->getMock('CloudBackupPlugin');
		$this->model = new Notifier($mock);
	}

	public function test_settings () {
		$this->assertEquals(2+2, 4);
	}
}

