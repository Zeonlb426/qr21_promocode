<?php

declare(strict_types=1);

namespace Nicklasos\LaravelAdmin\MediaLibrary;

use Encore\Admin\Extension;
use Encore\Admin\Form;

/**
 * Class MediaLibrary
 * @package Nicklasos\LaravelAdmin\MediaLibrary
 */
class MediaLibrary extends Extension
{
    public $name = 'laravel-admin-media-library';

    /**
     * @return bool
     */
    public static function boot(): bool
    {
        Form::extend('mediaLibrary', MediaLibraryFile::class);
        Form::extend('multipleMediaLibrary', MediaLibraryMultipleFile::class);

        return parent::boot();
    }
}
