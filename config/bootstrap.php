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

use Cake\Cache\Cache;
use Cake\Core\Configure;

//Sets the default photos directory
if (!defined('PHOTOS')) {
    define('PHOTOS', WWW_ROOT . 'img' . DS . 'photos' . DS);
}

//Loads the MeCms/Photos configuration and merges with the configuration from
//  application, if exists
Configure::load('MeCms/Photos.me_cms_photos');
if (is_readable(CONFIG . 'me_cms_photos.php')) {
    Configure::load('me_cms_photos');
}

//Sets the cache
if (!Cache::getConfig('photos')) {
    Cache::setConfig('photos', [
        'className' => 'File',
        'duration' => '+999 days',
        'prefix' => '',
        'mask' => 0777,
        'path' => CACHE . 'me_cms_photos',
    ]);
}

//Sets directories to be created and must be writable
$writableDirs = Configure::read('WRITABLE_DIRS', []);
if (!in_array(PHOTOS, $writableDirs)) {
    Configure::write('WRITABLE_DIRS', array_merge($writableDirs, [PHOTOS]));
}

//Sets files to be copied
$configFiles = Configure::read('CONFIG_FILES', []);
if (!in_array('MeCms/Photos.me_cms_photos', $configFiles)) {
    Configure::write('CONFIG_FILES', array_merge($configFiles, ['MeCms/Photos.me_cms_photos']));
}

if (!defined('I18N_PHOTOS')) {
    define('I18N_PHOTOS', __d('me_cms/photos', 'Photos'));
}
