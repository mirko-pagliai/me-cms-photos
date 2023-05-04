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
 * PhotosMenuHelperTest class
 */
class PhotosMenuHelperTest extends MenuHelperTestCase
{
    /**
     * @test
     * @uses \MeCms\Photos\View\Helper\PhotosMenuHelper::getLinks()
     */
    public function testGetLinks(): void
    {
        $expected = [
            '<a href="/me-cms-photos/admin/photos" title="List photos">List photos</a>',
            '<a href="/me-cms-photos/admin/photos/upload" title="Upload photos">Upload photos</a>',
            '<a href="/me-cms-photos/admin/photos-albums" title="List albums">List albums</a>',
            '<a href="/me-cms-photos/admin/photos-albums/add" title="Add album">Add album</a>',
        ];
        $this->assertSame($expected, $this->getLinksAsHtml());

        foreach (['manager', 'admin'] as $name) {
            $this->setIdentity(['group' => compact('name')]);
            $this->assertSame($expected, $this->getLinksAsHtml());
        }
    }
}
