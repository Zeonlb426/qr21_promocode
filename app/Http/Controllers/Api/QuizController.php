<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AnswerRequest;
use App\Services\ProfileCustomerService;
use Illuminate\Http\JsonResponse;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Response;

final class QuizController extends Controller
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
     *     path="/api/quiz",
     *     tags={"Quiz"},
     *     summary="Сохранение ответов квиза в mindbox",
     *     description="Сохранение ответов квиза в mindbox",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"answer"},
     *             @OA\Property(property="answer", type="string", example="[Из соцсетей]"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Данные успешно отправлены",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="Success", description="Статус отправки смс"),
     *         )
     *     ),
     *     @OA\Response(response=422, ref="#/components/responses/error:validation"),
     *     @OA\Response(response=424, description="Неудалось отправить данные",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Неудалось отправить данные", description="Неудалось отправить данные")
     *         ),
     *      ),
     *      @OA\Response(response=502, description="Cервер в процессе обработки запроса, получил недопустимый ответ",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Http client error", description="Http client error")
     *         ),
     *      ),
     * )
     *
     */
    public function sendAnswer(AnswerRequest $request): JsonResponse
    {
        try {
            $result = $this->service->sendQuizAnswer($request);
        }catch (\Exception $exception){
            return \response()->json(['message' => $exception->getMessage()])
                ->setStatusCode(Response::HTTP_BAD_GATEWAY);
        }
        if (null === $result) {
            return \response()->json(['message' => 'Неудалось отправить данные'])
                ->setStatusCode(Response::HTTP_FAILED_DEPENDENCY);
        }

        return \response()->json(['status' => 'Success'])
            ->setStatusCode(Response::HTTP_CREATED);
    }
}
