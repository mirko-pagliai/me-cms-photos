<?php

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
