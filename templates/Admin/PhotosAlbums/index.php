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
 * @var \MeCms\Photos\Model\Entity\PhotosAlbum[] $albums
 * @var \MeCms\View\View\Admin\AppView $this
 */
$this->extend('MeCms./Admin/common/index');
$this->assign('title', __d('me_cms/photos', 'Albums'));
$this->append('actions', $this->Html->button(
    I18N_ADD,
    ['action' => 'add'],
    ['class' => 'btn-success', 'icon' => 'plus']
));
$this->append('actions', $this->Html->button(
    __d('me_cms/photos', 'Upload photos'),
    ['controller' => 'Photos', 'action' => 'upload'],
    ['class' => 'btn-success', 'icon' => 'plus']
));
?>

<table class="table table-hover">
    <thead>
        <tr>
            <th class="text-center"><?= $this->Paginator->sort('id', I18N_ID) ?></th>
            <th><?= $this->Paginator->sort('title', I18N_TITLE) ?></th>
            <th class="text-center"><?= I18N_DESCRIPTION ?></th>
            <th class="text-center"><?= $this->Paginator->sort('created', I18N_DATE) ?></th>
            <th class="text-nowrap text-center"><?= $this->Paginator->sort('photo_count', I18N_PHOTOS) ?></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($albums as $album) : ?>
            <tr>
                <td class="text-nowrap text-center">
                    <code><?= $album->get('id') ?></code>
                </td>
                <td>
                    <strong>
                        <?= $this->Html->link($album->get('title'), ['action' => 'edit', $album->get('id')]) ?>
                    </strong>
                    <?php
                    $actions = [
                        $this->Html->link(I18N_EDIT, ['action' => 'edit', $album->get('id')], ['icon' => 'pencil-alt']),
                    ];

                    //Only admins and managers can delete albums
                    if ($this->Identity->isGroup('admin', 'manager')) {
                        $actions[] = $this->Form->postLink(I18N_DELETE, ['action' => 'delete', $album->get('id')], [
                            'class' => 'text-danger',
                            'icon' => 'trash-alt',
                            'confirm' => I18N_SURE_TO_DELETE,
                        ]);
                    }

                    $actions[] = $this->Html->link(
                        I18N_UPLOAD,
                        ['controller' => 'Photos', 'action' => 'upload', '?' => ['album' => $album->get('id')]],
                        ['icon' => 'upload']
                    );

                    if ($album->get('photo_count')) {
                        $actions[] = $this->Html->link(
                            I18N_OPEN,
                            ['_name' => 'album', $album->get('slug')],
                            ['icon' => 'external-link-alt', 'target' => '_blank']
                        );
                    }

                    echo $this->Html->ul($actions, ['class' => 'actions']);
                    ?>
                </td>
                <td class="text-center">
                    <?= $album->get('description') ?>
                </td>
                <td class="text-nowrap text-center">
                    <div class="d-none d-lg-block">
                        <?= $album->get('created')->i18nFormat() ?>
                    </div>
                    <div class="d-lg-none">
                        <div><?= $album->get('created')->i18nFormat(getConfigOrFail('main.date.short')) ?></div>
                        <div><?= $album->get('created')->i18nFormat(getConfigOrFail('main.time.short')) ?></div>
                    </div>
                </td>
                <td class="text-nowrap text-center">
                    <?php
                    if ($album->hasValue('photo_count')) {
                        echo $this->Html->link(
                            (string)$album->get('photo_count'),
                            ['controller' => 'Photos', 'action' => 'index', '?' => ['album' => $album->get('id')]],
                            ['title' => I18N_BELONG_ELEMENT]
                        );
                    } else {
                        echo $album->get('photo_count');
                    }
                    ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?= $this->element('MeTools.paginator') ?>
