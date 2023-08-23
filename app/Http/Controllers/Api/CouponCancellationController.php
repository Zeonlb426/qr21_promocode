<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CancellationRequest;
use App\Services\CouponCancellationService;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Response;

final class CouponCancellationController extends Controller
{
    /**
     * @var \App\Services\CouponCancellationService
     */
    private CouponCancellationService $service;

    /**
     * @param \App\Services\CouponCancellationService $service
     */
    public function __construct(CouponCancellationService $service)
    {
        $this->service = $service;
    }
    /**
     * @OA\Post(
     *     path="/api/cancellation",
     *     tags={"Gazprom cancellation coupon"},
     *     summary="Маркировка использованных промокодов в торговых сетях",
     *     description="Маркировка использованных промокодов в торговых сетях",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(type="string", example="[{'BARCODE' : '1234567890','COUPON_CODE' : '11111GZrCUkxaF48','COUPON_STATUS' : 0,'DEVICE_SN' : '','RETAILER_ID' : 'gazn','SELLER_ADDRESS' : '\u0441. \u0413\u0430\u0434\u044e\u043a\u0438\u043d\u043e.; \u0443\u043b. \u041a\u0430\u0440\u043b\u0430 \u041c\u0430\u0440\u043a\u0441\u0430, ;\u0434. 0','SELLER_NAME' : '\u0410\u0417\u0421 ;\u2116;0','SELLER_PIN' : '0000000','TIMESTAMP' : 1646894709}]"),
     *         )
     *     ),
     *     @OA\Response(
     *          response="200",
     *          description="ОК",
     *         @OA\JsonContent(
     *             @OA\Property(type="string", example="[{'COUPON_CODE' : '1234567890','COUPON_ID' : '11111GZrCUkxaF48','COUPON_STATUS' : 0,'RETAILER_ID' : 'gazn'}]"),
     *         )
     *     ),
     *     @OA\Response(
     *          response="403",
     *          description="Forbidden",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="forbidden"),
     *         )
     *     ),
     *     @OA\Response(response=422, ref="#/components/responses/error:validation"),
     * )
     *
     */
    public function CouponCancellation(CancellationRequest $request)
    {
        $user = \Auth::user();
        if ($user->tokenCan('api:cancellation')) {
            $arrayCoupons = $request->json()->all();
            return \response()->json($this->service->CouponCancellation($arrayCoupons))
                ->setStatusCode(Response::HTTP_OK);
        }

        return response()->json(['message' => "forbidden"], Response::HTTP_FORBIDDEN);
    }
}
