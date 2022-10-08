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

namespace MeCms\Photos\Model\Table;

use Cake\Datasource\EntityInterface;
use Cake\Event\Event;
use Cake\ORM\RulesChecker;
use MeCms\Model\Table\AppTable;
use MeCms\ORM\Query;
use MeCms\Photos\Model\Validation\PhotosAlbumValidator;

/**
 * PhotosAlbums model
 * @method findActiveBySlug($slug)
 * @method findById($id)
 * @property \Cake\ORM\Association\HasMany $Photos
 */
class PhotosAlbumsTable extends AppTable
{
    /**
     * Cache configuration name
     * @var string
     */
    protected string $cache = 'photos';

    /**
     * Called after an entity has been deleted
     * @param \Cake\Event\Event $event Event object
     * @param \Cake\Datasource\EntityInterface $entity Entity object
     * @return void
     */
    public function afterDelete(Event $event, EntityInterface $entity): void
    {
        @rmdir($entity->get('path'));

        parent::afterDelete($event, $entity);
    }

    /**
     * Called after an entity is saved
     * @param \Cake\Event\Event $event Event object
     * @param \Cake\Datasource\EntityInterface $entity Entity object
     * @return void
     */
    public function afterSave(Event $event, EntityInterface $entity): void
    {
        @mkdir($entity->get('path'), 0777, true);

        parent::afterSave($event, $entity);
    }

    /**
     * Returns a rules checker object that will be used for validating
     *  application integrity
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules): RulesChecker
    {
        return $rules->add($rules->isUnique(['slug'], I18N_VALUE_ALREADY_USED))
            ->add($rules->isUnique(['title'], I18N_VALUE_ALREADY_USED));
    }

    /**
     * "active" find method
     * @param \MeCms\ORM\Query $query Query object
     * @return \MeCms\ORM\Query $query Query object
     */
    public function findActive(Query $query): Query
    {
        return $query->innerJoinWith($this->Photos->getAlias(), fn(Query $query): Query => $query->find('active'))->distinct();
    }

    /**
     * Initialize method
     * @param array $config The configuration for the table
     * @return void
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('photos_albums');
        $this->setDisplayField('title');
        $this->setPrimaryKey('id');

        $this->hasMany('Photos', ['className' => PhotosTable::class])
            ->setForeignKey('album_id');

        $this->addBehavior('Timestamp');

        $this->_validatorClass = PhotosAlbumValidator::class;
    }
}
