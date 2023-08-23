<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\File;
use Spatie\MediaLibrary\MediaCollections\Models\Media;


class Product extends \Eloquent  implements HasMedia, Sortable
{
    use HasFactory, InteractsWithMedia, SortableTrait;

    public $sortable = [
        'order_column_name' => 'sort',
        'sort_when_creating' => true,
    ];

    protected $casts = [
        'order' =>'json',
    ];

    public $timestamps = false;

    public const MEDIA_COLLECTION_IMAGE = 'image';

    protected $fillable = [
        'name',
        'short_name',
        'status',
        'title',
        'sub_title',
        'label',
        'sort',
    ];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection(self::MEDIA_COLLECTION_IMAGE)
            ->acceptsFile(fn(File $file) => \str_contains($file->mimeType, 'image'))
            ->singleFile()
        ;
    }

    public function setImageAttribute(): void
    {
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphOne
     */
    public function imageMedia(): MorphOne
    {
        return $this
            ->morphOne(Media::class, 'model')
            ->where('collection_name', '=', self::MEDIA_COLLECTION_IMAGE)
        ;
    }

    public function setProductsAttribute(): void
    {
    }

    public function tradeNetworks(): MorphToMany
    {
        return $this->morphedByMany(TradeNetwork::class, 'productable', 'productable')
            ->where('status', '=', true)
        ;
    }

}
