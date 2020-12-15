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
 * Topbar Helper.
 *

 * This helper returns an array with the links to put in the topbar.
 */
class TopbarHelper extends Helper
{
    /**
     * Helpers
     * @var array
     */
    public $helpers = [
        'Html' => ['className' => 'MeTools.Html'],
    ];

    /**
     * Returns an array with the links to put in the topbar
     * @return array
     */
    public function build(): array
    {
        return [
            $this->Html->link(I18N_PHOTOS, ['_name' => 'albums'], ['class' => 'nav-link']),
        ];
    }
}
