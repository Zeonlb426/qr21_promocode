<?php

declare(strict_types=1);

namespace App\Services;

use App\DataTransfersObject\ResponseDataCustomerDTO;
use App\Http\Requests\CheckConfirmationCodeRequest;
use Carbon\Carbon;
use App\Models\User;
use App\Http\Requests\PhoneRequest;
use App\Operations\Mindbox\MindboxOperation;
use Mindbox\DTO\V3\Responses\CustomerResponseDTO;
use Mindbox\Exceptions\MindboxClientException;
use Propaganistas\LaravelPhone\PhoneNumber;

/**
 * Class AuthorizationService
 *
 * @package App\Services
 */
final class AuthorizationService
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
     * @param \App\Http\Requests\PhoneRequest $request
     *
     * @return \Mindbox\DTO\V3\Responses\CustomerResponseDTO|null
     * @throws \Mindbox\Exceptions\MindboxClientException
     */
    public function sendCodeToPhone(PhoneRequest $request): ?CustomerResponseDTO
    {
        $phone = trim(PhoneNumber:: make($request->get('phone'), 'RU')->formatE164(), '+');

        try {

            $customer = $this->mindboxManager->getProfileCustomer($phone)->getCustomer();

            if ($customer->getProcessingStatus() === 'NotFound') {
                // При регистрации нового пользователя, смс с кодом отправляется автоматически
                $customer = $this->mindboxManager->registrationByPhone($phone)->getCustomer();
            }elseif ($customer->getIsMobilePhoneConfirmed()) {
                // Запрос кода авторизации если клиент существует и имеет подтвержденный телефон
                $sendCode = $this->mindboxManager->sendPhoneAuthCode($phone, $customer->getId('mindboxId'));
                if ($sendCode->getStatus() !== 'Success') {
                    return null;
                }
            }else {
                // Запрос кода подтверждения если клиент существует, но телефон не подтвержден
                $sendCode = $this->mindboxManager->sendPhoneConfirmationCode($phone, $customer->getId('mindboxId'));
                if ($sendCode->getStatus() !== 'Success') {
                    return null;
                }
            }
        } catch (MindboxClientException $exception) {
            throw new MindboxClientException($exception->getMessage(), $exception->getCode(), $exception);
        }

        User::updateOrCreate(
            ['phone'=> $phone],
            ['mindbox_id' => $customer->getId('mindboxId'), 'last_time_sent_code' => Carbon::now()]
        );

        return $customer;
    }

    /**
     * @param \App\Http\Requests\CheckConfirmationCodeRequest $request
     *
     * @return \App\DataTransfersObject\ResponseDataCustomerDTO|null
     * @throws \Mindbox\Exceptions\MindboxClientException
     */
    public function checkCode(CheckConfirmationCodeRequest $request): ?ResponseDataCustomerDTO
    {
        $customerId = $request->get('customerId');
        $code = $request->get('code');

        $user = User::where('mindbox_id', '=', $customerId)->first();

        if ($user === null) {
            return null;
        }

        $phone = trim($user->phone, '+');

        try {
            $customer = $this->mindboxManager->getProfileCustomer($phone);
        } catch (\Exception $exception) {
            throw new MindboxClientException($exception->getMessage(), $exception->getCode(), $exception);
        }

        try {
            if ($customer->getCustomer()->getIsMobilePhoneConfirmed()) {
                // Запрос проверки кода авторизации
                $responseCheckCode = $this->mindboxManager->checkAuthCode($customerId, $phone, $code);
                if ($responseCheckCode->getStatus() !== 'Success') {
                    return null;
                }
            }else {
                // Запрос проверки кода регистрации
                $responseCheckCode = $this->mindboxManager->checkConfirmationCode($customerId, $phone, $code);
                if ($responseCheckCode->getStatus() !== 'Success') {
                    return null;
                }
                $processingStatus = $responseCheckCode->getSmsConfirmation()->getProcessingStatus();
                if (in_array($processingStatus, ['IncorrectConfirmationCode', 'NotFound']) || null === $processingStatus) {
                    return null;
                }
            }
        } catch (\Exception $exception) {
            return null;
//                throw new MindboxClientException($exception->getMessage(), $exception->getCode(), $exception);
        }


        $user->trade_network_id = null;
        $user->product_id = null;
        $user->save();

        $token = $user->createToken('id'.$customerId)->plainTextToken;

        return ResponseDataCustomerDTO::fromMindbox($customer, $user, $token);
    }
}
