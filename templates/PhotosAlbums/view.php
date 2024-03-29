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
 * @var \MeCms\Photos\Model\Entity\PhotosAlbum $album
 * @var \Cake\ORM\ResultSet<\MeCms\Photos\Model\Entity\Photo> $photos
 * @var \MeCms\View\View\AppView $this
 */

$this->extend('MeCms./common/view');
$this->assign('title', $album->get('title'));

if (getConfig('default.fancybox')) {
    $this->Library->fancybox();
}

/**
 * Breadcrumb
 */
$this->Breadcrumbs->add(I18N_PHOTOS, ['_name' => 'albums']);
$this->Breadcrumbs->add($album->get('title'), $album->get('url'));

//Sets base options for each photo
$baseOptions = ['class' => 'd-block'];

//If Fancybox is enabled
if (getConfig('default.fancybox')) {
    $baseOptions = ['class' => 'd-block', 'data-fancybox' => 'gallery'];
}
?>

<div class="row">
    <?php
    foreach ($photos as $photo) {
        $linkOptions = $baseOptions;
        if ($photo->has('description')) {
            $linkOptions += ['title' => $photo->get('description')];
        }
        //If Fancybox is enabled, adds some options
        if (getConfig('default.fancybox')) {
            $linkOptions += [
                'data-caption' => $photo->get('description'),
                'data-src' => $this->Thumb->resizeUrl($photo->get('path'), ['width' => 1280]),
            ];
        }

        echo $this->Html->div('col-md-4 col-lg-3 mb-4', $this->element('MeCms/Photos.views/photo-preview', [
            'link' => $photo->get('url'),
            'path' => $photo->get('path'),
            'text' => $photo->get('description'),
        ] + compact('linkOptions')));
    }
    ?>
</div>

<?= $this->element('MeTools.paginator') ?>
