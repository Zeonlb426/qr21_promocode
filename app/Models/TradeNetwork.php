<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;

class TradeNetwork extends \Eloquent implements Sortable
{
    use HasFactory, SortableTrait;

    protected $casts = [
        'instruction_questions' => 'array',
        'quiz_answers' => 'array',
        'order' => 'array',
    ];

    public $sortable = [
        'order_column_name' => 'sort',
        'sort_when_creating' => true,
    ];

    protected $attributes = [
        'product_id' => 1,
    ];

    protected $fillable = [
        'status',
        'name',
        'url',
        'title',
        'sub_title',
        'type_promocode_id',
        'instruction_title',
        'instruction_questions',
        'show_instruction',
        'product_id',
        'quiz_show',
        'quiz_own_answer',
        'quiz_type_answers',
        'quiz_question',
        'quiz_answers',
        'order',
        'send_status',
        'sort',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function typePromocode(): BelongsTo
    {
        return $this->belongsTo(TypePromocode::class);
    }

    public function products(): MorphToMany
    {
        return $this->morphToMany(Product::class, 'productable', 'productable')
            ->where('status', '=', true)
        ;
    }

}
