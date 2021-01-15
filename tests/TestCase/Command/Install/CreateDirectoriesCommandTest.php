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
namespace MeCms\Banners\Test\TestCase\Command\Install;

use MeTools\TestSuite\ConsoleIntegrationTestTrait;
use MeTools\TestSuite\TestCase;

/**
 * CreateDirectoriesCommandTest class
 */
class CreateDirectoriesCommandTest extends TestCase
{
    use ConsoleIntegrationTestTrait;

    /**
     * Tests for `execute()` method
     * @test
     */
    public function testExecute()
    {
        $this->exec('me_tools.create_directories -v');
        $this->assertOutputContains('File or directory `tests/test_app/TestApp/webroot/img/photos` already exists');
    }
}
