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
Configure::write('WRITABLE_DIRS', array_merge(Configure::read('WRITABLE_DIRS', []), [PHOTOS]));
