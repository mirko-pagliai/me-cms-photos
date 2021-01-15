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

use Cake\Controller\Controller;
use Cake\Event\EventInterface;
use MeCms\Photos\Model\Entity\Photo;
use MeCms\TestSuite\ControllerTestCase;

/**
 * PhotosControllerTest class
 */
class PhotosControllerTest extends ControllerTestCase
{
    /**
     * Fixtures
     * @var array
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
            $plugin = $this->getPluginName($this);
            $this->_controller->$alias = $this->getMockForModel($plugin . '.' . $alias, ['save']);
        }
    }

    /**
     * Tests for `beforeFilter()` method
     * @return void
     * @test
     */
    public function testBeforeFilter()
    {
        parent::testBeforeFilter();

        $this->Table->Albums->deleteAll(['id IS NOT' => null]);
        $this->get($this->url + ['action' => 'index']);
        $this->assertRedirect(['controller' => 'PhotosAlbums', 'action' => 'index']);
    }

    /**
     * Tests for `index()` method
     * @return void
     * @test
     */
    public function testIndex()
    {
        $this->get($this->url + ['action' => 'index']);
        $this->assertResponseOkAndNotEmpty();
        $this->assertTemplate('Admin' . DS . $this->Controller->getName() . DS . 'index.php');
        $this->assertContainsOnlyInstancesOf(Photo::class, $this->viewVariable('photos'));
        $this->assertCookieIsEmpty('render-photos');
    }

    /**
     * Tests for `index()` method, render as `grid`
     * @return void
     * @test
     */
    public function testIndexAsGrid()
    {
        $this->get($this->url + ['action' => 'index', '?' => ['render' => 'grid']]);
        $this->assertResponseOkAndNotEmpty();
        $this->assertTemplate('Admin' . DS . $this->Controller->getName() . DS . 'index_as_grid.php');
        $this->assertContainsOnlyInstancesOf(Photo::class, $this->viewVariable('photos'));
        $this->assertCookie('grid', 'render-photos');

        //With cookie
        $this->cookie('render-photos', 'grid');
        $this->get($this->url + ['action' => 'index']);
        $this->assertResponseOkAndNotEmpty();
        $this->assertTemplate('Admin' . DS . $this->Controller->getName() . DS . 'index_as_grid.php');
        $this->assertCookie('grid', 'render-photos');
    }

    /**
     * Tests for `upload()` method
     * @return void
     * @test
     */
    public function testUpload()
    {
        $url = $this->url + ['action' => 'upload'];

        $this->get($url);
        $this->assertResponseOkAndNotEmpty();
        $this->assertTemplate('Admin' . DS . $this->Controller->getName() . DS . 'upload.php');

        $url += ['?' => ['album' => 1]];
        $this->get($url);
        $this->assertResponseOkAndNotEmpty();
        $this->assertTemplate('Admin' . DS . $this->Controller->getName() . DS . 'upload.php');

        //POST request. This works
        $file = $this->createImageToUpload();
        $this->post($url + ['_ext' => 'json'], compact('file'));
        $this->assertResponseOkAndNotEmpty();
        $record = $this->Table->find()->last();
        $this->assertEquals(1, $record->get('album_id'));
        $this->assertEquals($file['name'], $record->get('filename'));
        $this->assertFileExists($record->get('path'));
        $this->Table->delete($record);

        //POST request. This works without the parent ID on the query string,
        //  beacuse there is only one record from the associated table
        $this->Table->Albums->deleteAll(['id >' => 1]);
        $this->post($this->url + ['action' => 'upload', '_ext' => 'json'], compact('file'));
        $this->assertRedirect($url);
    }

    /**
     * Tests for `upload()` method, with some errors.
     *
     * The table `save()` method returns `false` for this test.
     * See the `controllerSpy()` method.
     * @return void
     * @test
     */
    public function testUploadErrors()
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
        $this->assertTemplate('Admin' . DS . $this->Controller->getName() . DS . 'json' . DS . 'upload.php');

        //Error during the upload
        $this->post($url, ['file' => ['error' => UPLOAD_ERR_NO_FILE] + $this->createImageToUpload()]);
        $this->assertResponseFailure();
        $this->assertResponseEquals('{"error":"No file was uploaded"}');
        $this->assertTemplate('Admin' . DS . $this->Controller->getName() . DS . 'json' . DS . 'upload.php');

        //Error on entity
        $this->post($url, ['file' => ['name' => 'a.pdf'] + $this->createImageToUpload()]);
        $this->assertResponseFailure();
        $this->assertResponseEquals('{"error":"Valid extensions: gif, jpg, jpeg, png"}');
        $this->assertTemplate('Admin' . DS . $this->Controller->getName() . DS . 'json' . DS . 'upload.php');
    }

    /**
     * Tests for `edit()` method
     * @return void
     * @test
     */
    public function testEdit()
    {
        $url = $this->url + ['action' => 'edit', 1];

        $this->get($url);
        $this->assertResponseOkAndNotEmpty();
        $this->assertTemplate('Admin' . DS . $this->Controller->getName() . DS . 'edit.php');
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
     * Tests for `download()` method
     * @return void
     * @test
     */
    public function testDownload()
    {
        $this->get($this->url + ['action' => 'download', 1]);
        $this->assertResponseOkAndNotEmpty();
        $this->assertFileResponse($this->Table->get(1)->get('path'));
    }

    /**
     * Tests for `delete()` method
     * @return void
     * @test
     */
    public function testDelete()
    {
        $record = $this->Table->get(1);
        $this->assertFileExists($record->get('path'));
        $this->post($this->url + ['action' => 'delete', 1]);
        $this->assertRedirect(['action' => 'index']);
        $this->assertFlashMessage(I18N_OPERATION_OK);
        $this->assertTrue($this->Table->findById(1)->isEmpty());
        $this->skipIf(IS_WIN);
        $this->assertFileNotExists($record->get('path'));
    }

    /**
     * Tests for `isAuthorized()` method
     * @test
     */
    public function testIsAuthorized()
    {
        parent::testIsAuthorized();

        $this->assertGroupsAreAuthorized([
            'admin' => true,
            'manager' => true,
            'user' => true,
        ]);

        //With `delete` action
        $this->assertGroupsAreAuthorized([
            'admin' => true,
            'manager' => true,
            'user' => false,
        ], 'delete');
    }
}
