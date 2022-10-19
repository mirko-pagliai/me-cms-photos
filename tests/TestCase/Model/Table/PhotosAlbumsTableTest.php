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

use MeCms\Photos\Model\Validation\PhotosAlbumValidator;
use MeCms\TestSuite\TableTestCase;
use Tools\Filesystem;

/**
 * PhotosAlbumsTableTest class
 * @property \MeCms\Photos\Model\Table\PhotosAlbumsTable $Table
 */
class PhotosAlbumsTableTest extends TableTestCase
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
     * This method is called after the last test of this test class is run.
     */
    public static function tearDownAfterClass(): void
    {
        parent::tearDownAfterClass();

        Filesystem::instance()->unlinkRecursive(PHOTOS, '.gitkeep', true);
    }

    /**
     * Test for event methods
     * @uses \MeCms\Photos\Model\Table\PhotosAlbumsTable::afterDelete()
     * @uses \MeCms\Photos\Model\Table\PhotosAlbumsTable::afterSave()
     * @test
     */
    public function testEventMethods(): void
    {
        $entity = $this->Table->newEntity(['title' => 'new album', 'slug' => 'new-album']);
        $this->Table->save($entity);
        $this->assertIsWritable($entity->get('path'));

        $this->Table->delete($entity);
        $this->assertFileDoesNotExist($entity->get('path'));
    }

    /**
     * Test for `buildRules()` method
     * @test
     */
    public function testBuildRules(): void
    {
        $example = ['title' => 'My title', 'slug' => 'my-slug'];

        $entity = $this->Table->newEntity($example);
        $this->assertNotEmpty($this->Table->save($entity));

        //Saves again the same entity
        $entity = $this->Table->newEntity($example);
        $this->assertFalse($this->Table->save($entity));
        $this->assertEquals([
            'slug' => ['_isUnique' => I18N_VALUE_ALREADY_USED],
            'title' => ['_isUnique' => I18N_VALUE_ALREADY_USED],
        ], $entity->getErrors());
    }

    /**
     * Test for `initialize()` method
     * @test
     */
    public function testInitialize(): void
    {
        $this->assertEquals('photos_albums', $this->Table->getTable());
        $this->assertEquals('title', $this->Table->getDisplayField());
        $this->assertEquals('id', $this->Table->getPrimaryKey());

        $this->assertHasMany($this->Table->Photos);
        $this->assertEquals('album_id', $this->Table->Photos->getForeignKey());

        $this->assertHasBehavior('Timestamp');

        $this->assertInstanceOf(PhotosAlbumValidator::class, $this->Table->getValidator());
    }

    /**
     * Test for `find()` methods
     * @test
     */
    public function testFindMethods(): void
    {
        $query = $this->Table->find('active');
        $sql = $query->sql();
        $this->assertTrue($query->getValueBinder()->bindings()[':c0']['value']);

        $this->skipIfCakeIsLessThan('4.3');
        $this->assertSqlEndsWith('FROM `photos_albums` `PhotosAlbums` INNER JOIN `photos` `Photos` ON (`Photos`.`active` = :c0 AND `PhotosAlbums`.`id` = `Photos`.`album_id`)', $sql);
    }
}
