<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property-read \App\Models\TradeNetwork $resource
 *
 * @OA\Schema(
 *     schema="ProductResource",
 *     @OA\Property(property="id", type="string", example="34234"),
 *     @OA\Property(property="name", type="string", example="Пятёрочка"),
 *     @OA\Property(property="shortName", type="string", example="Пятёрочка"),
 *     @OA\Property(property="title", type="string", example="Пятёрочка"),
 *     @OA\Property(property="subTitle", type="string", example="Пятёрочка"),
 *     @OA\Property(property="label", type="string", example="4 пачки"),
 *     @OA\Property(property="sort", type="integer", example="2"),
 *     @OA\Property(property="src", type="string", example="https://dddd.com/"),
 * )
 *
 * Class ProductResource
 *
 * @package App\Http\Resources
 */
final class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array
     */
    public function toArray($request): array
    {
        $media = \optional($this->resource->imageMedia);
        return [
            'id' => $this->resource->id,
            'name' => $this->resource->name,
            'shortName' => $this->resource->short_name,
            'title' => $this->resource->title,
            'subTitle' => $this->resource->sub_title,
            'label' => $this->resource->label,
            'sort' => $this->resource->sort,
            'src' => $media->getFullUrl(),
        ];
    }
}
