<?php

declare(strict_types=1);

namespace Nicklasos\LaravelAdmin\MediaLibrary\Http\Controllers;

use Illuminate\Routing\Controller;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * Class MediaLibraryController
 * @package Nicklasos\LaravelAdmin\MediaLibrary\Http\Controllers
 */
class MediaLibraryController extends Controller
{
    /**
     * @param $id
     * @return \Spatie\MediaLibrary\MediaCollections\Models\Media
     */
    public function download($id): Media
    {
        return Media::findOrFail($id);
    }
}
