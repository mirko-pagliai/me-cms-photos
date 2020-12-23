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

namespace MeCms\Photos\Test\TestCase;

use Cake\I18n\I18n;
use MeCms\TestSuite\TestCase;

/**
 * I18nTest class
 */
class I18nTest extends TestCase
{
    /**
     * Tests that string are translated correctly
     * @test
     */
    public function testI18nConstant()
    {
        $translator = I18n::getTranslator('me_cms/photos', 'it');
        $this->assertEquals('Devi prima creare un album', $translator->translate('You must first create an album'));
    }
}
