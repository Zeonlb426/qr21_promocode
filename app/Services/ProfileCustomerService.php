<?php

declare(strict_types=1);

namespace App\Services;

use App\DataTransfersObject\RequestDataCustomerDTO;
use App\Http\Requests\DataCustomerRequest;
use App\Models\Product;
use App\Operations\Mindbox\MindboxOperation;
use Mindbox\DTO\ResultDTO;
use Mindbox\Exceptions\MindboxClientException;

/**
 * Class ProfileCustomerService
 *
 * @package App\Services
 */
final class ProfileCustomerService
{
    /**
     * @var \App\Operations\Mindbox\MindboxOperation
     */
    private MindboxOperation $mindboxManager;

    /**
     * @param \App\Operations\Mindbox\MindboxOperation $mindboxManager
     */
    public function __construct(MindboxOperation $mindboxManager)
    {
        $this->mindboxManager = $mindboxManager;
    }

    /**
     * @param \App\Http\Requests\DataCustomerRequest $request
     *
     * @return \Mindbox\DTO\ResultDTO|null
     * @throws \Mindbox\Exceptions\MindboxClientException
     */
    public function editCustomerData(DataCustomerRequest $request): ?ResultDTO
    {
        $dataCustomer = RequestDataCustomerDTO::fromRequest($request);

        try {
            $editProfile = $this->mindboxManager->editProfileCustomer($dataCustomer);
            if ($dataCustomer->productId !== null) {
                $product = Product::where('id', '=', $dataCustomer->productId)->first();
                $editProfile = $this->mindboxManager->editProduct($dataCustomer->customerId, $product->short_name);
            }
        } catch (\Exception $exception) {
            throw new MindboxClientException($exception->getMessage(), $exception->getCode(), $exception);
        }

        if ($editProfile->getStatus() !== 'Success') {
            return null;
        }

        $user = \Auth::user();
        $user->trade_network_id = $dataCustomer->tradeNetworkId;
        $user->product_id = $dataCustomer->productId;
        $user->save();

        return $editProfile;
    }

    /**
     * @return \Mindbox\DTO\ResultDTO|null
     *
     * @throws \Mindbox\Exceptions\MindboxClientException
     */
    public function getCustomerData(): ?ResultDTO
    {
        $phone = \Auth::user()->phone;
        try {
            $customerProfile = $this->mindboxManager->getProfileCustomer($phone);
        } catch (\Exception $exception) {
            throw new MindboxClientException($exception->getMessage(), $exception->getCode(), $exception);
        }

        if ($customerProfile->getStatus() !== 'Success') {
            return null;
        }

        return $customerProfile;
    }

    /**
     * @param $request
     *
     * @return \Mindbox\DTO\ResultDTO|null
     * @throws \Mindbox\Exceptions\MindboxClientException
     */
    public function sendQuizAnswer($request): ?ResultDTO
    {
        $mindbox_id = \Auth::user()->mindbox_id;
        $answer = $request->get('answer');

        try {
            $customerProfile = $this->mindboxManager->editAnswer($mindbox_id, $answer);
        } catch (\Exception $exception) {
            throw new MindboxClientException($exception->getMessage(), $exception->getCode(), $exception);
        }

        if ($customerProfile->getStatus() !== 'Success') {
            return null;
        }

        return $customerProfile;
    }

}
