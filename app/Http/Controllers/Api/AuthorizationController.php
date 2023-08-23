<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CheckConfirmationCodeRequest;
use App\Http\Requests\PhoneRequest;
use App\Services\AuthorizationService;
use Illuminate\Http\JsonResponse;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Response;

final class AuthorizationController extends Controller
{

    /**
     * @var \App\Services\AuthorizationService
     */
    private AuthorizationService $service;

    /**
     * @param \App\Services\AuthorizationService $service
     */
    public function __construct(AuthorizationService $service)
    {
        $this->service = $service;
    }
    /**
     * @OA\Post(
     *     path="/api/send/code",
     *     tags={"Authorization"},
     *     summary="Отправка смс на указаный номер телефона",
     *     description="Отправка смс на указаный номер телефона",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"phone"},
     *             @OA\Property(property="phone", type="string", example="78986656544"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Код успешно отправлен",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="Success", description="Статус отправки смс"),
     *             @OA\Property(property="customerId", type="integer", example="12345678", description="ID пользователя"),
     *         )
     *     ),
     *     @OA\Response(response=422, ref="#/components/responses/error:validation"),
     *     @OA\Response(response=424, description="Неудалось отправить код подтверждения",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="Failed", description="Операция не выполнена"),
     *             @OA\Property(property="message", type="string", example="Неудалось отправить код подтверждения", description="Неудалось отправить код подтверждения")
     *         ),
     *      ),
     *      @OA\Response(response=502, description="Cервер в процессе обработки запроса, получил недопустимый ответ",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="Failed", description="Операция не выполнена"),
     *             @OA\Property(property="message", type="string", example="Http client error", description="Http client error")
     *         ),
     *      ),
     *     security={ }
     * )
     *
     * @param \App\Http\Requests\PhoneRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendConfirmationCode(PhoneRequest $request): JsonResponse
    {
        try {
            $result = $this->service->sendCodeToPhone($request);
        }catch (\Exception $exception){
            return \response()->json(['status' => 'Failed', 'message' => $exception->getMessage()])
                ->setStatusCode(Response::HTTP_BAD_GATEWAY);
        }
        if (null === $result) {
            return \response()->json(['status' => 'Failed', 'message' => 'Неудалось отправить код подтверждения'])
                ->setStatusCode(Response::HTTP_FAILED_DEPENDENCY);
        }

        return \response()->json(['status' => 'Success', 'customerId' => $result->getId('mindboxId')])
            ->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * @OA\Post(
     *     path="/api/check/code",
     *     tags={"Authorization"},
     *     summary="Проверка кода отправленного в смс",
     *     description="Проверка кода отправленного в смс",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"customerId", "code"},
     *             @OA\Property(property="customerId", type="integer", example="24352"),
     *             @OA\Property(property="code", type="string", example="3456"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Код подтвержден",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="Success", description="Статус получения данных"),
     *             @OA\Property(property="customerId", example="423456", type="integer", description="ID пользователя"),
     *             @OA\Property(property="firstName", example="Сергей", type="string", description="Имя пользователя"),
     *             @OA\Property(property="lastName", example="Петров", type="string", description="Фамилия пользователя"),
     *             @OA\Property(property="email", example="sp@email.com", type="string", description="Почта пользователя"),
     *             @OA\Property(property="birthDate", example="1995-02-05", type="string", description="Дата рождения пользователя"),
     *             @OA\Property(property="city", example="Вологда", type="string", description="Город пользователя"),
     *             @OA\Property(property="tradeNetworkId", example="2", type="integer", description="ID Торговой сети"),
     *             @OA\Property(property="productId", example="3", type="integer", description="ID продукта"),
     *             @OA\Property(property="token", example="ga52g24aw4r5q24fwef2", type="string", description="Токен должен быть передан в Authorization заголовке как Bearer"),
     *         )
     *     ),
     *     @OA\Response(response=422, ref="#/components/responses/error:validation"),
     *     @OA\Response(response=406, description="Неверный код подтверждения",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Неверный код подтверждения", description="Неверный код подтверждения")
     *         ),
     *      ),
     *      @OA\Response(response=502, description="Cервер в процессе обработки запроса, получил недопустимый ответ",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Http client error", description="Http client error")
     *         ),
     *      ),
     *     security={ }
     * )
     *
     * @param \App\Http\Requests\CheckConfirmationCodeRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkConfirmationCode(CheckConfirmationCodeRequest $request): JsonResponse
    {
        try {
            $result = $this->service->checkCode($request);
        }catch (\Exception $exception){
            return \response()->json(['message' => $exception->getMessage()])
                ->setStatusCode(Response::HTTP_BAD_GATEWAY);
        }

        if (null === $result) {
            return \response()->json(['message' => 'Неверный код подтверждения'])
                ->setStatusCode(Response::HTTP_NOT_ACCEPTABLE);
        }

        return \response()->json($result)->setStatusCode(Response::HTTP_OK);
    }
}
