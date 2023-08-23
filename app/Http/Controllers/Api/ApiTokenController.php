<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ApiTokenRequest;
use App\Models\ApiUser;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Response;

final class ApiTokenController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/token",
     *     tags={"Gazprom cancellation coupon"},
     *     summary="Получение токена для доступа к API",
     *     description="Получение токена для доступа к API",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"username", "password"},
     *             @OA\Property(property="username", type="string", example="gazprom"),
     *             @OA\Property(property="password", type="string", example="Fdf33fGGW4g"),
     *         )
     *     ),
     *     @OA\Response(
     *          response="200",
     *          description="ОК",
     *         @OA\JsonContent(
     *             @OA\Property(property="access_token", type="string", example="54|Gfdgsdrg3gaerg435geThg435g"),
     *         )
     *     ),
     *     @OA\Response(
     *          response="401",
     *          description="Unauthenticated",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated"),
     *         )
     *     ),
     *     @OA\Response(response=422, ref="#/components/responses/error:validation"),
     *     security={ }
     * )
     *
     */
    public function getToken(ApiTokenRequest $request)
    {
        $login = $request->get('username');
        $password = $request->get('password');

        $apiUser = ApiUser::where('login', $login)->where('password', $password)->first();

        if (!$apiUser) {
            return response()->json(['message' => "Unauthenticated"], Response::HTTP_UNAUTHORIZED);
        }

        $apiUser->tokens()->delete();

        $token = $apiUser->createToken('api_token', ['api:cancellation'])->plainTextToken;

        return \response()->json(['access_token' => $token])
            ->setStatusCode(Response::HTTP_OK);
    }
}
