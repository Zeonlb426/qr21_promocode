<?php

declare(strict_types=1);

namespace Nicklasos\LaravelAdmin\MediaLibrary;

use Encore\Admin\Form\Field\MultipleFile;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class MediaLibraryMultipleFile
 * @package Nicklasos\LaravelAdmin\MediaLibrary
 */
class MediaLibraryMultipleFile extends MultipleFile
{
    use MediaLibraryBase;

    protected $view = 'admin::form.multiplefile';

    public function fill($data)
    {
        parent::fill($data);

        $value = $this->form->model()->getMedia($this->column());

        foreach ($value as $key => $media) {
            $this->value[$key] = $media->id;
        }
    }

    public function setOriginal($data)
    {
        $value = $this->form->model()->getMedia($this->column());

        foreach ($value as $key => $media) {
            $this->original[$key] = $media->id;
        }
    }

    public function destroy($key)
    {
        $files = $this->original ?: [];

        foreach ($files as $fileKey => $file) {
            if ($file == $key) {
                $media = Media::whereId($key)->first();
                $media->delete();
            }
        }

        return array_values($files);
    }

    protected function prepareForeach(UploadedFile $file = null)
    {
        $this->name = $this->getStoreName($file);

        return $this->uploadMedia($file);
    }

    protected function initialPreviewConfig()
    {
        $medias = Media::whereIn('id', $this->value ?: [])->orderBy('order_column')->get();

        $config = [];
        foreach ($medias as $media) {
            $config[] = $this->getPreviewEntry($media);
        }

        return $config;
    }
}
