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

namespace MeCms\Photos\Test\TestCase\Model\Entity;

use MeCms\Photos\Model\Entity\Photo;
use MeCms\TestSuite\EntityTestCase;

/**
 * PhotosAlbumTest class
 */
class PhotosAlbumTest extends EntityTestCase
{
    /**
     * Called before every test method
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->Entity->set('id', 1)->set('slug', 'a-slug');
    }

    /**
     * Test for fields that cannot be mass assigned
     * @test
     */
    public function testNoAccessibleProperties(): void
    {
        $this->assertHasNoAccessibleProperty(['id', 'photo_count', 'modified']);
    }

    /**
     * Test for `_getPath()` method
     * @test
     */
    public function testPathGetMutator(): void
    {
        $this->assertNotEmpty($this->Entity->get('path'));
    }

    /**
     * Test for `_getPreview()` method
     * @test
     */
    public function testPreviewGetMutator(): void
    {
        $path = WWW_ROOT . 'img' . DS . 'photos' . DS . '1' . DS . 'photo.jpg';
        copy(WWW_ROOT . 'img' . DS . 'image.jpg', $path);
        $this->Entity->set('photos', [new Photo(['album_id' => 1, 'filename' => basename($path)])]);
        $this->assertEquals($this->Entity->get('preview'), $path);
        unlink($path);
    }

    /**
     * Test for `_getUrl()` method
     * @test
     */
    public function testUrl(): void
    {
        $this->assertStringEndsWith('/album/a-slug', $this->Entity->get('url'));
    }
}
