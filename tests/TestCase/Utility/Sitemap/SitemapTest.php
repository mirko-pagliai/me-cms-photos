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

namespace MeCms\Photos\Test\TestCase\Utility\Sitemap;

use Cake\Cache\Cache;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use MeCms\Photos\Utility\Sitemap\Sitemap;
use MeCms\TestSuite\TestCase;

/**
 * SitemapTest class
 */
class SitemapTest extends TestCase
{
    /**
     * Does not automatically load fixtures
     * @var bool
     */
    public $autoFixtures = false;

    /**
     * Fixtures
     * @var array
     */
    public $fixtures = [
        'plugin.MeCms/Photos.Photos',
        'plugin.MeCms/Photos.PhotosAlbums',
    ];

    /**
     * Called before every test method
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        Cache::clearAll();
    }

    /**
     * Test for `photos()` method
     * @test
     */
    public function testPhotos()
    {
        $this->loadFixtures('Photos', 'PhotosAlbums');
        $table = TableRegistry::getTableLocator()->get('MeCms/Photos.PhotosAlbums');

        //Photos are disabled for the sitemap
        Configure::write('MeCms/Photos.sitemap.photos', false);
        $this->assertEmpty(Sitemap::photos());
        Configure::write('MeCms/Photos.sitemap.photos', true);

        $expected = [
            [
                'loc' => 'http://localhost/albums',
                'lastmod' => '2016-12-28T10:40:42+00:00',
                'priority' => '0.5',
            ],
            [
                'loc' => 'http://localhost/album/another-album-test',
                'lastmod' => '2016-12-28T10:39:42+00:00',
                'priority' => '0.5',
            ],
            [
                'loc' => 'http://localhost/photo/another-album-test/2',
                'lastmod' => '2016-12-28T10:39:42+00:00',
                'priority' => '0.5',
            ],
            [
                'loc' => 'http://localhost/album/test-album',
                'lastmod' => '2016-12-28T10:40:42+00:00',
                'priority' => '0.5',
            ],
            [
                'loc' => 'http://localhost/photo/test-album/3',
                'lastmod' => '2016-12-28T10:40:42+00:00',
                'priority' => '0.5',
            ],
            [
                'loc' => 'http://localhost/photo/test-album/1',
                'lastmod' => '2016-12-28T10:38:42+00:00',
                'priority' => '0.5',
            ],
        ];
        $this->assertEquals($expected, Sitemap::photos());
        $this->assertEquals($expected, Cache::read('sitemap', $table->getCacheName()));

        //Deletes all records
        $table->deleteAll(['id IS NOT' => null]);
        $this->assertEmpty(Sitemap::photos());
    }
}
