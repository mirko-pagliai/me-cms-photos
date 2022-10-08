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

namespace MeCms\Photos\Model\Entity;

use Cake\ORM\Entity;
use Cake\Routing\Router;
use MeTools\Utility\BBCode;
use Thumber\Cake\Utility\ThumbCreator;
use Tools\Exceptionist;
use Tools\Filesystem;

/**
 * Photo entity
 * @property int $id
 * @property int $album_id
 * @property string $filename
 * @property string $size
 * @property string $description
 * @property bool $active
 * @property \Cake\I18n\Time $created
 * @property \Cake\I18n\Time $modified
 * @property \MeCms\Photos\Model\Entity\PhotosAlbum $album
 */
class Photo extends Entity
{
    /**
     * Fields that can be mass assigned
     * @var array<string, bool>
     */
    protected $_accessible = [
        '*' => true,
        'id' => false,
        'modified' => false,
    ];

    /**
     * Virtual fields that should be exposed
     * @var array<string>
     */
    protected $_virtual = ['path', 'plain_description', 'preview', 'url'];

    /**
     * Gets the photo path (virtual field)
     * @return string
     * @throws \Tools\Exception\PropertyNotExistsException
     */
    protected function _getPath(): string
    {
        Exceptionist::objectPropertyExists($this, ['album_id', 'filename']);

        return Filesystem::instance()->concatenate(PHOTOS, (string)$this->get('album_id'), $this->get('filename'));
    }

    /**
     * Gets description as string
     * @param string|null $description Description
     * @return string
     */
    protected function _getDescription(?string $description): string
    {
        return (string)$description;
    }

    /**
     * Gets description as plain text (virtual field)
     * @return string
     */
    protected function _getPlainDescription(): string
    {
        return $this->has('description') ? trim(strip_tags((new BBCode())->remove($this->get('description')))) : '';
    }

    /**
     * Gets the photo preview (virtual field)
     * @return \Cake\ORM\Entity Entity with `preview`, `width` and `height`
     *  properties
     * @throws \Tools\Exception\PropertyNotExistsException
     * @uses _getPath()
     */
    protected function _getPreview(): Entity
    {
        $path = $this->_getPath();
        [$width, $height] = getimagesize($path) ?: [];
        $thumber = new ThumbCreator($path);
        $thumber->resize(1200, 1200)->save(['format' => 'jpg']);

        return new Entity(['url' => $thumber->getUrl()] + compact('width', 'height'));
    }

    /**
     * Gets the url (virtual field)
     * @return string
     * @since 2.27.2
     */
    protected function _getUrl(): string
    {
        $album = $this->get('album');
        if (!$this->has('id') || !$album || !$album->has('slug')) {
            return '';
        }

        return Router::url(['_name' => 'photo', 'slug' => $album->get('slug'), 'id' => (string)$this->get('id')], true);
    }
}
