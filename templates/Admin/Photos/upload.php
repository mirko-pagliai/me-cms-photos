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
$this->extend('MeCms./common/form');
$this->assign('title', __d('me_cms_photos', 'Upload photos'));
?>

<div class="card card-body bg-light border-0 mb-4">
    <?= $this->Form->createInline(null, ['type' => 'get']) ?>
    <fieldset>
    <?php
    echo $this->Form->label('album', __d('me_cms_photos', 'Album to upload photos'));
    echo $this->Form->control('album', [
        'default' => $this->getRequest()->getQuery('album'),
        'label' => __d('me_cms_photos', 'Album to upload photos'),
        'onchange' => 'sendForm(this)',
        'options' => $albums,
    ]);
    echo $this->Form->submit(I18N_SELECT);
    ?>
    </fieldset>
    <?= $this->Form->end() ?>
</div>

<?php
if ($this->getRequest()->getQuery('album')) {
    echo $this->element('MeCms.admin/uploader');
}
