<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use function PHPUnit\Framework\isEmpty;

/**
 * @property-read \App\Models\TradeNetwork $resource
 *
 * @OA\Schema(
 *     schema="TradeNetworkResource",
 *     @OA\Property(property="id", type="string", example="34234"),
 *     @OA\Property(property="name", type="string", example="Пятёрочка"),
 *     @OA\Property(property="url", type="string", example="kbreg.ploom.ru"),
 *     @OA\Property(property="title", type="string", example="Предъявите ваш код продавцу в магазине Пятёрочка"),
 *     @OA\Property(property="subTitle", type="string|null", example="null"),
 *     @OA\Property(
 *          property="instruction",
 *          type="object",
 *          @OA\Property(property="title", type="string", example="В случае возникновения проблем в магазине"),
 *          @OA\Property(property="content", type="string", example="[Отсканировать товар,Нажать кнопку,Произвести расчет]"),
 *          @OA\Property(property="active", type="string", example="true"),
 *     ),
 *     @OA\Property(
 *          property="promocode",
 *          type="object",
 *          @OA\Property(property="id", type="integer", example="2"),
 *          @OA\Property(property="name", type="string", example="QR-код"),
 *          @OA\Property(property="shortName", type="string", example="qr"),
 *     ),
 *     @OA\Property(
 *          property="order",
 *          type="object",
 *          @OA\Property(property="1", type="integer", example="2"),
 *          @OA\Property(property="2", type="string", example="4"),
 *          @OA\Property(property="3", type="string", example="5"),
 *     ),
 *     @OA\Property(
 *         property="product",
 *         type="array",
 *         @OA\Items(ref="#/components/schemas/ProductResource"),
 *     ),
 *     @OA\Property(
 *          property="quiz",
 *          type="object",
 *          @OA\Property(property="active", type="string", example="true"),
 *          @OA\Property(property="haveOwnAnswer", type="string", example="true"),
 *          @OA\Property(property="typeAnswers", type="string", example="radio", description="Варианта два: radio, checkbox"),
 *          @OA\Property(property="question", type="string", example="Текст с вопроса ..."),
 *          @OA\Property(property="answers", type="string", example="[Ответ 1, Ответ 2, Ответ 3]"),
 *     ),
 * )
 *
 * Class TradeNetworkResource
 *
 * @package App\Http\Resources
 */
final class TradeNetworkResource extends JsonResource
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
        return [
            'id' => $this->resource->id,
            'name' => $this->resource->name,
            'url' => $this->resource->url,
            'title' => $this->resource->title,
            'subTitle' => $this->resource->sub_title,
            'promocode' => [
                'id' => $this->resource->typePromocode->id,
                'name' => $this->resource->typePromocode->name,
                'shortName' => $this->resource->typePromocode->short_name,
            ],
            'instruction' => [
                'title' => $this->resource->instruction_title,
                'content' => $this->resource->instruction_questions,
                'active' => $this->resource->show_instruction,
            ],
            'quiz' => [
                'active' => $this->resource->quiz_show,
                'haveOwnAnswer' => $this->resource->quiz_own_answer,
                'typeAnswers' => $this->resource->quiz_type_answers,
                'question' => $this->resource->quiz_question,
                'answers' => $this->resource->quiz_answers,
            ],
            'order' => $this->resource->order ? (object)$this->resource->order : null,
            'product' => ProductResource::collection($this->resource->products),
        ];
    }
}
