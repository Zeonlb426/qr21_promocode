<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\DataCustomerRequest;
use App\Services\ProfileCustomerService;
use Illuminate\Http\JsonResponse;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Response;

final class ProfileCustomerController extends Controller
{
    /**
     * @var \App\Services\ProfileCustomerService
     */
    private ProfileCustomerService $service;

    /**
     * @param \App\Services\ProfileCustomerService $service
     */
    public function __construct(ProfileCustomerService $service)
    {
        $this->service = $service;
    }

    /**
     * @OA\Post(
     *     path="/api/customer/update",
     *     tags={"Profile"},
     *     summary="Редактирование данных пользователя",
     *     description="Отправка обновленных данных пользователя в Mindbox",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"firstName", "lastName", "email", "birthDate", "city", "tradeNetworkId", "productId"},
     *             @OA\Property(property="firstName", example="Сергей", type="string", maxLength=32),
     *             @OA\Property(property="lastName", example="Петров", type="string", maxLength=32),
     *             @OA\Property(property="email", example="sp@email.com", type="email"),
     *             @OA\Property(property="birthDate", example="1995-02-05", type="string"),
     *             @OA\Property(property="city", type="string", example="Вологда", maxLength=32),
     *             @OA\Property(property="tradeNetworkId", example="2", type="integer", description="ID Торговой сети"),
     *             @OA\Property(property="productId", example="3", type="integer", description="ID продукта"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Данные успешно обновлены",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="Success", description="Статус отправки данных"),
     *             @OA\Property(property="firstName", example="Сергей", type="string", maxLength=32),
     *             @OA\Property(property="lastName", example="Петров", type="string", maxLength=32),
     *             @OA\Property(property="email", example="sp@email.com", type="email"),
     *             @OA\Property(property="birthDate", example="1995-02-05", type="string"),
     *             @OA\Property(property="city", type="string", example="Вологда", maxLength=32),
     *             @OA\Property(property="tradeNetworkId", example="2", type="integer", description="ID Торговой сети"),
     *             @OA\Property(property="productId", example="3", type="integer", description="ID продукта"),
     *         )
     *     ),
     *     @OA\Response(response=422, ref="#/components/responses/error:validation"),
     *     @OA\Response(response=424, description="Неудалось записать данные",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Неудалось записать данные", description="Неудалось записать данные")
     *         ),
     *      ),
     *      @OA\Response(response=502, description="Cервер в процессе обработки запроса, получил недопустимый ответ",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Http client error", description="Http client error")
     *         ),
     *      ),
     * )
     *
     * @param \App\Http\Requests\DataCustomerRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateCustomerData(DataCustomerRequest $request): JsonResponse
    {
        try {
            $editProfile = $this->service->editCustomerData($request);
        }catch (\Exception $exception){
            return \response()->json(['message' => $exception->getMessage()])
                ->setStatusCode(Response::HTTP_BAD_GATEWAY);
        }

        if (null === $editProfile) {
            return \response()->json(['message' => 'Неудалось записать данные'])
                ->setStatusCode(Response::HTTP_FAILED_DEPENDENCY);
        }

        return $this->extracted($editProfile);
    }

    /**
     * @OA\Post(
     *     path="/api/customer/info",
     *     tags={"Profile"},
     *     summary="Получение данных о пользователе",
     *     description="Получение данных о пользователе из Mindbox",
     *     @OA\Response(
     *         response=201,
     *         description="Данные успешно получены",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="Success", description="Статус получения данных"),
     *             @OA\Property(property="customerId", example="423456", type="integer", description="ID пользователя"),
     *             @OA\Property(property="firstName", example="Сергей", type="string", description="Имя пользователя"),
     *             @OA\Property(property="lastName", example="Петров", type="string", description="Фамилия пользователя"),
     *             @OA\Property(property="email", example="sp@email.com", type="string", description="Почта пользователя"),
     *             @OA\Property(property="phone", example="+79005674534", type="string", description="Телефон пользователя"),
     *             @OA\Property(property="birthDate", example="1995-02-05", type="string", description="Дата рождения пользователя"),
     *             @OA\Property(property="city", example="Вологда", type="string", description="Город пользователя"),
     *             @OA\Property(property="tradeNetworkId", example="2", type="integer", description="ID Торговой сети"),
     *             @OA\Property(property="productId", example="3", type="integer", description="ID продукта"),
     *         )
     *     ),
     *     @OA\Response(response=422, ref="#/components/responses/error:validation"),
     *     @OA\Response(response=424, description="Неудалось получить данные",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Неудалось получить данные", description="Неудалось получить данные")
     *         ),
     *      ),
     *      @OA\Response(response=502, description="Cервер в процессе обработки запроса, получил недопустимый ответ",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Http client error", description="Http client error")
     *         ),
     *      ),
     * )
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCustomerData(): JsonResponse
    {
        try {
            $customerData = $this->service->getCustomerData();
        }catch (\Exception $exception){
            return \response()->json(['message' => $exception->getMessage()])
                ->setStatusCode(Response::HTTP_BAD_GATEWAY);
        }
        if (null === $customerData) {
            return \response()->json(['message' => 'Неудалось получить данные'])
                ->setStatusCode(Response::HTTP_FAILED_DEPENDENCY);
        }

        return $this->extracted($customerData);
    }

    /**
     * @param \Mindbox\DTO\ResultDTO $customerData
     * @return \Illuminate\Http\JsonResponse|object
     */
    public function extracted(\Mindbox\DTO\ResultDTO $customerData)
    {
        return \response()->json([
                'status' => $customerData->getStatus(),
                'customerId' => $customerData->getCustomer()->getId('mindboxId'),
                'firstName' => $customerData->getCustomer()->getFirstName(),
                'lastName' => $customerData->getCustomer()->getLastName(),
                'email' => $customerData->getCustomer()->getEmail(),
                'phone' => $customerData->getCustomer()->getMobilePhone(),
                'birthDate' => $customerData->getCustomer()->getBirthDate(),
                'city' => $customerData->getCustomer()->getCustomField('city'),
                'tradeNetworkId' => \Auth::user()->getAttribute('trade_network_id'),
                'productId' => \Auth::user()->getAttribute('product_id'),
            ]
        )->setStatusCode(Response::HTTP_CREATED);
    }
}
