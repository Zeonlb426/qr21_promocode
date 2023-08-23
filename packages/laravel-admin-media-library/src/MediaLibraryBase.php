<?php

declare(strict_types=1);

namespace Nicklasos\LaravelAdmin\MediaLibrary;

use Encore\Admin\Form\NestedForm;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Trait MediaLibraryBase
 * @package Nicklasos\LaravelAdmin\MediaLibrary
 */
trait MediaLibraryBase
{
    private bool $responsive = false;

    public function responsive(): self
    {
        $this->responsive = true;

        return $this;
    }

    public function uploadMedia(UploadedFile $file = null)
    {
        $model = $this->form->model();
        if (null === $model->getKey()) {
            $done = false;
            $model::saved(function (HasMedia $savedModel) use ($file, $model, &$done) {
                if (!$done && null !== $model->getKey() && spl_object_id($savedModel) === spl_object_id($model)) {
                    $done = true;
                    $media = $model
                        ->addMedia($file)
                        ->usingName($this->name)
                        ->usingFileName($this->name)
                        ->preservingOriginal();
                    if ($this->responsive) {
                        $media->withResponsiveImages();
                    }
                    $media->toMediaCollection($this->column());
                }
            });
            return null;
        }

        $media = $model
            ->addMedia($file)
            ->usingName($this->name)
            ->usingFileName($this->name)
            ->preservingOriginal();

        if ($this->responsive) {
            $media->withResponsiveImages();
        }

        $media = $media
            ->toMediaCollection($this->column())
            ->toArray();

        $media[NestedForm::REMOVE_FLAG_NAME] = 0;
        return tap($media, function () {
            $this->name = null;
        });
    }

    /**
     * @param $mediaId
     *
     * @return string
     */
    public function objectUrl($mediaId): string
    {
        /**
         * @var $model Media
         */
        $model = Media::whereId($mediaId)->first();

        return $model->getFullUrl();
    }

    private function getPreviewEntry(Media $media)
    {
        $ext = pathinfo($media->getFullUrl(), PATHINFO_EXTENSION);

        $type = $this->getMimeType($media->mime_type);

        $entry = [
            'caption' => $media->file_name,
            'key' => $media->id,
            'size' => $media->size,
        ];

        if (!empty($type)) {
            $entry['type'] = $type;
            if ($type == 'video' || $type == 'audio') {
                $entry['filetype'] = "{$type}/{$ext}";
            }
        }

        return $entry;
    }

    private function getMimeType(string $mime): string
    {
        switch ($mime) {
            case 'image/jpeg':
            case 'image/png':
                $type = 'image';
                break;
            case 'application/pdf':
                $type = 'pdf';
                break;
            case 'text/plain':
                $type = 'text';
                break;
            case 'application/msword':
            case 'application/vnd.openxmlformats-officedocument.wordprocessingml.document':
            case 'application/vnd.ms-excel':
            case 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet':
            case 'application/vnd.ms-powerpoint':
            case 'application/vnd.openxmlformats-officedocument.presentationml.presentation':
                $type = 'office';
                break;
            case 'image/tiff':
                $type = 'gdocs';
                break;
            case 'text/html':
                $type = 'html';
                break;
            case 'video/mp4':
            case 'application/mp4':
            case 'video/x-sgi-movie':
                $type = 'video';
                break;
            case 'audio/mpeg':
            case 'audio/mp3':
                $type = 'audio';
                break;

            default:
                $type = 'image';
        }

        return $type;
    }
}
