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

namespace MeCms\Photos\Controller;

use Cake\Cache\Cache;
use MeCms\Controller\AppController;
use MeCms\ORM\Query;

/**
 * PhotosAlbums controller
 * @property \MeCms\Photos\Model\Table\PhotosAlbumsTable $PhotosAlbums
 */
class PhotosAlbumsController extends AppController
{
    /**
     * Lists albums
     * @return \Cake\Http\Response|null|void
     */
    public function index()
    {
        $this->paginate['limit'] = $this->paginate['maxLimit'] = getConfigOrFail('MeCms/Photos.default.albums');

        //Sets the cache name
        /** @var string $queryPage */
        $queryPage = $this->getRequest()->getQuery('page', '1');
        $cache = sprintf('albums_limit_%s_page_%s', $this->paginate['limit'], trim($queryPage, '/'));

        //Tries to get data from the cache
        $albums = Cache::read($cache, $this->PhotosAlbums->getCacheName());
        $paging = Cache::read($cache . '_paging', $this->PhotosAlbums->getCacheName());

        //If the data are not available from the cache
        if (!$albums || !$paging) {
            $query = $this->PhotosAlbums->find('active')
                ->select(['id', 'title', 'slug', 'photo_count', 'created'])
                ->contain($this->PhotosAlbums->Photos->getAlias(), fn(Query $query): Query => $query->find('active')->select(['id', 'album_id', 'filename']))
                ->orderDesc(sprintf('%s.created', $this->PhotosAlbums->getAlias()));

            [$albums, $paging] = [$this->paginate($query), $this->getPaging()];

            //Writes on cache
            Cache::writeMany([$cache => $albums, $cache . '_paging' => $paging], $this->PhotosAlbums->getCacheName());
        //Else, sets the paging parameter
        } else {
            $this->setPaging($paging);
        }

        //If there is only one record, redirects
        if ($albums->count() === 1) {
            return $this->redirect(['_name' => 'album', $albums->first()->get('slug')]);
        }

        $this->set(compact('albums'));
    }

    /**
     * Views album
     * @param string $slug Album slug
     * @return \Cake\Http\Response|null|void
     */
    public function view(string $slug)
    {
        //Data can be passed as query string, from a widget
        if ($this->getRequest()->getQuery('q')) {
            return $this->redirect([$this->getRequest()->getQuery('q')]);
        }

        //Gets album ID and title
        $album = $this->PhotosAlbums->findActiveBySlug($slug)
            ->select(['id', 'title', 'slug'])
            ->cache('album_' . md5($slug))
            ->firstOrFail();

        /** @var string $queryPage */
        $queryPage = $this->getRequest()->getQuery('page', '1');
        $this->paginate['limit'] = $this->paginate['maxLimit'] = getConfigOrFail('MeCms/Photos.default.photos');

        //Sets the cache name
        $cache = sprintf('album_%s_limit_%s_page_%s', md5($slug), $this->paginate['limit'], $queryPage);
        //Tries to get data from the cache
        $photos = Cache::read($cache, $this->PhotosAlbums->getCacheName());
        $paging = Cache::read($cache . '_paging', $this->PhotosAlbums->getCacheName());

        //If the data are not available from the cache
        if (!$photos || !$paging) {
            $query = $this->PhotosAlbums->Photos->findActiveByAlbumId($album->get('id'))
                ->contain([$this->PhotosAlbums->Photos->Albums->getAlias() => ['fields' => ['slug']]])
                ->orderDesc(sprintf('%s.created', $this->PhotosAlbums->Photos->getAlias()))
                ->orderDesc(sprintf('%s.id', $this->PhotosAlbums->Photos->getAlias()));

            [$photos, $paging] = [$this->paginate($query), $this->getPaging()];

            Cache::writeMany([$cache => $photos, $cache . '_paging' => $paging], $this->PhotosAlbums->getCacheName());
        //Else, sets the paging parameter
        } else {
            $this->setPaging($paging);
        }

        $this->set(compact('album', 'photos'));
    }
}
