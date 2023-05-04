<?php

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
