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

namespace MeCms\Photos\Test\TestCase\Controller\Admin;

use Cake\Controller\Controller;
use Cake\Event\EventInterface;
use Cake\Http\Response;
use Laminas\Diactoros\UploadedFile;
use MeCms\Photos\Model\Entity\Photo;
use MeCms\TestSuite\Admin\ControllerTestCase;

/**
 * PhotosControllerTest class
 * @property \MeCms\Photos\Model\Table\PhotosTable $Table
 * @group admin-controller
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
     * Adds additional event spies to the controller/view event manager
     * @param \Cake\Event\EventInterface $event A dispatcher event
     * @param \Cake\Controller\Controller|null $controller Controller instance
     * @return void
     */
    public function controllerSpy(EventInterface $event, ?Controller $controller = null): void
    {
        parent::controllerSpy($event, $controller);

        //Only for the `testUploadErrors()` method, it mocks the table
        if ($this->getName() == 'testUploadErrors') {
            $alias = $this->Table->getRegistryAlias();
            /** @var \MeCms\Photos\Model\Table\PhotosTable&\PHPUnit\Framework\MockObject\MockObject $PhotosTable */
            $PhotosTable = $this->getMockForModel($alias, ['save']);
            $this->_controller->getTableLocator()->set($alias, $PhotosTable);
        }
    }

    /**
     * @test
     * @uses \MeCms\Photos\Controller\Admin\PhotosController::beforeFilter()
     */
    public function testBeforeFilter(): void
    {
        $this->Table->Albums->deleteAll(['id IS NOT' => null]);
        $this->get($this->url + ['action' => 'index']);
        $this->assertRedirect(['controller' => 'PhotosAlbums', 'action' => 'index']);

        /**
         * This tests that the parent `beforeFilter()` method is being executed correctly
         */
        /** @var \MeCms\Photos\Controller\Admin\PhotosController&\PHPUnit\Framework\MockObject\MockObject $Controller */
        $Controller = $this->getMockBuilder($this->originClassName)->onlyMethods(['initialize', 'isSpammer'])->getMock();
        $Controller->expects($this->once())->method('isSpammer')->willReturn(true);
        $result = $Controller->dispatchEvent('Controller.initialize')->getResult();
        $this->assertInstanceOf(Response::class, $result);
    }

    /**
     * @test
     * @uses \MeCms\Photos\Controller\Admin\PhotosController::index()
     */
    public function testIndex(): void
    {
        $this->get($this->url + ['action' => 'index']);
        $this->assertResponseOkAndNotEmpty();
        $this->assertTemplate('Admin' . DS . 'Photos' . DS . 'index.php');
        $this->assertContainsOnlyInstancesOf(Photo::class, $this->viewVariable('photos'));
        $this->assertCookieIsEmpty('render-photos');
    }

    /**
     * Tests for `index()` method, render as `grid`
     * @test
     * @uses \MeCms\Photos\Controller\Admin\PhotosController::index()
     */
    public function testIndexAsGrid(): void
    {
        $this->get($this->url + ['action' => 'index', '?' => ['render' => 'grid']]);
        $this->assertResponseOkAndNotEmpty();
        $this->assertTemplate('Admin' . DS . 'Photos' . DS . 'index_as_grid.php');
        $this->assertContainsOnlyInstancesOf(Photo::class, $this->viewVariable('photos'));
        $this->assertCookie('grid', 'render-photos');

        //With cookie
        $this->cookie('render-photos', 'grid');
        $this->get($this->url + ['action' => 'index']);
        $this->assertResponseOkAndNotEmpty();
        $this->assertTemplate('Admin' . DS . 'Photos' . DS . 'index_as_grid.php');
        $this->assertCookie('grid', 'render-photos');
    }

    /**
     * @test
     * @uses \MeCms\Photos\Controller\Admin\PhotosController::upload()
     */
    public function testUpload(): void
    {
        $url = $this->url + ['action' => 'upload'];

        $this->get($url);
        $this->assertResponseOkAndNotEmpty();
        $this->assertTemplate('Admin' . DS . 'Photos' . DS . 'upload.php');

        $url += ['?' => ['album' => 1]];
        $this->get($url);
        $this->assertResponseOkAndNotEmpty();
        $this->assertTemplate('Admin' . DS . 'Photos' . DS . 'upload.php');

        //POST request. This works
        $file = $this->createImageToUpload();
        $this->post($url + ['_ext' => 'json'], compact('file'));
        $this->assertResponseOk();
        $record = $this->Table->find()->all()->last();
        $this->assertSame(1, $record->get('album_id'));
        $this->assertSame($file->getClientFilename(), $record->get('filename'));
        $this->assertFileExists($record->get('path'));
        $this->Table->delete($record);

        //POST request. This works without the parent ID on query string, because there is only one record from the associated table
        $this->Table->Albums->deleteAll(['id >' => 1]);
        $this->post($this->url + ['action' => 'upload', '_ext' => 'json'], compact('file'));
        $this->assertRedirect($url);
    }

    /**
     * Tests for `upload()` method, with some errors.
     *
     * The table `save()` method returns `false` for this test. See the `controllerSpy()` method.
     * @uses \MeCms\Photos\Controller\Admin\PhotosController::upload()
     * @test
     */
    public function testUploadErrors(): void
    {
        $url = $this->url + ['action' => 'upload', '_ext' => 'json'];

        //Missing ID on the query string
        $this->post($url, ['file' => true]);
        $this->assertResponseFailure();
        $this->assertResponseContains(I18N_MISSING_ID);

        $url += ['?' => [substr('album_id', 0, -3) => 1]];

        $this->post($url, ['file' => $this->createImageToUpload()]);
        $this->assertResponseFailure();
        $this->assertResponseEquals('{"error":"' . I18N_OPERATION_NOT_OK . '"}');
        $this->assertTemplate('Admin' . DS . 'Photos' . DS . 'json' . DS . 'upload.php');

        //Error during the upload
        $file = new UploadedFile('', 0, UPLOAD_ERR_NO_FILE);
        $this->post($url, compact('file'));
        $this->assertResponseFailure();
        $this->assertResponseEquals('{"error":"No file was uploaded"}');
        $this->assertTemplate('Admin' . DS . 'Photos' . DS . 'json' . DS . 'upload.php');

        //Error on entity
        $file = new UploadedFile(fopen('php://memory', 'r+') ?: '', 0, UPLOAD_ERR_OK);
        $this->post($url, compact('file'));
        $this->assertResponseFailure();
        $this->assertResponseEquals('{"error":"The mimetype  is not accepted"}');
        $this->assertTemplate('Admin' . DS . 'Photos' . DS . 'json' . DS . 'upload.php');
    }

    /**
     * @test
     * @uses \MeCms\Photos\Controller\Admin\PhotosController::edit()
     */
    public function testEdit(): void
    {
        $url = $this->url + ['action' => 'edit', 1];

        $this->get($url);
        $this->assertResponseOkAndNotEmpty();
        $this->assertTemplate('Admin' . DS . 'Photos' . DS . 'edit.php');
        $this->assertInstanceof(Photo::class, $this->viewVariable('photo'));

        //POST request. Data are valid
        $this->post($url, ['description' => 'New description for first record']);
        $this->assertRedirect(['action' => 'index']);
        $this->assertFlashMessage(I18N_OPERATION_OK);

        //POST request. Data are invalid
        $this->post($url, ['album_id' => 'invalid']);
        $this->assertResponseOkAndNotEmpty();
        $this->assertResponseContains(I18N_OPERATION_NOT_OK);
        $this->assertInstanceof(Photo::class, $this->viewVariable('photo'));
    }

    /**
     * @test
     * @uses \MeCms\Photos\Controller\Admin\PhotosController::download()
     */
    public function testDownload(): void
    {
        $this->get($this->url + ['action' => 'download', 1]);
        $this->assertResponseOkAndNotEmpty();
        $this->assertFileResponse($this->Table->get(1)->get('path'));
    }

    /**
     * @test
     * @uses \MeCms\Photos\Controller\Admin\PhotosController::delete()
     */
    public function testDelete(): void
    {
        $record = $this->Table->get(1);
        $this->assertFileExists($record->get('path'));
        $this->post($this->url + ['action' => 'delete', 1]);
        $this->assertRedirect(['action' => 'index']);
        $this->assertFlashMessage(I18N_OPERATION_OK);
        $this->assertTrue($this->Table->findById(1)->all()->isEmpty());
        $this->skipIf(IS_WIN);
        $this->assertFileDoesNotExist($record->get('path'));
    }

    /**
     * @test
     * @uses \MeCms\Photos\Controller\Admin\PhotosController::isAuthorized()
     */
    public function testIsAuthorized(): void
    {
        $this->assertAllGroupsAreAuthorized('index');
        $this->assertAllGroupsAreAuthorized('upload');

        $this->assertGroupIsAuthorized('delete', 'admin');
        $this->assertGroupIsAuthorized('delete', 'manager');
        $this->assertGroupIsNotAuthorized('delete', 'user');
    }
}
