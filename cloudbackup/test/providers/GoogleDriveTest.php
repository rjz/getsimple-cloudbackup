<?php namespace CloudBackup\Providers;

require_once('test_helper.php');
require_once('providers/googledrive.php');

class GoogledriveTest extends \GSTestCase {

	public function setUp () {
		$mock = $this->getMock('BackupPlugin');
		$this->model = new Googledrive($mock);
	}

	public function test_run () {
		$this->assertEquals(2, 1 + 1); 
	}
}
