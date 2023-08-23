<?php

declare(strict_types=1);

use Nicklasos\LaravelAdmin\MediaLibrary\Http\Controllers\MediaLibraryController;

Route::get('media/download/{id}', MediaLibraryController::class . '@download')->name('admin.media.download');
