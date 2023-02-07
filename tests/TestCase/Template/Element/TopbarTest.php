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

namespace MeCms\Photos\Test\TestCase\Template\Element;

use MeCms\TestSuite\TestCase;
use MeCms\View\View\AppView;

/**
 * TopbarTest class
 */
class TopbarTest extends TestCase
{
    /**
     * Test for `topbar` element
     * @test
     */
    public function testTopbar(): void
    {
        $result = (new AppView())->element('MeCms.topbar');
        $this->assertStringContainsString('<a href="/" class="nav-link" title="Home">Home</a>', $result);
        $this->assertStringContainsString('<a href="/albums" class="nav-link" title="Photos">Photos</a>', $result);
    }
}
