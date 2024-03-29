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

namespace MeCms\Photos\Test\TestCase\Controller;

use Cake\Cache\Cache;
use MeCms\Photos\Model\Entity\Photo;
use MeCms\Photos\Model\Entity\PhotosAlbum;
use MeCms\TestSuite\ControllerTestCase;

/**
 * PhotosAlbumsControllerTest class
 * @property \MeCms\Photos\Controller\PhotosAlbumsController $_controller
 * @property \MeCms\Photos\Model\Table\PhotosAlbumsTable $Table
 */
class PhotosAlbumsControllerTest extends ControllerTestCase
{
    /**
     * Fixtures
     * @var array<string>
     */
    public $fixtures = [
        'plugin.MeCms/Photos.Photos',
        'plugin.MeCms/Photos.PhotosAlbums',
    ];

    /**
     * Tests for `index()` method
     * @test
     */
    public function testIndex(): void
    {
        $this->get(['_name' => 'albums']);
        $this->assertResponseOkAndNotEmpty();
        $this->assertTemplate('PhotosAlbums' . DS . 'index.php');
        $this->assertContainsOnlyInstancesOf(PhotosAlbum::class, $this->viewVariable('albums'));
        foreach ($this->viewVariable('albums') as $album) {
            $this->assertContainsOnlyInstancesOf(Photo::class, $album->get('photos'));
        }

        $cache = sprintf('albums_limit_%s_page_%s', getConfigOrFail('MeCms/Photos.default.albums'), 1);
        [$albumsFromCache, $pagingFromCache] = array_values(Cache::readMany(
            [$cache, sprintf('%s_paging', $cache)],
            $this->Table->getCacheName()
        ));
        $this->assertEquals($this->viewVariable('albums')->toArray(), $albumsFromCache->toArray());
        $this->assertNotEmpty($pagingFromCache['PhotosAlbums']);

        //GET request again. Now the data is in cache
        $this->get(['_name' => 'albums']);
        $this->assertResponseOkAndNotEmpty();
        $this->assertNotEmpty($this->_controller->getPaging()['PhotosAlbums']);

        //Deletes all albums, except the first one. Now it redirects to the first album
        Cache::clear($this->Table->getCacheName());
        $this->Table->deleteAll(['id >' => 1]);
        $this->get(['_name' => 'albums']);
        $this->assertRedirect(['_name' => 'album', 'test-album']);
    }

    /**
     * Tests for `view()` method
     * @requires OS Linux
     * @test
     */
    public function testView(): void
    {
        $url = ['_name' => 'album', 'test-album'];

        $this->get($url);
        $this->assertResponseOkAndNotEmpty();
        $this->assertTemplate('PhotosAlbums' . DS . 'view.php');
        $this->assertInstanceof(PhotosAlbum::class, $this->viewVariable('album'));
        $this->assertContainsOnlyInstancesOf(Photo::class, $this->viewVariable('photos'));
        $cache = Cache::read('album_' . md5('test-album'), $this->Table->getCacheName());
        $this->assertEquals($this->viewVariable('album'), $cache->first());

        //Sets the cache name
        $cache = sprintf('album_%s_limit_%s_page_%s', md5('test-album'), getConfigOrFail('MeCms/Photos.default.photos'), 1);
        [$photosFromCache, $pagingFromCache] = array_values(Cache::readMany(
            [$cache, sprintf('%s_paging', $cache)],
            $this->Table->getCacheName()
        ));

        $this->assertEquals($this->viewVariable('photos')->toArray(), $photosFromCache->toArray());
        $this->assertNotEmpty($pagingFromCache['Photos']);

        //GET request again. Now the data is in cache
        $this->get($url);
        $this->assertResponseOkAndNotEmpty();
        $this->assertNotEmpty($this->_controller->getPaging()['Photos']);

        //GET request with query string
        $this->get($url + ['?' => ['q' => 'test-album']]);
        $this->assertRedirect($url);
    }
}
