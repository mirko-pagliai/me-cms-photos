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

use Cake\View\Helper;

/**
 * Menu Helper.
 *
 * This helper contains methods that will be called automatically to generate
 *  menus for the admin layout.
 * You don't need to call these methods manually, use instead the
 *  `MenuBuilderHelper` helper.
 *
 * Each method must return an array with four values:
 *  - the menu links, as an array of parameters;
 *  - the menu title;
 *  - the options for the menu title;
 *  - the controllers handled by this menu, as an array.
 *
 * See the `\MeCms\View\Helper\MenuBuilderHelper::generate()` method for more
 *  information.
 */
class MenuHelper extends Helper
{
    /**
     * Helpers
     * @var array
     */
    public $helpers = ['MeCms.Auth'];

    /**
     * Internal function to generate the menu for "photos" actions
     * @return array Array with links, title, title options and handled controllers
     */
    public function photos(): array
    {
        $params = ['controller' => 'Photos', 'plugin' => 'MeCms/Photos', 'prefix' => ADMIN_PREFIX];
        $links[] = [__d('me_cms', 'List photos'), ['action' => 'index'] + $params];
        $links[] = [__d('me_cms', 'Upload photos'), ['action' => 'upload'] + $params];

        $params['controller'] = 'PhotosAlbums';
        $links[] = [__d('me_cms', 'List albums'), ['action' => 'index'] + $params];
        $links[] = [__d('me_cms', 'Add album'), ['action' => 'add'] + $params];

        return [$links, I18N_PHOTOS, ['icon' => 'camera-retro'], ['Photos', 'PhotosAlbums']];
    }
}
