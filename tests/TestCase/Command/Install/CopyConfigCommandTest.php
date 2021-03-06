<?php
declare(strict_types=1);

/**
 * This file is part of me-cms-photos.
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright   Copyright (c) Mirko Pagliai
 * @link        https://github.com/mirko-pagliai/me-cms-photos
 * @license     https://opensource.org/licenses/mit-license.php MIT License
 */
namespace MeCms\Photos\Test\TestCase\Controller\Admin;

use MeTools\TestSuite\ConsoleIntegrationTestTrait;
use MeTools\TestSuite\TestCase;
use Tools\Filesystem;

/**
 * SetPermissionsCommandTest class
 */
class CopyConfigCommandTest extends TestCase
{
    use ConsoleIntegrationTestTrait;

    /**
     * Tests for `execute()` method
     * @test
     */
    public function testExecute()
    {
        $this->exec('me_cms.copy_config -v');
        $this->assertOutputContains('File or directory `' . (new Filesystem())->rtr(TEST_APP . 'TestApp' . DS . 'config' . DS . 'me_cms_photos.php') . '` already exists');
    }
}
