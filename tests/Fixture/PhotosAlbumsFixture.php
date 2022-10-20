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

use Cake\Database\Driver\Postgres;
use Cake\Datasource\ConnectionInterface;
use Cake\TestSuite\Fixture\TestFixture;
use Tools\Exceptionist;
use Tools\Filesystem;

require_once ROOT . 'config' . DS . 'bootstrap.php';

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
            'id' => 1,
            'title' => 'Test album',
            'slug' => 'test-album',
            'description' => 'This is an album test',
            'photo_count' => 2,
            'created' => '2016-12-28 10:38:46',
            'modified' => '2016-12-28 10:38:46',
        ],
        [
            'id' => 2,
            'title' => 'Another album test',
            'slug' => 'another-album-test',
            'description' => 'This is another album test',
            'photo_count' => 2,
            'created' => '2016-12-28 10:39:46',
            'modified' => '2016-12-28 10:39:46',
        ],
        [
            'id' => 3,
            'title' => 'Third album test',
            'slug' => 'third-album-test',
            'description' => 'This is the third album test',
            'photo_count' => 0,
            'created' => '2016-12-28 10:40:46',
            'modified' => '2016-12-28 10:40:46',
        ],
    ];

    /**
     * Run before each test is executed.
     * Should insert all the records into the test database.
     * @param \Cake\Datasource\ConnectionInterface $connection An instance of the connection into which the records will be inserted
     * @return \Cake\Database\StatementInterface|bool on success or if there are no records to insert, or false on failure
     * @throws \Tools\Exception\NotWritableException
     * @noinspection PhpMissingReturnTypeInspection
     */
    public function insert(ConnectionInterface $connection)
    {
        if ($connection->getDriver() instanceof Postgres) {
            $id = range(1, count($this->records));
            $this->records = array_map(function (array $record): array {
                unset($record['id']);

                return $record;
            }, $this->records);
        } else {
            $id = array_map(fn(array $record): int => $record['id'], $this->records);
        }

        foreach ($id as $sId) {
            $dir = Filesystem::instance()->concatenate(PHOTOS, (string)$sId);
            if (!file_exists($dir)) {
                Exceptionist::isWritable(dirname($dir));
                mkdir($dir, 0777, true);
            }
        }

        return parent::insert($connection);
    }
}
