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

namespace MeCms\Photos\View\Helper;

use MeCms\View\Helper\AbstractMenuHelper;

/**
 * PhotosMenuHelper
 */
class PhotosMenuHelper extends AbstractMenuHelper
{
    /**
     * Gets the links for this menu. Each links is an array of parameters
     * @return array[]
     */
    public function getLinks(): array
    {
        $params = ['plugin' => 'MeCms/Photos', 'prefix' => ADMIN_PREFIX];

        return [
            [__d('me_cms/photos', 'List photos'), ['controller' => 'Photos', 'action' => 'index'] + $params],
            [__d('me_cms/photos', 'Upload photos'), ['controller' => 'Photos', 'action' => 'upload'] + $params],
            [__d('me_cms/photos', 'List albums'), ['controller' => 'PhotosAlbums', 'action' => 'index'] + $params],
            [__d('me_cms/photos', 'Add album'), ['controller' => 'PhotosAlbums', 'action' => 'add'] + $params],
        ];
    }

    /**
     * Gets the options for this menu
     * @return array
     */
    public function getOptions(): array
    {
        return ['icon' => 'camera-retro'];
    }

    /**
     * Gets the title for this menu
     * @return string
     */
    public function getTitle(): string
    {
        return I18N_PHOTOS;
    }
}
