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

namespace MeCms\Photos\Test\TestCase\View\Cell;

use Cake\Cache\Cache;
use Cake\Http\ServerRequest;
use Cake\Routing\Router;
use MeCms\TestSuite\CellTestCase;

/**
 * PhotosWidgetsCellTest class
 * @property \MeCms\Photos\Model\Table\PhotosTable $Table
 */
class PhotosWidgetsCellTest extends CellTestCase
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
     * @uses \MeCms\Photos\View\Cell\PhotosWidgetsCell::albums()
     * @test
     */
    public function testAlbums(): void
    {
        $widget = 'MeCms/Photos.Photos::albums';

        $expected = [
            ['div' => ['class' => 'widget mb-5']],
            'h4' => ['class' => 'widget-title'],
            'Albums',
            '/h4',
            ['div' => ['class' => 'widget-content']],
            'form' => ['method' => 'get', 'accept-charset' => 'utf-8', 'action' => '/album/album'],
            ['div' => ['class' => 'input mb-3 select']],
            'select' => ['name' => 'q', 'class' => 'form-control form-select', 'onchange' => 'sendForm(this)'],
            ['option' => ['value' => '']],
            '/option',
            ['option' => ['value' => 'another-album-test']],
            'Another album test (2)',
            '/option',
            ['option' => ['value' => 'test-album']],
            'Test album (2)',
            '/option',
            '/select',
            '/div',
            '/form',
            '/div',
            '/div',
        ];
        $result = $this->Widget->widget($widget)->render();
        $this->assertHtml($expected, $result);

        //Renders as list
        $expected = [
            ['div' => ['class' => 'widget mb-5']],
            'h4' => ['class' => 'widget-title'],
            'Albums',
            '/h4',
            ['div' => ['class' => 'widget-content']],
            'ul' => ['class' => 'fa-ul'],
            ['li' => true],
            ['i' => ['class' => 'fas fa-caret-right fa-li']],
            ' ',
            '/i',
            ['a' => ['href' => '/album/another-album-test', 'title' => 'Another album test']],
            'Another album test',
            '/a',
            '/li',
            ['li' => true],
            ['i' => ['class' => 'fas fa-caret-right fa-li']],
            ' ',
            '/i',
            ['a' => ['href' => '/album/test-album', 'title' => 'Test album']],
            'Test album',
            '/a',
            '/li',
            '/ul',
            '/div',
            '/div',
        ];
        $result = $this->Widget->widget($widget, ['render' => 'list'])->render();
        $this->assertHtml($expected, $result);

        //Empty on albums index
        $request = $this->Widget->getView()->getRequest()->withEnv('REQUEST_URI', Router::url(['_name' => 'albums']));
        $this->Widget->getView()->setRequest($request);
        $this->assertEmpty($this->Widget->widget($widget)->render());
        $this->Widget->getView()->setRequest(new ServerRequest());
        $this->assertEquals(2, Cache::read('widget_albums', $this->Table->getCacheName())->count());

        //With no photos
        $this->Table->deleteAll(['id IS NOT' => null]);
        $request = $this->Widget->getView()->getRequest()->withEnv('REQUEST_URI', '/');
        $this->Widget->getView()->setRequest($request);
        $this->assertEmpty($this->Widget->widget($widget)->render());
        $this->assertEmpty($this->Widget->widget($widget, ['render' => 'list'])->render());
    }

    /**
     * @uses \MeCms\Photos\View\Cell\PhotosWidgetsCell::latest()
     * @test
     */
    public function testLatest(): void
    {
        $widget = 'MeCms/Photos.Photos::latest';

        $expected = [
            ['div' => ['class' => 'widget mb-5']],
            'h4' => ['class' => 'widget-title'],
            'Latest photo',
            '/h4',
            ['div' => ['class' => 'widget-content']],
            'a' => ['href' => '/albums'],
            'img' => ['src', 'alt', 'class' => 'img-fluid thumbnail'],
            '/a',
            '/div',
            '/div',
        ];
        $result = $this->Widget->widget($widget)->render();
        $this->assertHtml($expected, $result);

        //Tries another limit
        $expected = [
            ['div' => ['class' => 'widget mb-5']],
            'h4' => ['class' => 'widget-title'],
            'Latest 2 photos',
            '/h4',
            ['div' => ['class' => 'widget-content']],
            ['a' => ['href' => '/albums']],
            ['img' => ['src', 'alt', 'class' => 'img-fluid thumbnail']],
            '/a',
            ['a' => ['href' => '/albums']],
            ['img' => ['src', 'alt', 'class' => 'img-fluid thumbnail']],
            '/a',
            '/div',
            '/div',
        ];
        $result = $this->Widget->widget($widget, ['limit' => 2])->render();
        $this->assertHtml($expected, $result);

        //Empty on same controllers
        foreach (['Photos', 'PhotosAlbums'] as $controller) {
            $request = $this->Widget->getView()->getRequest()->withParam('controller', $controller);
            $this->Widget->getView()->setRequest($request);
            $this->assertEmpty($this->Widget->widget($widget)->render());
        }
        $this->Widget->getView()->setRequest(new ServerRequest());

        //Tests cache
        $this->assertEquals(1, Cache::read('widget_latest_1', $this->Table->getCacheName())->count());
        $this->assertEquals(2, Cache::read('widget_latest_2', $this->Table->getCacheName())->count());

        //With no photos
        $this->Table->deleteAll(['id IS NOT' => null]);
        $this->assertEmpty($this->Widget->widget($widget)->render());
    }

    /**
     * @uses \MeCms\Photos\View\Cell\PhotosWidgetsCell::random()
     * @test
     */
    public function testRandom(): void
    {
        $widget = 'MeCms/Photos.Photos::random';

        $expected = [
            ['div' => ['class' => 'widget mb-5']],
            'h4' => ['class' => 'widget-title'],
            'Random photo',
            '/h4',
            ['div' => ['class' => 'widget-content']],
            ['a' => ['href' => '/albums']],
            ['img' => ['src', 'alt', 'class' => 'img-fluid thumbnail']],
            '/a',
            '/div',
            '/div',
        ];
        $result = $this->Widget->widget($widget)->render();
        $this->assertHtml($expected, $result);

        //Tries another limit
        $expected = [
            ['div' => ['class' => 'widget mb-5']],
            'h4' => ['class' => 'widget-title'],
            'Random 2 photos',
            '/h4',
            ['div' => ['class' => 'widget-content']],
            ['a' => ['href' => '/albums']],
            ['img' => ['src', 'alt', 'class' => 'img-fluid thumbnail']],
            '/a',
            ['a' => ['href' => '/albums']],
            ['img' => ['src', 'alt', 'class' => 'img-fluid thumbnail']],
            '/a',
            '/div',
            '/div',
        ];
        $result = $this->Widget->widget($widget, ['limit' => 2])->render();
        $this->assertHtml($expected, $result);

        //Empty on same controllers
        foreach (['Photos', 'PhotosAlbums'] as $controller) {
            $request = $this->Widget->getView()->getRequest()->withParam('controller', $controller);
            $this->Widget->getView()->setRequest($request);
            $this->assertEmpty($this->Widget->widget($widget)->render());
        }
        $this->Widget->getView()->setRequest(new ServerRequest());

        //Tests cache
        $this->assertEquals(3, Cache::read('widget_random_1', $this->Table->getCacheName())->count());
        $this->assertEquals(3, Cache::read('widget_random_2', $this->Table->getCacheName())->count());

        //With no photos
        $this->Table->deleteAll(['id IS NOT' => null]);
        $this->assertEmpty($this->Widget->widget($widget)->render());
    }
}
