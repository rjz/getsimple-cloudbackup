<?php namespace CloudBackup\Providers;

require_once('test_helper.php');
require_once('providers/ftp.php');

class FtpTest extends \GSTestCase {

	public function setUp () {
		$mock = $this->getMock('BackupPlugin');
		$this->model = new Dropbox($mock);
	}

	public function test_run () {
		$this->assertEquals(2, 1 + 1); 
	}
}
