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
use MeCms\TestSuite\ControllerTestCase;

/**
 * PhotosControllerTest class
 * @property \MeCms\Photos\Model\Table\PhotosTable $Table
 */
class PhotosControllerTest extends ControllerTestCase
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
     * Tests for `view()` method
     * @requires OS Linux
     * @uses \MeCms\Photos\Controller\PhotosController::view()
     * @test
     */
    public function testView(): void
    {
        $url = ['_name' => 'photo', 'test-album', '1'];

        $this->get($url);
        $this->assertResponseOkAndNotEmpty();
        $this->assertTemplate('Photos' . DS . 'view.php');
        $this->assertInstanceof(Photo::class, $this->viewVariable('photo'));
        $cache = Cache::read('view_' . md5('1'), $this->Table->getCacheName());
        $this->assertEquals($this->viewVariable('photo'), $cache->first());

        //Backward compatibility for URLs like `/photo/1`
        $this->get('/photo/1');
        $this->assertRedirect($url);

        //No existing photo
        $this->get('/photo/999');
        $this->assertResponseError();
    }

    /**
     * Tests for `preview()` method
     * @uses \MeCms\Photos\Controller\PhotosController::preview()
     * @test
     */
    public function testPreview(): void
    {
        $this->get(['_name' => 'photosPreview', 4]);
        $this->assertResponseOkAndNotEmpty();
        $this->assertTemplate('Photos' . DS . 'view.php');
        $this->assertInstanceof(Photo::class, $this->viewVariable('photo'));
    }
}
