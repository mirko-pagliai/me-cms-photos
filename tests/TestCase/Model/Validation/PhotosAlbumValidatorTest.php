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

namespace MeCms\Photos\Test\TestCase\Model\Validation;

use MeCms\TestSuite\ValidationTestCase;

/**
 * PhotosAlbumValidatorTest class
 */
class PhotosAlbumValidatorTest extends ValidationTestCase
{
    /**
     * @var array
     */
    protected array $example = ['title' => 'My title', 'slug' => 'my-slug'];

    /**
     * Fixtures
     * @var array<string>
     */
    public $fixtures = [
        'plugin.MeCms/Photos.PhotosAlbums',
    ];
}
