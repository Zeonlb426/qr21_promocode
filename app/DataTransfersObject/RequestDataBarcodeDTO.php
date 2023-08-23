<?php

declare(strict_types=1);

namespace App\DataTransfersObject;

use App\Http\Requests\BarcodeRequest;
use Spatie\DataTransferObject\DataTransferObject;

final class RequestDataBarcodeDTO extends DataTransferObject
{
    public int $tradeNetworkId;

    public int $productId;

    public static function fromRequest(BarcodeRequest $request): self
    {
        return new self([
            'tradeNetworkId' => $request->get('tradeNetworkId'),
            'productId' => $request->get('productId'),
        ]);
    }
}
