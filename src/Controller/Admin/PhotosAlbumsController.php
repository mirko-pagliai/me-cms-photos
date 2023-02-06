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

use Cake\Http\Response;
use MeCms\Controller\Admin\AppController;
use MeCms\Model\Entity\User;

/**
 * PhotosAlbums controller
 * @property \MeCms\Photos\Model\Table\PhotosAlbumsTable $PhotosAlbums
 */
class PhotosAlbumsController extends AppController
{
    /**
     * Checks if the provided user is authorized for the request
     * @param \MeCms\Model\Entity\User $User User entity
     * @return bool `true` if the user is authorized, otherwise `false`
     */
    public function isAuthorized(User $User): bool
    {
        //Only admins and managers can delete albums
        return !$this->getRequest()->is('delete') || in_array($User->get('group')->get('name'), ['admin', 'manager']);
    }

    /**
     * Lists albums
     * @return void
     */
    public function index(): void
    {
        $this->paginate['order'] = ['created' => 'DESC'];

        $albums = $this->paginate($this->PhotosAlbums->find());

        $this->set(compact('albums'));
    }

    /**
     * Adds photos album
     * @return \Cake\Http\Response|null|void
     */
    public function add()
    {
        $album = $this->PhotosAlbums->newEmptyEntity();

        if ($this->getRequest()->is('post')) {
            $album = $this->PhotosAlbums->patchEntity($album, $this->getRequest()->getData());

            if ($this->PhotosAlbums->save($album)) {
                $this->Flash->success(I18N_OPERATION_OK);

                return $this->redirect($this->referer(['action' => 'index']));
            }

            $this->Flash->error(I18N_OPERATION_NOT_OK);
        }

        $this->set(compact('album'));
    }

    /**
     * Edits photos album
     * @param string $id Photos Album ID
     * @return \Cake\Http\Response|null|void
     */
    public function edit(string $id)
    {
        $album = $this->PhotosAlbums->get($id);

        if ($this->getRequest()->is(['patch', 'post', 'put'])) {
            $album = $this->PhotosAlbums->patchEntity($album, $this->getRequest()->getData());

            if ($this->PhotosAlbums->save($album)) {
                $this->Flash->success(I18N_OPERATION_OK);

                return $this->redirect($this->referer(['action' => 'index']));
            }

            $this->Flash->error(I18N_OPERATION_NOT_OK);
        }

        $this->set(compact('album'));
    }

    /**
     * Deletes photos album
     * @param string $id Photos Album ID
     * @return \Cake\Http\Response|null
     */
    public function delete(string $id): ?Response
    {
        $this->getRequest()->allowMethod(['post', 'delete']);

        //Before deleting, it checks if the album has some photos
        $album = $this->PhotosAlbums->get($id);
        [$method, $message] = ['alert', I18N_BEFORE_DELETE];
        if (!$album->get('photo_count')) {
            $this->PhotosAlbums->deleteOrFail($album);
            [$method, $message] = ['success', I18N_OPERATION_OK];
        }
        $this->Flash->$method($message);

        return $this->redirect($this->referer(['action' => 'index']));
    }
}
