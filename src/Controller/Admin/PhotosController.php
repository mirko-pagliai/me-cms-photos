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

namespace MeCms\Photos\Controller\Admin;

use Cake\Event\EventInterface;
use Cake\Http\Cookie\Cookie;
use Cake\Http\Response;
use MeCms\Controller\Admin\AppController;
use MeCms\Model\Entity\User;

/**
 * Photos controller
 * @property \MeCms\Photos\Model\Table\PhotosTable $Photos
 * @property \MeTools\Controller\Component\UploaderComponent $Uploader
 */
class PhotosController extends AppController
{
    /**
     * Called before the controller action
     * @param \Cake\Event\EventInterface $event An Event instance
     * @return \Cake\Http\Response|null|void
     */
    public function beforeFilter(EventInterface $event)
    {
        $parent = parent::beforeFilter($event);
        if ($parent instanceof Response) {
            return $parent;
        }

        $albums = $this->Photos->Albums->getList()->all();
        if ($albums->isEmpty()) {
            $this->Flash->alert(__d('me_cms/photos', 'You must first create an album'));

            return $this->redirect(['controller' => 'PhotosAlbums', 'action' => 'index']);
        }

        $this->set(compact('albums'));
    }

    /**
     * Checks if the provided user is authorized for the request
     * @param \MeCms\Model\Entity\User $User User entity
     * @return bool `true` if the user is authorized, otherwise `false`
     */
    public function isAuthorized(User $User): bool
    {
        //Only admins and managers can delete photos
        return !$this->getRequest()->is('delete') || in_array($User->get('group')->get('name'), ['admin', 'manager']);
    }

    /**
     * Lists photos.
     *
     * This action can use the `index_as_grid` template.
     * @return void
     */
    public function index(): void
    {
        //The "render" type can set by query or by cookies
        $render = $this->getRequest()->getQuery('render', $this->getRequest()->getCookie('render-photos'));

        $query = $this->Photos->find()->contain(['Albums' => ['fields' => ['id', 'slug', 'title']]]);

        $this->paginate['order'] = ['Photos.created' => 'DESC'];

        //Sets paginate limit and the maximum paginate limit
        //See https://book.cakephp.org/4/en/controllers/components/pagination.html#limit-the-maximum-number-of-rows-per-page
        if ($render === 'grid') {
            $this->paginate['limit'] = $this->paginate['maxLimit'] = getConfigOrFail('MeCms/Photos.admin.photos');
            $this->viewBuilder()->setTemplate('index_as_grid');
        }

        $this->set('photos', $this->paginate($this->Photos->queryFromFilter($query, $this->getRequest()->getQueryParams())));
        $this->set('title', I18N_PHOTOS);

        if ($render) {
            $cookie = (new Cookie('render-photos', $render))->withNeverExpire();
            $this->setResponse($this->getResponse()->withCookie($cookie));
        }
    }

    /**
     * Uploads photos
     * @return \Cake\Http\Response|null
     * @throws \Tools\Exception\ObjectWrongInstanceException|\Throwable
     * @uses \MeCms\Controller\Admin\AppController::setUploadError()
     */
    public function upload(): ?Response
    {
        /** @var string $album */
        $album = $this->getRequest()->getQuery('album');
        $albums = $this->viewBuilder()->getVar('albums')->toArray();

        //If there's only one available album
        if (!$album && count($albums) < 2) {
            return $this->redirect(['?' => ['album' => array_key_first($albums)]]);
        }

        if ($this->getRequest()->getData('file')) {
            if (!$album) {
                $this->setUploadError(I18N_MISSING_ID);

                return null;
            }

            $uploaded = $this->Uploader->setFile($this->getRequest()->getData('file'))
                ->mimetype('image')
                ->save(PHOTOS . DS . $album);

            if (!$uploaded) {
                $this->setUploadError($this->Uploader->getError() ?: '');

                return null;
            }

            $entity = $this->Photos->newEntity([
                'album_id' => $album,
                'filename' => basename($uploaded),
            ]);

            if ($entity->getErrors()) {
                $this->setUploadError(array_value_first_recursive($entity->getErrors()));

                return null;
            }

            if (!$this->Photos->save($entity)) {
                $this->setUploadError(I18N_OPERATION_NOT_OK);
            }
        }

        return null;
    }

    /**
     * Edits photo
     * @param string $id Photo ID
     * @return \Cake\Http\Response|null|void
     */
    public function edit(string $id)
    {
        $photo = $this->Photos->get($id);

        if ($this->getRequest()->is(['patch', 'post', 'put'])) {
            $photo = $this->Photos->patchEntity($photo, $this->getRequest()->getData());

            if ($this->Photos->save($photo)) {
                $this->Flash->success(I18N_OPERATION_OK);

                return $this->redirect($this->referer(['action' => 'index']));
            }

            $this->Flash->error(I18N_OPERATION_NOT_OK);
        }

        $this->set(compact('photo'));
    }

    /**
     * Downloads photo
     * @param string $id Photo ID
     * @return \Cake\Http\Response
     */
    public function download(string $id): Response
    {
        return $this->getResponse()->withFile($this->Photos->get($id)->get('path'), ['download' => true]);
    }

    /**
     * Deletes photo
     * @param string $id Photo ID
     * @return \Cake\Http\Response|null
     */
    public function delete(string $id): ?Response
    {
        $this->getRequest()->allowMethod(['post', 'delete']);
        $this->Photos->deleteOrFail($this->Photos->get($id));
        $this->Flash->success(I18N_OPERATION_OK);

        return $this->redirect($this->referer(['action' => 'index']));
    }
}
