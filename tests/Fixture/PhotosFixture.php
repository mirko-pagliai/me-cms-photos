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

use Cake\Datasource\ConnectionInterface;
use Cake\TestSuite\Fixture\TestFixture;
use Tools\Filesystem;

require_once ROOT . 'config' . DS . 'bootstrap.php';

/**
 * PhotosFixture
 */
class PhotosFixture extends TestFixture
{
    /**
     * Fields
     * @var array
     */
    public $fields = [
        'id' => ['type' => 'integer', 'length' => 11, 'null' => false, 'default' => null, 'autoIncrement' => true],
        'album_id' => ['type' => 'integer', 'length' => 11, 'null' => false, 'default' => null, 'autoIncrement' => null],
        'filename' => ['type' => 'string', 'length' => 255, 'null' => false, 'default' => null],
        'description' => ['type' => 'string', 'length' => 255, 'null' => true, 'default' => null],
        'active' => ['type' => 'boolean', 'length' => null, 'null' => false, 'default' => '1'],
        'created' => ['type' => 'datetime', 'length' => null, 'null' => false, 'default' => null],
        'modified' => ['type' => 'datetime', 'length' => null, 'null' => true, 'default' => null],
        '_indexes' => [
            'album_id' => ['type' => 'index', 'columns' => ['album_id'], 'length' => []],
        ],
        '_constraints' => [
            'primary' => ['type' => 'primary', 'columns' => ['id'], 'length' => []],
            'filename' => ['type' => 'unique', 'columns' => ['filename'], 'length' => []],
        ],
    ];

    /**
     * Records
     * @var array
     */
    public $records = [
        [
            'album_id' => 1,
            'filename' => 'photo1.jpg',
            'description' => '<b>A photo</b>',
            'active' => 1,
            'created' => '2016-12-28 10:38:42',
            'modified' => '2016-12-28 10:38:42',
        ],
        [
            'album_id' => 2,
            'filename' => 'photoa.jpg',
            'description' => 'Another photo',
            'active' => 1,
            'created' => '2016-12-28 10:39:42',
            'modified' => '2016-12-28 10:39:42',
        ],
        [
            'album_id' => 1,
            'filename' => 'photo3.jpg',
            'description' => 'Third photo',
            'active' => 1,
            'created' => '2016-12-28 10:40:42',
            'modified' => '2016-12-28 10:40:42',
        ],
        [
            'album_id' => 2,
            'filename' => 'photo4.jpg',
            'description' => 'No active photo',
            'active' => 0,
            'created' => '2016-12-28 10:41:42',
            'modified' => '2016-12-28 10:41:42',
        ],
    ];

    /**
     * Run before each test is executed.
     * Should insert all the records into the test database.
     * @param \Cake\Datasource\ConnectionInterface $connection An instance of
     *  the connection into which the records will be inserted
     * @return \Cake\Database\StatementInterface|bool on success or if there are
     *  no records to insert, or false on failure
     */
    public function insert(ConnectionInterface $connection)
    {
        $Filesystem = new Filesystem();

        foreach ($this->records as $record) {
            @$Filesystem->copy(WWW_ROOT . 'img' . DS . 'image.jpg', $Filesystem->concatenate(PHOTOS, (string)$record['album_id'], $record['filename']));
        }

        return parent::insert($connection);
    }
}
