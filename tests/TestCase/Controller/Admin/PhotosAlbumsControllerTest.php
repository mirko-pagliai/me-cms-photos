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

use MeCms\Photos\Model\Entity\PhotosAlbum;
use MeCms\TestSuite\Admin\ControllerTestCase;

/**
 * PhotosAlbumsControllerTest class
 * @property \MeCms\Photos\Model\Table\PhotosAlbumsTable $Table
 * @group admin-controller
 */
class PhotosAlbumsControllerTest extends ControllerTestCase
{
    /**
     * Fixtures
     * @var array<string>
     */
    public $fixtures = [
        'plugin.MeCms/Photos.PhotosAlbums',
    ];

    /**
     * @test
     * @uses \MeCms\Photos\Controller\Admin\PhotosAlbumsController::isAuthorized()
     */
    public function testIsAuthorized(): void
    {
        $this->assertAllGroupsAreAuthorized('index');
        $this->assertAllGroupsAreAuthorized('upload');

        $this->assertGroupIsAuthorized('delete', 'admin');
        $this->assertGroupIsAuthorized('delete', 'manager');
        $this->assertGroupIsNotAuthorized('delete', 'user');
    }

    /**
     * @test
     * @uses \MeCms\Photos\Controller\Admin\PhotosAlbumsController::index()
     */
    public function testIndex(): void
    {
        $this->get($this->url + ['action' => 'index']);
        $this->assertResponseOkAndNotEmpty();
        $this->assertTemplate('Admin' . DS . 'PhotosAlbums' . DS . 'index.php');
        $this->assertContainsOnlyInstancesOf(PhotosAlbum::class, $this->viewVariable('albums'));
    }

    /**
     * @test
     * @uses \MeCms\Photos\Controller\Admin\PhotosAlbumsController::add()
     */
    public function testAdd(): void
    {
        $url = $this->url + ['action' => 'add'];

        $this->get($url);
        $this->assertResponseOkAndNotEmpty();
        $this->assertTemplate('Admin' . DS . 'PhotosAlbums' . DS . 'add.php');
        $this->assertInstanceof(PhotosAlbum::class, $this->viewVariable('album'));

        //POST request. Data are valid
        $this->post($url, ['title' => 'new category', 'slug' => 'category-slug']);
        $this->assertRedirect(['action' => 'index']);
        $this->assertFlashMessage(I18N_OPERATION_OK);

        //POST request. Data are invalid
        $this->post($url, ['title' => 'aa']);
        $this->assertResponseOkAndNotEmpty();
        $this->assertResponseContains(I18N_OPERATION_NOT_OK);
        $this->assertInstanceof(PhotosAlbum::class, $this->viewVariable('album'));
    }

    /**
     * @test
     * @uses \MeCms\Photos\Controller\Admin\PhotosAlbumsController::edit()
     */
    public function testEdit(): void
    {
        $url = $this->url + ['action' => 'edit', 1];

        $this->get($url);
        $this->assertResponseOkAndNotEmpty();
        $this->assertTemplate('Admin' . DS . 'PhotosAlbums' . DS . 'edit.php');
        $this->assertInstanceof(PhotosAlbum::class, $this->viewVariable('album'));

        //POST request. Data are valid
        $this->post($url, ['title' => 'another title']);
        $this->assertRedirect(['action' => 'index']);
        $this->assertFlashMessage(I18N_OPERATION_OK);

        //POST request. Data are invalid
        $this->post($url, ['title' => 'aa']);
        $this->assertResponseOkAndNotEmpty();
        $this->assertResponseContains(I18N_OPERATION_NOT_OK);
        $this->assertInstanceof(PhotosAlbum::class, $this->viewVariable('album'));
    }

    /**
     * @test
     * @uses \MeCms\Photos\Controller\Admin\PhotosAlbumsController::delete()
     */
    public function testDelete(): void
    {
        //POST request. This album has no photos
        $this->post($this->url + ['action' => 'delete', 3]);
        $this->assertRedirect(['action' => 'index']);
        $this->assertFlashMessage(I18N_OPERATION_OK);
        $this->assertTrue($this->Table->findById(3)->all()->isEmpty());

        //POST request. This album has some photos, so it cannot be deleted
        $this->post($this->url + ['action' => 'delete', 1]);
        $this->assertRedirect(['action' => 'index']);
        $this->assertFlashMessage(I18N_BEFORE_DELETE);
        $this->assertFalse($this->Table->findById(1)->all()->isEmpty());
    }
}
