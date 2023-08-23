<?php

declare(strict_types=1);

namespace App\Models;

use Encore\Admin\Traits\DefaultDatetimeFormat;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class IdxLog extends \Eloquent
{
    use HasFactory, DefaultDatetimeFormat;

    protected $fillable = [
        'method',
        'phone',
        'params',
        'result_code',
        'result_code_text',
        'response',
        'score',
        'score_text',
        'duration',
        'url',
        'trade_network',
        'product',
    ];
}
