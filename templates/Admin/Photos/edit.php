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
 * @var \MeCms\View\View\AdminView $this
 */
$this->extend('MeCms./common/form');
$this->assign('title', $title = __d('me_cms/photos', 'Edit photo'));
?>

<?= $this->Form->create($photo); ?>
<div class="row">
    <div class="col-lg-3 order-last">
        <div class="float-form">
        <?= $this->Form->control('album_id', ['label' => __d('me_cms/photos', 'Album')]) ?>
        <?= $this->Form->control('active', ['label' => I18N_PUBLISHED]) ?>
        </div>
    </div>
    <fieldset class="col">
        <div class="mb-2">
            <strong><?= I18N_PREVIEW ?></strong>
        </div>
        <?php
        echo $this->Thumb->resize($photo->get('path'), ['width' => 1186], ['class' => 'img-thumbnail mb-3']);

        echo $this->Form->control('filename', [
            'disabled' => true,
            'label' => I18N_FILENAME,
        ]);
        echo $this->Form->control('description', [
            'label' => I18N_DESCRIPTION,
            'rows' => 3,
            'type' => 'textarea',
        ]);
        ?>
    </fieldset>
</div>
<?= $this->Form->submit($title) ?>
<?= $this->Form->end() ?>
