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

namespace MeCms\Photos\Model\Validation;

use MeCms\Validation\AppValidator;

/**
 * Photo validator class
 */
class PhotoValidator extends AppValidator
{
    /**
     * Valid extensions
     */
    protected const VALID_EXTENSIONS = ['gif', 'jpg', 'jpeg', 'png'];

    /**
     * Construct
     */
    public function __construct()
    {
        parent::__construct();

        $this->add('album_id', [
            'naturalNumber' => [
                'message' => I18N_SELECT_VALID_OPTION,
                'rule' => 'naturalNumber',
            ],
        ])->requirePresence('album_id', 'create');

        $this->add('filename', [
            'extension' => [
                'message' => __d('me_cms', 'Valid extensions: {0}', implode(', ', self::VALID_EXTENSIONS)),
                'rule' => ['extension', self::VALID_EXTENSIONS],
            ],
        ])->requirePresence('filename', 'create');
    }
}
