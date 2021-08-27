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

namespace MeCms\Photos\Utility\Sitemap;

use Cake\Cache\Cache;
use Cake\ORM\Query;
use Cake\ORM\TableRegistry;
use MeCms\Utility\Sitemap\SitemapBase;

/**
 * This class contains methods called by the `SitemapBuilder`.
 * Each method must be return an array or urls to add to the sitemap.
 *
 * This class contains methods that will be called automatically.
 * You do not need to call these methods manually.
 */
class Sitemap extends SitemapBase
{
    /**
     * Returns photos urls
     * @return array
     */
    public static function photos(): array
    {
        if (!getConfig('MeCms/Photos.sitemap.photos')) {
            return [];
        }

        /** @var \MeCms\Photos\Model\Table\PhotosAlbumsTable $Table */
        $Table = TableRegistry::get('MeCms/Photos.PhotosAlbums');
        $url = Cache::read('sitemap', $Table->getCacheName());

        if (!$url) {
            $albums = $Table->find('active')
                ->select(['id', 'slug', 'created'])
                ->contain($Table->Photos->getAlias(), function (Query $query) {
                    return $query->find('active')
                        ->select(['id', 'album_id', 'modified'])
                        ->orderDesc('modified');
                })
                ->orderDesc(sprintf('%s.created', $Table->getAlias()));

            if ($albums->isEmpty()) {
                return [];
            }

            //Adds albums index
            $latest = $Table->Photos->find('active')
                ->select(['modified'])
                ->orderDesc('modified')
                ->firstOrFail();
            $url[] = self::parse(['_name' => 'albums'], ['lastmod' => $latest->get('modified')]);

            foreach ($albums as $album) {
                //Adds album
                $url[] = self::parse(
                    ['_name' => 'album', $album->get('slug')],
                    ['lastmod' => array_value_first($album->get('photos'))->get('modified')]
                );

                //Adds each photo
                foreach ($album->get('photos') as $photo) {
                    $url[] = self::parse(
                        ['_name' => 'photo', 'slug' => $album->get('slug'), 'id' => (string)$photo->get('id')],
                        ['lastmod' => $photo->get('modified')]
                    );
                }
            }

            Cache::write('sitemap', $url, $Table->getCacheName());
        }

        return $url;
    }
}
