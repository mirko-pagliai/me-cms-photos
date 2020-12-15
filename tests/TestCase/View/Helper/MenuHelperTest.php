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

namespace MeCms\Photos\Test\TestCase\View\Helper;

use MeCms\TestSuite\MenuHelperTestCase;

/**
 * MenuHelperTest class
 */
class MenuHelperTest extends MenuHelperTestCase
{
    /**
     * Tests for `photos()` method
     * @test
     */
    public function testPhotos()
    {
        [$links,,, $handledControllers] = $this->Helper->photos();
        $this->assertNotEmpty($links);
        $this->assertEquals(['Photos', 'PhotosAlbums'], $handledControllers);
    }
}
