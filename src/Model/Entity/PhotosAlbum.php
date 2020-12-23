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
use Tools\Exceptionist;

/**
 * PhotosAlbum entity
 * @property int $id
 * @property string $title
 * @property string $slug
 * @property string $description
 * @property int $photo_count
 * @property \Cake\I18n\Time $created
 * @property \Cake\I18n\Time $modified
 */
class PhotosAlbum extends Entity
{
    /**
     * Fields that can be mass assigned
     * @var array
     */
    protected $_accessible = [
        '*' => true,
        'id' => false,
        'photo_count' => false,
        'modified' => false,
    ];

    /**
     * Virtual fields that should be exposed
     * @var array
     */
    protected $_virtual = ['path', 'preview', 'url'];

    /**
     * Gets the album full path (virtual field)
     * @return string
     * @throws \Tools\Exception\PropertyNotExistsException
     */
    protected function _getPath(): ?string
    {
        Exceptionist::objectPropertyExists($this, 'id');

        return PHOTOS . $this->get('id');
    }

    /**
     * Gets the album preview (virtual field)
     * @return string
     * @since 2.21.1
     */
    protected function _getPreview(): ?string
    {
        $photos = $this->get('photos');

        return $photos ? array_value_first($photos)->get('path') : null;
    }

    /**
     * Gets the url (virtual field)
     * @return string
     * @since 2.27.2
     * @throws \Tools\Exception\PropertyNotExistsException
     */
    protected function _getUrl(): string
    {
        Exceptionist::objectPropertyExists($this, 'slug');

        return Router::url(['_name' => 'album', $this->get('slug')], true);
    }
}
