<?php namespace CloudBackup\Archivers;

require_once('test_helper.php');
require_once('archivers/tarball.php');

class TarballTest extends \GSTestCase {

	public function setUp () {
		$mock = $this->getMock('BackupPlugin');
		$this->model = new Tarball($mock);
	}

	public function tearDown () {
		$this->model->clean();
	}

	public function test_run () {
		$files = array('backup.php');
		$files = $this->model->run($files);
		$this->assertEquals(1, count($files)); 
	}
}
