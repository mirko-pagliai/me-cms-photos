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
use Cake\ORM\Query as CakeQuery;
use Cake\ORM\RulesChecker;
use MeCms\Model\Table\AppTable;
use MeCms\ORM\Query;
use MeCms\Photos\Model\Table\PhotosAlbumsTable;
use MeCms\Photos\Model\Validation\PhotoValidator;

/**
 * Photos model
 * @property \Cake\ORM\Association\BelongsTo $Albums
 * @method \MeCms\Photos\Model\Entity\Photo get($primaryKey, $options = [])
 * @method \MeCms\Photos\Model\Entity\Photo newEntity($data = null, array $options = [])
 * @method \MeCms\Photos\Model\Entity\Photo[] newEntities(array $data, array $options = [])
 * @method \MeCms\Photos\Model\Entity\Photo|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \MeCms\Photos\Model\Entity\Photo patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \MeCms\Photos\Model\Entity\Photo[] patchEntities($entities, array $data, array $options = [])
 * @method \MeCms\Photos\Model\Entity\Photo findOrCreate($search, callable $callback = null, $options = [])
 * @method findActiveByAlbumId($albumId)
 * @method findActiveById($id)
 * @method findById($id)
 * @method findPendingById($id)
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 * @mixin \Cake\ORM\Behavior\CounterCacheBehavior
 */
class PhotosTable extends AppTable
{
    /**
     * Cache configuration name
     * @var string
     */
    protected $cache = 'photos';

    /**
     * Called after an entity has been deleted
     * @param \Cake\Event\Event $event Event object
     * @param \Cake\Datasource\EntityInterface $entity Entity object
     * @return void
     */
    public function afterDelete(Event $event, EntityInterface $entity): void
    {
        @unlink($entity->get('path'));

        parent::afterDelete($event, $entity);
    }

    /**
     * Called before each entity is saved
     * @param \Cake\Event\Event $event Event object
     * @param \Cake\Datasource\EntityInterface $entity Entity object
     * @return void
     * @since 2.17.0
     */
    public function beforeSave(Event $event, EntityInterface $entity): void
    {
        [$width, $height] = getimagesize($entity->get('path'));
        $entity->set('size', compact('width', 'height'));
    }

    /**
     * Returns a rules checker object that will be used for validating
     *  application integrity
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules): RulesChecker
    {
        return $rules->add($rules->existsIn(['album_id'], 'Albums', I18N_SELECT_VALID_OPTION))
            ->add($rules->isUnique(['filename'], I18N_VALUE_ALREADY_USED));
    }

    /**
     * "active" find method
     * @param \MeCms\ORM\Query $query Query object
     * @return \MeCms\ORM\Query $query Query object
     */
    public function findActive(Query $query): Query
    {
        return $query->where([sprintf('%s.active', $this->getAlias()) => true]);
    }

    /**
     * "pending" find method
     * @param \MeCms\ORM\Query $query Query object
     * @return \MeCms\ORM\Query $query Query object
     */
    public function findPending(Query $query): Query
    {
        return $query->where([sprintf('%s.active', $this->getAlias()) => false]);
    }

    /**
     * Initialize method
     * @param array $config The configuration for the table
     * @return void
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('photos');
        $this->setDisplayField('filename');
        $this->setPrimaryKey('id');

        $this->belongsTo('Albums', ['className' => PhotosAlbumsTable::class])
            ->setForeignKey('album_id')
            ->setJoinType('INNER');

        $this->addBehavior('Timestamp');
        $this->addBehavior('CounterCache', ['Albums' => ['photo_count']]);

        $this->_validatorClass = PhotoValidator::class;
    }

    /**
     * Build query from filter data
     * @param \Cake\ORM\Query $query Query object
     * @param array $data Filter data (`$this->getRequest()->getQueryParams()`)
     * @return \Cake\ORM\Query $query Query object
     */
    public function queryFromFilter(CakeQuery $query, array $data = []): CakeQuery
    {
        $query = parent::queryFromFilter($query, $data);

        //"Album" field
        if (!empty($data['album']) && is_positive($data['album'])) {
            $query->where(['album_id' => $data['album']]);
        }

        //"Filename" field
        if (!empty($data['filename']) && strlen($data['filename']) > 2) {
            $query->where([sprintf('%s.%s LIKE', $this->getAlias(), 'filename') => '%' . $data['filename'] . '%']);
        }

        return $query;
    }
}
