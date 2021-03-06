<?php
/**
 * Copyright (c) 2014 Georg Ehrke <georg@owncloud.com>
 * This file is licensed under the Affero General Public License version 3 or
 * later.
 * See the COPYING-README file.
 */

namespace Test;


use OC\Installer;

class InstallerTest extends TestCase {

	private static $appid = 'testapp';

	protected function setUp() {
		parent::setUp();

		Installer::removeApp(self::$appid);
	}

	protected function tearDown() {
		Installer::removeApp(self::$appid);

		parent::tearDown();
	}

	public function testInstallApp() {
		$pathOfTestApp  = __DIR__;
		$pathOfTestApp .= '/../data/';
		$pathOfTestApp .= 'testapp.zip';

		$tmp = \OC::$server->getTempManager()->getTemporaryFile('.zip');
		\OC_Helper::copyr($pathOfTestApp, $tmp);

		$data = [
			'path' => $tmp,
			'source' => 'path',
			'appdata' => [
				'id' => 'Bar',
				'level' => 100,
			]
		];

		Installer::installApp($data);
		$isInstalled = Installer::isInstalled(self::$appid);

		$this->assertTrue($isInstalled);
	}

	public function testUpdateApp() {
		$pathOfOldTestApp  = __DIR__;
		$pathOfOldTestApp .= '/../data/';
		$pathOfOldTestApp .= 'testapp.zip';

		$oldTmp = \OC::$server->getTempManager()->getTemporaryFile('.zip');
		\OC_Helper::copyr($pathOfOldTestApp, $oldTmp);

		$oldData = [
			'path' => $oldTmp,
			'source' => 'path',
			'appdata' => [
				'id' => 'Bar',
				'level' => 100,
			]
		];

		$pathOfNewTestApp  = __DIR__;
		$pathOfNewTestApp .= '/../data/';
		$pathOfNewTestApp .= 'testapp2.zip';

		$newTmp = \OC::$server->getTempManager()->getTemporaryFile('.zip');
		\OC_Helper::copyr($pathOfNewTestApp, $newTmp);

		$newData = [
			'path' => $newTmp,
			'source' => 'path',
			'appdata' => [
				'id' => 'Bar',
				'level' => 100,
			]
		];

		Installer::installApp($oldData);
		$oldVersionNumber = \OC_App::getAppVersion(self::$appid);

		Installer::updateApp($newData);
		$newVersionNumber = \OC_App::getAppVersion(self::$appid);

		$this->assertNotEquals($oldVersionNumber, $newVersionNumber);
	}
}
