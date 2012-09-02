<?php namespace CloudBackup\Archivers;

require_once('test_helper.php');
require_once('archivers/getsimplezip.php');

function rrmdir($dir) {
	foreach(glob($dir . '/*') as $file) {
		if(is_dir($file))
			rrmdir($file);
		else
			unlink($file);
	}
	rmdir($dir);
}

class GetSimpleZipTest extends \GSTestCase {

	public function setUp () {
		$mock = $this->getMock('BackupPlugin');
		$this->model = new GetSimpleZip($mock);
	}

	public function tearDown () {
		$this->model->clean();
	}

	public function test_run () {
		$files = array('backup.php');
#		$files = $this->model->run($files);
#		$this->assertEquals(1, count($files)); 
	}
}
