<?php

require_once('test_helper.php');

class CloudBackupPluginTest extends GSTestCase {

	public function setUp () {
		$this->model = new CloudBackupPlugin;
	}

	public function test_settings () {
		$this->assertEquals(2+2, 4);
	}
}
