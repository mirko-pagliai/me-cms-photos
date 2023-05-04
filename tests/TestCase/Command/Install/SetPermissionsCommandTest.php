<?php
/** @noinspection PhpUnhandledExceptionInspection */
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
namespace MeCms\Photos\Test\TestCase\Command\Install;

use Cake\Core\Configure;
use MeTools\TestSuite\CommandTestCase;
use Tools\Filesystem;

/**
 * SetPermissionsCommandTest class
 */
class SetPermissionsCommandTest extends CommandTestCase
{
    /**
     * @test
     * @uses \MeTools\Command\Install\SetPermissionsCommand::execute()
     */
    public function testExecute(): void
    {
        $this->exec('me_cms.set_permissions -v');
        $this->assertExitSuccess();
        $this->assertErrorEmpty();
        $expectedDirs = Configure::read('MeCms/Photos.WritableDirs');
        $this->assertIsArrayNotEmpty($expectedDirs);
        foreach ($expectedDirs as $expectedDir) {
            $this->assertOutputContains('Set permissions on `' . Filesystem::instance()->rtr($expectedDir) . '`');
        }
    }
}
