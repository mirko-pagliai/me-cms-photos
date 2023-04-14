# 1.x branch
## 1.1 branch
### 1.1.6
* updated for me-cms 2.31.9.

### 1.1.5
* updated for me-cms 2.31.8;
* vertical alignment is used for admin interface tables.

### 1.1.4
* updated for me-cms 2.31.1;
* several, minor code tweaks.

### 1.1.3
* fixed a small bug in the sorting of columns in the photo editing form.

### 1.1.2
* updated for me-cms 2.30.10.

### 1.1.1-RC2
* `PhotosAlbumsController::index()` now uses the pagination. Added the `MeCms/Photos.default.albums` config value;
* fixed bug for `PhotosAlbum::_getUrl()` method;
* improved `afterDelete()`/`afterSave()` event methods for `PhotosAlbumsTable` and `PhotosTable`;
* uses the new fixture system;
* on an album index, the photo shown as the album preview is no longer random, but the first one. This avoids the
  constant need to generate new thumbnails;
* updated for me-cms 2.30.8-RC5 and me-tools 2.21.4;
* small and numerous improvements of descriptions, tags and code suggested by PhpStorm;
* numerous fixed suggested by phpstan.

### 1.1.0-RC1
* numerous code adjustments for improvement and adaptation to PHP 7.4 new features;
* updated for PHP 8.1 Requires at least PHP 7.4.

## 1.0 branch
### 1.0.4
* numerous code adjustments for improvement and adaptation to PHP 7.4 new features;
* updated for PHP 8.1 Requires at least PHP 7.4.

### 1.0.3
* little fixes.

### 1.0.2
* fixed a small bug in the resolution of the paths;
* fixed a large number of deprecations introduced by CakePHP 4.3.

### 1.0.1
* updated for `me-tools` 2.20.1 and `me-cms` 2.30.1. Fixed some code;
* extensive improvement of function descriptions and tags. The level of `phpstan`
    has been raised.

### 1.0.0
* first release.
