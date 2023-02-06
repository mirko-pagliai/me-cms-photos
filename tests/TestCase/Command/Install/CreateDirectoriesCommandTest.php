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

use MeTools\TestSuite\CommandTestCase;
use Tools\Filesystem;

/**
 * CreateDirectoriesCommandTest class
 */
class CreateDirectoriesCommandTest extends CommandTestCase
{
    /**
     * @uses \MeTools\Command\Install\CreateDirectoriesCommand::execute()
     * @test
     */
    public function testExecute(): void
    {
        if (!file_exists(PHOTOS)) {
            mkdir(PHOTOS, 0755, true);
        }
        $this->exec('me_tools.create_directories -v');
        $this->assertOutputContains('File or directory `' . Filesystem::instance()->rtr(PHOTOS) . '` already exists');
    }
}
