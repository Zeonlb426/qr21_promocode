<?php

declare(strict_types=1);

namespace App\DataTransfersObject;

use App\Models\User;
use Mindbox\DTO\ResultDTO;
use Spatie\DataTransferObject\DataTransferObject;

final class ResponseDataCustomerDTO extends DataTransferObject
{
    /**
     * @var string
     */
    public string $status;

    /**
     * @var int
     */
    public int $customerId;

    /**
     * @var string|null
     */
    public ?string $email;

    /**
     * @var string|null
     */
    public ?string $firstName;

    /**
     * @var string|null
     */
    public ?string $lastName;

    /**
     * @var string|null
     */
    public ?string $birthDate;

    /**
     * @var string|null
     */
    public ?string $city;

    /**
     * @var int|null
     */
    public ?int $tradeNetworkId;

    /**
     * @var int|null
     */
    public ?int $productId;

    /**
     * @var string
     */
    public string $token;

    public static function fromMindbox(ResultDTO $mindboxResultDTO, User $user, string $token): self
    {
        return new self([
            'status' => $mindboxResultDTO->getStatus(),
            'customerId' => (int) $mindboxResultDTO->getCustomer()->getId('mindboxId'),
            'email' => $mindboxResultDTO->getCustomer()->getEmail(),
            'firstName' => $mindboxResultDTO->getCustomer()->getFirstName(),
            'lastName' => $mindboxResultDTO->getCustomer()->getLastName(),
            'birthDate' => $mindboxResultDTO->getCustomer()->getBirthDate(),
            'city' => $mindboxResultDTO->getCustomer()->getCustomField('city'),
            'tradeNetworkId' => $user->getAttribute('trade_network_id'),
            'productId' => $user->getAttribute('product_id'),
            'token' => $token,
        ]);
    }
}
