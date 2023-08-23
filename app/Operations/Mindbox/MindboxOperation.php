<?php

declare(strict_types=1);

namespace App\Operations\Mindbox;

use App\DataTransfersObject\RequestDataCustomerDTO;
use Mindbox\DTO\ResultDTO;
use Mindbox\DTO\V3\OperationDTO;
use Mindbox\Loggers\MindboxFileLogger;
use Mindbox\Mindbox;
use Psr\Log\LogLevel;

/**
 * Class MindboxOperation
 * @package App\Services\Mindbox
 */
final class MindboxOperation
{
    /**
     * @var \Mindbox\Mindbox
     */
    private Mindbox $mindbox;

    private string $pointOfContact;

    public function __construct()
    {
        $pathToLogs = storage_path('logs/mindbox/');
//        $logger = new MindboxFileLogger($pathToLogs, LogLevel::DEBUG);
        $logger = new MindboxFileLogger($pathToLogs);

        $endpointId = \config('mindbox_endpoint', 'jti-ip.ploom.reg.test');
        $secretKey = \config('mindbox_key', 'B9WrlXhArGJP0vGnrC3G');
        $mindboxUrl = \config('mindbox_url', 'https://api.mindbox.ru/v3/operations/');
        $this->pointOfContact = \config('point_of_contact', 'dev.121reg.ru/qr');

        $this->mindbox = new Mindbox(
            [
                'endpointId'   => $endpointId,
                'secretKey'    => $secretKey,
                'domain'       => $mindboxUrl,
                'domainZone'       => 'ru',
            ],
            $logger
        );
    }

    /**
     * @param $phone
     * @return \Mindbox\DTO\ResultDTO
     * @throws \Mindbox\Exceptions\MindboxBadRequestException
     * @throws \Mindbox\Exceptions\MindboxClientException
     * @throws \Mindbox\Exceptions\MindboxConflictException
     * @throws \Mindbox\Exceptions\MindboxForbiddenException
     * @throws \Mindbox\Exceptions\MindboxNotFoundException
     * @throws \Mindbox\Exceptions\MindboxTooManyRequestsException
     * @throws \Mindbox\Exceptions\MindboxUnauthorizedException
     * @throws \Mindbox\Exceptions\MindboxUnavailableException
     */
    public function getProfileCustomer($phone): ResultDTO
    {
        $customer = new \Mindbox\DTO\V3\Requests\CustomerRequestDTO();
        $customer->setMobilePhone($phone);

        // Получение информации о пользователе по номеру телефона
        return $this->mindbox->customer()
            ->checkByPhone($customer, 'ploom.121reg.getCustomerInfo', false)
            ->sendRequest()
            ->getResult()
        ;
    }

    /**
     * @param $phone
     * @return \Mindbox\DTO\ResultDTO
     * @throws \Mindbox\Exceptions\MindboxBadRequestException
     * @throws \Mindbox\Exceptions\MindboxClientException
     * @throws \Mindbox\Exceptions\MindboxConflictException
     * @throws \Mindbox\Exceptions\MindboxForbiddenException
     * @throws \Mindbox\Exceptions\MindboxNotFoundException
     * @throws \Mindbox\Exceptions\MindboxTooManyRequestsException
     * @throws \Mindbox\Exceptions\MindboxUnauthorizedException
     * @throws \Mindbox\Exceptions\MindboxUnavailableException
     */
    public function registrationByPhone($phone): ResultDTO
    {
        $body = new \Mindbox\DTO\DTO([
            'customer'=>[
                'mobilePhone' => $phone,
            ],
            'pointOfContact' => $this->pointOfContact
        ]);
        return $this->mindbox->getClientV3()
            ->prepareRequest('POST', 'ploom.121reg.regMobilePhone', $body, '', [], true, false)
            ->sendRequest()
            ->getResult()
        ;
    }

    /**
     * @param $phone
     * @param $customerId
     * @return \Mindbox\DTO\ResultDTO
     * @throws \Mindbox\Exceptions\MindboxBadRequestException
     * @throws \Mindbox\Exceptions\MindboxClientException
     * @throws \Mindbox\Exceptions\MindboxConflictException
     * @throws \Mindbox\Exceptions\MindboxForbiddenException
     * @throws \Mindbox\Exceptions\MindboxNotFoundException
     * @throws \Mindbox\Exceptions\MindboxTooManyRequestsException
     * @throws \Mindbox\Exceptions\MindboxUnauthorizedException
     * @throws \Mindbox\Exceptions\MindboxUnavailableException
     */
    public function sendPhoneConfirmationCode($phone, $customerId): ResultDTO
    {
        $body = new \Mindbox\DTO\DTO([
            'customer'=>[
                'mobilePhone' => $phone,
            ],
            'pointOfContact' => $this->pointOfContact
        ]);
        return $this->mindbox->getClientV3()
            ->prepareRequest('POST', 'ploom.121reg.sendPhoneConfirmationCode', $body, '', [], true, false)
            ->sendRequest()
            ->getResult()
        ;
    }

    /**
     * @param $phone
     * @param $customerId
     * @return \Mindbox\DTO\ResultDTO
     * @throws \Mindbox\Exceptions\MindboxBadRequestException
     * @throws \Mindbox\Exceptions\MindboxClientException
     * @throws \Mindbox\Exceptions\MindboxConflictException
     * @throws \Mindbox\Exceptions\MindboxForbiddenException
     * @throws \Mindbox\Exceptions\MindboxNotFoundException
     * @throws \Mindbox\Exceptions\MindboxTooManyRequestsException
     * @throws \Mindbox\Exceptions\MindboxUnauthorizedException
     * @throws \Mindbox\Exceptions\MindboxUnavailableException
     */
    public function sendPhoneAuthCode($phone, $customerId): ResultDTO
    {
        $body = new \Mindbox\DTO\DTO([
            'customer'=>[
                'mobilePhone' => $phone,
            ],
            'pointOfContact' => $this->pointOfContact
        ]);
        return $this->mindbox->getClientV3()
            ->prepareRequest('POST', 'ploom.121reg.requestAuthCode', $body, '', [], true, false)
            ->sendRequest()
            ->getResult()
        ;
    }

    /**
     * @param $customerId
     * @param $phone
     * @param $code
     * @return \Mindbox\DTO\ResultDTO
     * @throws \Mindbox\Exceptions\MindboxBadRequestException
     * @throws \Mindbox\Exceptions\MindboxClientException
     * @throws \Mindbox\Exceptions\MindboxConflictException
     * @throws \Mindbox\Exceptions\MindboxForbiddenException
     * @throws \Mindbox\Exceptions\MindboxNotFoundException
     * @throws \Mindbox\Exceptions\MindboxTooManyRequestsException
     * @throws \Mindbox\Exceptions\MindboxUnauthorizedException
     * @throws \Mindbox\Exceptions\MindboxUnavailableException
     */
    public function checkConfirmationCode($customerId, $phone, $code): ResultDTO
    {
        $body = new \Mindbox\DTO\DTO([
            'smsConfirmation'=>[
                'code' => $code
            ],
            'customer'=>[
                'mobilePhone' => $phone,
                'ids'=>[
                    'mindboxId'=>$customerId,
                ],
            ],
            'pointOfContact' => $this->pointOfContact
        ]);
        return $this->mindbox->getClientV3()
            ->prepareRequest('POST', 'ploom.121reg.checkPhoneConfirmationCode', $body, '', [], true, false)
            ->sendRequest()
            ->getResult()
        ;
    }

    /**
     * @param $customerId
     * @param $phone
     * @param $code
     * @return \Mindbox\DTO\ResultDTO
     * @throws \Mindbox\Exceptions\MindboxBadRequestException
     * @throws \Mindbox\Exceptions\MindboxClientException
     * @throws \Mindbox\Exceptions\MindboxConflictException
     * @throws \Mindbox\Exceptions\MindboxForbiddenException
     * @throws \Mindbox\Exceptions\MindboxNotFoundException
     * @throws \Mindbox\Exceptions\MindboxTooManyRequestsException
     * @throws \Mindbox\Exceptions\MindboxUnauthorizedException
     * @throws \Mindbox\Exceptions\MindboxUnavailableException
     */
    public function checkAuthCode($customerId, $phone, $code): ResultDTO
    {
        $body = new \Mindbox\DTO\DTO([
            'customer'=>[
                'mobilePhone' => $phone,
                'ids'=>[
                    'mindboxId'=>$customerId,
                ],
            ],
            'authentificationCode'=>$code,
            'pointOfContact' => $this->pointOfContact,
        ]);
        return $this->mindbox->getClientV3()
            ->prepareRequest('POST', 'ploom.121reg.checkMobileAuthCode', $body, '', [], true, false)
            ->sendRequest()
            ->getResult()
        ;
    }

    /**
     * @param \App\DataTransfersObject\RequestDataCustomerDTO $dataCustomer
     * @return \Mindbox\DTO\ResultDTO
     * @throws \Mindbox\Exceptions\MindboxBadRequestException
     * @throws \Mindbox\Exceptions\MindboxClientException
     * @throws \Mindbox\Exceptions\MindboxConflictException
     * @throws \Mindbox\Exceptions\MindboxForbiddenException
     * @throws \Mindbox\Exceptions\MindboxNotFoundException
     * @throws \Mindbox\Exceptions\MindboxTooManyRequestsException
     * @throws \Mindbox\Exceptions\MindboxUnauthorizedException
     * @throws \Mindbox\Exceptions\MindboxUnavailableException
     */
    public function editProfileCustomer(RequestDataCustomerDTO $dataCustomer): ResultDTO
    {
        $customer = new \Mindbox\DTO\V3\Requests\CustomerRequestDTO();
        $customer->setId('mindboxId', $dataCustomer->customerId);
        $customer->setEmail(\mb_strtolower($dataCustomer->email));
        $customer->setFirstName($dataCustomer->firstName);
        $customer->setLastName($dataCustomer->lastName);
        $customer->setBirthDate($dataCustomer->birthDate);
        $customer->setCustomFields(['city' => $dataCustomer->city]);

        // Отправка данных пользователя в Mindbox
        return $this->mindbox->customer()
            ->edit($customer, 'ploom.121reg.editCustomerProfile', false)
            ->sendRequest()
            ->getResult()
        ;
    }

    /**
     * @param $customer_id
     * @param $short_name
     * @return \Mindbox\DTO\ResultDTO
     * @throws \Mindbox\Exceptions\MindboxBadRequestException
     * @throws \Mindbox\Exceptions\MindboxClientException
     * @throws \Mindbox\Exceptions\MindboxConflictException
     * @throws \Mindbox\Exceptions\MindboxForbiddenException
     * @throws \Mindbox\Exceptions\MindboxNotFoundException
     * @throws \Mindbox\Exceptions\MindboxTooManyRequestsException
     * @throws \Mindbox\Exceptions\MindboxUnauthorizedException
     * @throws \Mindbox\Exceptions\MindboxUnavailableException
     */
    public function editProduct($customer_id, $short_name): ResultDTO
    {
        $body = new \Mindbox\DTO\DTO([
            'customer'=>[
                'ids'=>[
                    'mindboxId'=>$customer_id,
                ],
                'customFields'=>[
                    'oneToOneRegSelectedProduct' => $short_name,
                ],
            ],
            'customerAction'=>[
                'customFields'=>[
                    'oneToOneRegSelectedProduct' => $short_name,
                ],
            ],
        ]);
        return $this->mindbox->getClientV3()
            ->prepareRequest('POST', 'ploom.121reg.editCustomerProfile', $body, '', [], true, false)
            ->sendRequest()
            ->getResult()
        ;
    }

//    /**
//     * @param $customerId
//     * @param $fieldsArray
//     * @return \Mindbox\DTO\ResultDTO
//     * @throws \Mindbox\Exceptions\MindboxBadRequestException
//     * @throws \Mindbox\Exceptions\MindboxClientException
//     * @throws \Mindbox\Exceptions\MindboxConflictException
//     * @throws \Mindbox\Exceptions\MindboxForbiddenException
//     * @throws \Mindbox\Exceptions\MindboxNotFoundException
//     * @throws \Mindbox\Exceptions\MindboxTooManyRequestsException
//     * @throws \Mindbox\Exceptions\MindboxUnauthorizedException
//     * @throws \Mindbox\Exceptions\MindboxUnavailableException
//     */
//    public function setCustomField($customerId, $fieldsArray): ResultDTO
//    {
//        $customer = new \Mindbox\DTO\V3\Requests\CustomerRequestDTO();
//        $customer->setId('mindboxId', $customerId);
//        $customer->setCustomFields($fieldsArray);
//
//        // Отправка данных пользователя
//        return $this->mindbox->customer()
//            ->edit($customer, 'ploom.v3.reg.editCustomerProfile', false)
//            ->sendRequest()
//            ->getResult()
//        ;
//    }

    /**
     * @param $phone
     * @param $promocode
     * @param $salt
     * @return \Mindbox\DTO\ResultDTO
     * @throws \Mindbox\Exceptions\MindboxBadRequestException
     * @throws \Mindbox\Exceptions\MindboxClientException
     * @throws \Mindbox\Exceptions\MindboxConflictException
     * @throws \Mindbox\Exceptions\MindboxForbiddenException
     * @throws \Mindbox\Exceptions\MindboxNotFoundException
     * @throws \Mindbox\Exceptions\MindboxTooManyRequestsException
     * @throws \Mindbox\Exceptions\MindboxUnauthorizedException
     * @throws \Mindbox\Exceptions\MindboxUnavailableException
     */
    public function sendSmsPromocode($phone, $promocode, $salt): ResultDTO
    {
        $body = new \Mindbox\DTO\DTO([
            'customer'=>[
                'mobilePhone'=>$phone,
            ],
            'smsMailing'=>[
                'customParameters'=>[
                    'promoCode'=>$promocode,
                    'shortUrl'=>\URL::to('/' . $salt),
                ],
            ],
        ]);

        // Отправка смс с промокодом на номер телефона пользователя
        return $this->mindbox->getClientV3()
            ->prepareRequest('POST', 'ploom.v3.reg.sendSMSwithPromoCode', $body, '', [], true, false)
            ->sendRequest()
            ->getResult()
        ;
    }

    /**
     * @param $customer_id
     * @param $answer
     * @return \Mindbox\DTO\ResultDTO
     * @throws \Mindbox\Exceptions\MindboxBadRequestException
     * @throws \Mindbox\Exceptions\MindboxClientException
     * @throws \Mindbox\Exceptions\MindboxConflictException
     * @throws \Mindbox\Exceptions\MindboxForbiddenException
     * @throws \Mindbox\Exceptions\MindboxNotFoundException
     * @throws \Mindbox\Exceptions\MindboxTooManyRequestsException
     * @throws \Mindbox\Exceptions\MindboxUnauthorizedException
     * @throws \Mindbox\Exceptions\MindboxUnavailableException
     */
    public function editAnswer($customer_id, $answer): ResultDTO
    {

        $body = new \Mindbox\DTO\DTO([
            'customer'=>[
                'ids'=>[
                    'mindboxId'=>$customer_id,
                ],
            ],
            'customerAction'=>[
                'customFields'=>[
                    'oneToOneHowDoYouKnow' => $answer,
                ],
            ],
        ]);
        return $this->mindbox->getClientV3()
            ->prepareRequest('POST', 'ploom.121reg.editCustomerProfile', $body, '', [], true, false)
            ->sendRequest()
            ->getResult()
        ;
    }


    /**
     * @param $customerMindboxId
     * @param $resultVerifyAge
     * @return \Mindbox\DTO\ResultDTO
     * @throws \Mindbox\Exceptions\MindboxBadRequestException
     * @throws \Mindbox\Exceptions\MindboxClientException
     * @throws \Mindbox\Exceptions\MindboxConflictException
     * @throws \Mindbox\Exceptions\MindboxForbiddenException
     * @throws \Mindbox\Exceptions\MindboxNotFoundException
     * @throws \Mindbox\Exceptions\MindboxTooManyRequestsException
     * @throws \Mindbox\Exceptions\MindboxUnauthorizedException
     * @throws \Mindbox\Exceptions\MindboxUnavailableException
     */
    public function sendVerifyAge($customerMindboxId, $resultVerifyAge): ResultDTO
    {

        $body = new \Mindbox\DTO\DTO([
            'customer'=>[
                'ids'=>[
                    'mindboxId'=>$customerMindboxId,
                ],
                'customFields' => [
                    'idxVerifyPhoneOperationToken' => $resultVerifyAge->operationToken,
                    'idxVerifyPhoneStatus' => $resultVerifyAge->score == 100,
                ],
            ]
        ]);
        return $this->mindbox->getClientV3()
            ->prepareRequest('POST', 'ploom.121reg.editCustomerProfile', $body, '', [], true, false)
            ->sendRequest()
            ->getResult()
        ;
    }
}
