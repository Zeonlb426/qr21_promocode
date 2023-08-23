<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TradeNetworkResource;
use App\Services\TradeNetworkService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use OpenApi\Annotations as OA;

final class TradeNetworkController extends Controller
{

    /**
     * @var string|\Illuminate\Http\Resources\Json\JsonResource
     */
    protected string $resource = TradeNetworkResource::class;

    /**
     * @var \App\Services\TradeNetworkService
     */
    private TradeNetworkService $service;


    /**
     * @param \App\Services\TradeNetworkService $service
     */
    public function __construct(TradeNetworkService $service)
    {
        $this->service = $service;
    }

    /**
     * @OA\Get (
     *     path="/api/trade",
     *     tags={"TradeNetwork"},
     *     summary="Список торговых сетей",
     *     description="Информация о торговых сетях",
     *     @OA\Response(
     *          response="200",
     *          description="ОК",
     *          @OA\JsonContent(
     *              @OA\Property(
     *                  property="data",
     *                  type="array",
     *                  @OA\Items(ref="#/components/schemas/TradeNetworkResource")
     *              )
     *          )
     *     ),
     * )
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function getTradeNetworkList(): AnonymousResourceCollection
    {
        return $this->resource::collection(
            $this->service->getAll()
        );
    }
}
