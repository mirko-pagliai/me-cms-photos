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
 *
 * @var \MeCms\Photos\Model\Entity\Photo $photo
 * @var \MeCms\View\View\AppView $this
 */

$this->extend('MeCms./common/view');
$this->assign('title', $photo->get('filename'));

/**
 * Breadcrumb
 */
$this->Breadcrumbs->add(I18N_PHOTOS, ['_name' => 'albums']);
$this->Breadcrumbs->add($photo->get('album')->get('title'), $photo->get('album')->get('url'));
$this->Breadcrumbs->add($photo->get('filename'), $photo->get('url'));

/**
 * Meta tags
 */
if ($this->getRequest()->is('action', 'view', 'Photos')) {
    if ($photo->has('modified')) {
        $this->Html->meta(['content' => $photo->get('modified')->toUnixString(), 'property' => 'og:updated_time']);
    }

    if ($photo->has('preview')) {
        $this->Html->meta(['href' => $photo->get('preview')->get('url'), 'rel' => 'image_src']);
        $this->Html->meta(['content' => $photo->get('preview')->get('url'), 'property' => 'og:image']);
        $this->Html->meta(['content' => $photo->get('preview')->get('width'), 'property' => 'og:image:width']);
        $this->Html->meta(['content' => $photo->get('preview')->get('height'), 'property' => 'og:image:height']);
    }

    $this->Html->meta([
        'content' => $this->Text->truncate($photo->get('plain_description'), 100, ['html' => true]),
        'property' => 'og:description',
    ]);
}

echo $this->Thumb->resize($photo->get('path'), ['width' => 1200]);
