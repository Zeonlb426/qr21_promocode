<?php

declare(strict_types=1);

namespace App\DataTransfersObject;

use Carbon\Carbon;
use App\Http\Requests\DataCustomerRequest;
use Spatie\DataTransferObject\DataTransferObject;

final class RequestDataCustomerDTO extends DataTransferObject
{
    public int $customerId;

    public string $email;

    public string $firstName;

    public string $lastName;

    public string $birthDate;

    public string $city;

    public ?int $tradeNetworkId;

    public ?int $productId;

    public static function fromRequest(DataCustomerRequest $request): self
    {
        return new self([
            'customerId' => \Auth::user()->mindbox_id,
            'email' => $request->get('email'),
            'firstName' => $request->get('firstName'),
            'lastName' => $request->get('lastName'),
            'birthDate' => $request->get('birthDate'),
            'city' => $request->get('city'),
            'tradeNetworkId' => $request->get('tradeNetworkId'),
            'productId' => $request->get('productId'),
        ]);
    }

    public static function generateCarbonObject(string $birthDate): ?Carbon
    {
        return \Illuminate\Support\Carbon::createFromFormat('Y-m-d', $birthDate);
    }

}
