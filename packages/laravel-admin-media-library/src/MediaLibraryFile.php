<?php

declare(strict_types=1);

namespace Nicklasos\LaravelAdmin\MediaLibrary;

use Encore\Admin\Form\Field\File;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class MediaLibraryFile
 * @package Nicklasos\LaravelAdmin\MediaLibrary
 */
class MediaLibraryFile extends File
{
    use MediaLibraryBase;

    protected $view = 'admin::form.file';

    public function fill($data)
    {
        parent::fill($data);

        $value = $this->form->model()->getMedia($this->column());

        if ($value->count()) {
            $this->value = $value->first()->id;
        } else {
            $this->value = [];
        }
    }

    public function prepare($file)
    {
        if (request()->has(static::FILE_DELETE_FLAG)) {
            return $this->destroy();
        }

        $this->prepareMedia($file);
    }

    public function destroy()
    {
        $id = $this->original;

        if ($id) {
            $media = Media::whereId($id)->first();
            $media->delete();
        }
    }

    protected function prepareMedia(UploadedFile $file = null)
    {
        $this->name = $this->getStoreName($file);

        $this->form->model()->clearMediaCollection($this->column());

        return $this->uploadMedia($file);
    }

    public function setOriginal($data)
    {
        $value = $this->form->model()->getMedia($this->column);
        if ($value->count()) {
            $this->original = $value[0]->id;
        }
    }

    protected function initialPreviewConfig()
    {
        $media = Media::where('id', '=', $this->value)->first();

        return [$this->getPreviewEntry($media)];
    }
}
