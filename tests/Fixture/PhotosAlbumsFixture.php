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

namespace MeCms\Photos\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * PhotosAlbumsFixture
 */
class PhotosAlbumsFixture extends TestFixture
{
    /**
     * Fields
     * @var array
     */
    public $fields = [
        'id' => ['type' => 'integer', 'length' => 11, 'null' => false, 'default' => null, 'autoIncrement' => true],
        'title' => ['type' => 'string', 'length' => 100, 'null' => false, 'default' => null],
        'slug' => ['type' => 'string', 'length' => 100, 'null' => false, 'default' => null],
        'description' => ['type' => 'string', 'length' => 255, 'null' => true, 'default' => null],
        'photo_count' => ['type' => 'integer', 'length' => 11, 'null' => false, 'default' => '0', 'autoIncrement' => null],
        'created' => ['type' => 'datetime', 'length' => null, 'null' => true, 'default' => null],
        'modified' => ['type' => 'datetime', 'length' => null, 'null' => true, 'default' => null],
        '_constraints' => [
            'primary' => ['type' => 'primary', 'columns' => ['id'], 'length' => []],
        ],
    ];

    /**
     * Records
     * @var array
     */
    public $records = [
        [
            'title' => 'Test album',
            'slug' => 'test-album',
            'description' => 'This is an album test',
            'photo_count' => 2,
            'created' => '2016-12-28 10:38:46',
            'modified' => '2016-12-28 10:38:46',
        ],
        [
            'title' => 'Another album test',
            'slug' => 'another-album-test',
            'description' => 'This is another album test',
            'photo_count' => 2,
            'created' => '2016-12-28 10:39:46',
            'modified' => '2016-12-28 10:39:46',
        ],
        [
            'title' => 'Third album test',
            'slug' => 'third-album-test',
            'description' => 'This is the third album test',
            'photo_count' => 0,
            'created' => '2016-12-28 10:40:46',
            'modified' => '2016-12-28 10:40:46',
        ],
    ];
}
