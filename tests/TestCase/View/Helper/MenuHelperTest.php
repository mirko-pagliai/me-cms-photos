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

use MeTools\TestSuite\HelperTestCase;
use MeTools\View\Helper\HtmlHelper;

/**
 * MenuHelperTest class
 */
class MenuHelperTest extends HelperTestCase
{
    /**
     * Internal method to write auth data on session
     * @param array $data Data you want to write
     * @return void
     */
    protected function writeAuthOnSession(array $data = []): void
    {
        $this->Helper->getView()->getRequest()->getSession()->write('Auth.User', $data);
        $this->Helper->Auth->initialize([]);
    }

    /**
     * Internal method to build links
     * @param array $links Links
     * @return string
     */
    protected function buildLinks(array $links): string
    {
        return implode(PHP_EOL, array_map(function (array $link) {
            return call_user_func_array([$this->getMockForHelper(HtmlHelper::class, null), 'link'], $link);
        }, $links));
    }

    /**
     * Tests for `photos()` method
     * @test
     */
    public function testPhotos()
    {
        [$links,,, $handledControllers] = $this->Helper->photos();
        $this->assertNotEmpty($links);
        $this->assertEquals(['Photos', 'PhotosAlbums'], $handledControllers);
    }
}
