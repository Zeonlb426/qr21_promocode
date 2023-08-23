<?php

declare(strict_types=1);

namespace App\DataTransfersObject;

use Spatie\DataTransferObject\DataTransferObject;

final class ResponseDataBarcodeDTO extends DataTransferObject
{
    public string $type;

    public string $promocode;

    public string $content;

    public \DateTime $date;

    public int $timestamp;
}
