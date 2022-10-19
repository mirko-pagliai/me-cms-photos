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

namespace MeCms\Photos\Test\TestCase\Model\Table;

use MeCms\Photos\Model\Validation\PhotoValidator;
use MeCms\TestSuite\TableTestCase;
use Tools\Filesystem;

/**
 * PhotosTableTest class
 * @property \MeCms\Photos\Model\Table\PhotosTable $Table
 */
class PhotosTableTest extends TableTestCase
{
    /**
     * @var array
     */
    protected static array $example = [
        'album_id' => 1,
        'filename' => 'pic.jpg',
    ];

    /**
     * Fixtures
     * @var array<string>
     */
    public $fixtures = [
        'plugin.MeCms/Photos.Photos',
        'plugin.MeCms/Photos.PhotosAlbums',
    ];

    /**
     * This method is called before the first test of this test class is run.
     */
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        $dir = Filesystem::instance()->concatenate(PHOTOS, (string)self::$example['album_id']);
        if (!file_exists($dir)) {
            mkdir($dir);
        }
    }

    /**
     * This method is called after the last test of this test class is run.
     */
    public static function tearDownAfterClass(): void
    {
        parent::tearDownAfterClass();

        Filesystem::instance()->unlinkRecursive(PHOTOS, '.gitkeep', true);
    }

    /**
     * Called before every test method
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $file = Filesystem::instance()->concatenate(PHOTOS, (string)self::$example['album_id'], self::$example['filename']);
        if (!file_exists($file)) {
            copy(WWW_ROOT . 'img' . DS . 'image.jpg', $file);
        }
    }

    /**
     * Test for event methods
     * @uses \MeCms\Photos\Model\Table\PhotosTable::afterDelete()
     * @test
     */
    public function testEventMethods(): void
    {
        $entity = $this->Table->get(1);
        $this->assertFileExists($entity->get('path'));
        $this->Table->delete($entity);
        $this->assertFileDoesNotExist($entity->get('path'));
    }

    /**
     * Test for `buildRules()` method
     * @test
     */
    public function testBuildRules(): void
    {
        $entity = $this->Table->newEntity(self::$example);
        $this->assertNotEmpty($this->Table->save($entity));

        //Saves again the same entity
        $entity = $this->Table->newEntity(self::$example);
        $this->assertFalse($this->Table->save($entity));
        $this->assertEquals(['filename' => ['_isUnique' => I18N_VALUE_ALREADY_USED]], $entity->getErrors());

        $entity = $this->Table->newEntity(['album_id' => 999, 'filename' => 'pic2.jpg']);
        $this->assertFalse($this->Table->save($entity));
        $this->assertEquals(['album_id' => ['_existsIn' => I18N_SELECT_VALID_OPTION]], $entity->getErrors());
    }

    /**
     * Test for `initialize()` method
     * @test
     */
    public function testInitialize(): void
    {
        $this->assertEquals('photos', $this->Table->getTable());
        $this->assertEquals('filename', $this->Table->getDisplayField());
        $this->assertEquals('id', $this->Table->getPrimaryKey());

        $this->assertBelongsTo($this->Table->Albums);
        $this->assertEquals('album_id', $this->Table->Albums->getForeignKey());
        $this->assertEquals('INNER', $this->Table->Albums->getJoinType());

        $this->assertHasBehavior(['Timestamp', 'CounterCache']);

        $this->assertInstanceOf(PhotoValidator::class, $this->Table->getValidator());
    }

    /**
     * Test for `find()` methods
     * @test
     */
    public function testFindMethods(): void
    {
        $query = $this->Table->find('active');
        $this->assertSqlEndsWith('FROM `photos` `Photos` WHERE `Photos`.`active` = :c0', $query->sql());
        $this->assertTrue($query->getValueBinder()->bindings()[':c0']['value']);

        $query = $this->Table->find('pending');
        $this->assertSqlEndsWith('FROM `photos` `Photos` WHERE `Photos`.`active` = :c0', $query->sql());
        $this->assertFalse($query->getValueBinder()->bindings()[':c0']['value']);
    }

    /**
     * Test for `queryFromFilter()` method
     * @test
     */
    public function testQueryFromFilter(): void
    {
        $query = $this->Table->queryFromFilter($this->Table->find(), ['album' => 2]);
        $this->assertSqlEndsWith('FROM `photos` `Photos` WHERE `album_id` = :c0', $query->sql());
        $this->assertEquals(2, $query->getValueBinder()->bindings()[':c0']['value']);

        $query = $this->Table->queryFromFilter($this->Table->find(), ['filename' => 'image.jpg']);
        $this->assertSqlEndsWith('FROM `photos` `Photos` WHERE `Photos`.`filename` like :c0', $query->sql());
        $this->assertEquals('%image.jpg%', $query->getValueBinder()->bindings()[':c0']['value']);

        //With some invalid data
        $query = $this->Table->queryFromFilter($this->Table->find(), ['filename' => 'ab']);
        $this->assertEmpty($query->getValueBinder()->bindings());
    }
}
