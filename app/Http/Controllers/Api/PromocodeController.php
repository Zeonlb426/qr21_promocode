<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\DataTransfersObject\RequestDataBarcodeDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\BarcodeRequest;
use App\Http\Requests\PromocodeRequest;
use App\Models\ActionCustomer;
use App\Models\TradeNetwork;
use App\Services\BarcodeService;
use App\Services\IdxService;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class PromocodeController
 *
 * @package App\Http\Controllers\Api
 */
final class PromocodeController extends Controller
{

    /**
     * @var \App\Services\BarcodeService
     */
    private BarcodeService $service;

    private IdxService $idxService;

    /**
     * @param \App\Services\BarcodeService $service
     * @param \App\Services\IdxService $idxService
     */
    public function __construct(BarcodeService $service, IdxService $idxService)
    {
        $this->service = $service;

        $this->idxService = $idxService;
    }

    /**
     * @OA\Post (
     *     path="/api/promocode",
     *     tags={"Promocode"},
     *     summary="Получение промокода",
     *     description="Получение промокода",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"tradeNetworkId", "productId"},
     *             @OA\Property(property="tradeNetworkId", type="integer", example="3"),
     *             @OA\Property(property="productId", type="integer", example="1"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Данные успешно получены",
     *         @OA\JsonContent(
     *             @OA\Property(property="type", type="string", example="qr", description="Тип отображения (qr:2D код, bar:Штрих-код symbol:Символы на экране, screen:Без кода)"),
     *             @OA\Property(property="promocode", type="string", example="240093092529020300000", description="Промокод"),
     *             @OA\Property(property="content", type="string", example="<svg>...</svg>", description="Векторное отображение qr или bar кода, вслучае symbol - дублирование промокода"),
     *             @OA\Property(property="timestamp", type="string", example="1644434381", description="Время в UNIX формате"),
     *             @OA\Property(
     *                 property="date",
     *                 type="object",
     *                 @OA\Property(property="date", type="string", example="2022-02-09 22:19:41.179706"),
     *                 @OA\Property(property="timezone_type", type="string", example="3"),
     *                 @OA\Property(property="timezone", type="string", example="Europe/Moscow"),
     *             ),
     *         )
     *     ),
     *     @OA\Response(response=422, ref="#/components/responses/error:validation"),
     *     @OA\Response(response="404", ref="#/components/responses/error:not_found"),
     * )
     *
     * @param \App\Http\Requests\BarcodeRequest $request
     *
     * @return \Illuminate\Http\JsonResponse|object
     */
    public function getPromocode(BarcodeRequest $request)
    {

        $phone = \Auth::user()->phone;

        $dataRequest = RequestDataBarcodeDTO::fromRequest($request);

        $promocode = $this->service->getPromocode($dataRequest);
        $tradeNetwork = $this->service->getTradeNetwork($dataRequest);

        if ($tradeNetwork == null) {
            return \response()->json(['message' => 'Торговая сеть не найдена'])->setStatusCode(Response::HTTP_NOT_FOUND);
        }

        $code = null;

        if ($promocode) {
            $resultVerifyAge = $this->idxService->verifyAge($phone, $dataRequest);
            $this->service->setVerifyAgeMindbox($resultVerifyAge);
            $this->service->setPromocodeBusy($promocode);
            $this->service->logPromocode($promocode);

            $code = $promocode->code;
        }
        $responseData = $this->service->getBarcode($tradeNetwork->typePromocode->short_name, $code);

        return \response()->json($responseData->toArray())->setStatusCode(Response::HTTP_OK);
    }

    /**
     * @OA\Post (
     *     path="/api/send/sms/promocode",
     *     tags={"Promocode"},
     *     summary="Отправка СМС с промокодом",
     *     description="Отправка СМС с промокодом",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"promocode"},
     *             @OA\Property(property="promocode", type="string", example="2353460004533"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Данные успешно обновлены",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="Success"),
     *             @OA\Property(property="url", type="string", example="https://domen/gerg3v3t4f42"),
     *         )
     *     ),
     *     @OA\Response(response=422, ref="#/components/responses/error:validation"),
     *     @OA\Response(response=502, description="Cервер в процессе обработки запроса, получил недопустимый ответ",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Http client error", description="Http client error")
     *         ),
     *      ),
     * )
     *
     * @param \App\Http\Requests\PromocodeRequest $request
     *
     * @return \Illuminate\Http\JsonResponse|object
     * @throws \Exception
     */
    public function sendSmsPromocode(PromocodeRequest $request)
    {
        $promocode = $request->get('promocode');
        try {
            $salt = $this->service->generateSalt($promocode);
            $result = $this->service->sendSmsPromocode($promocode, $salt);
        }catch (\Exception $exception){
            return \response()->json(['message' => $exception->getMessage()])
                ->setStatusCode(Response::HTTP_BAD_GATEWAY);
        }

        return \response()->json(['status' => $result->getStatus(), 'url' => \URL::to('/' . $salt)])->setStatusCode(Response::HTTP_OK);
    }

    /**
     * @OA\Get(
     *     path="/api/show/{code}",
     *     tags={"Last screen"},
     *     summary="Показ промокода по ссылке из смс",
     *     description="Показ промокода по ссылке из смс",
     *     @OA\Parameter(
     *          in="path",
     *          name="code",
     *          @OA\Schema(type="string",example="fgEfr54gEg4g"),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Данные отправлены",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                  property="code",
     *                  type="object",
     *                   @OA\Property(property="type", type="string", example="qr", description="Тип отображения (qr:2D код, bar:Штрих-код symbol:Символы на экране, screen:Без кода)"),
     *                   @OA\Property(property="promocode", type="string", example="240093092529020300000", description="Промокод"),
     *                   @OA\Property(property="content", type="string", example="<svg>...</svg>", description="Векторное отображение qr или bar кода, вслучае symbol - дублирование промокода"),
     *                   @OA\Property(property="timestamp", type="string", example="1644434381", description="Время в UNIX формате"),
     *                   @OA\Property(
     *                       property="date",
     *                       type="object",
     *                       @OA\Property(property="date", type="string", example="2022-02-09 22:19:41.179706"),
     *                       @OA\Property(property="timezone_type", type="string", example="3"),
     *                       @OA\Property(property="timezone", type="string", example="Europe/Moscow"),
     *                   ),
     *             ),
     *             @OA\Property(
     *                  property="tradeNetwork",
     *                  type="object",
     *                   @OA\Property(property="id", type="string", example="3"),
     *                   @OA\Property(property="status", type="boolean", example="false"),
     *                   @OA\Property(property="name", type="string", example="Пятёрочка"),
     *                   @OA\Property(property="url", type="string", example="https://brreg.ploom.ru"),
     *                   @OA\Property(property="title", type="string", example="Предъявите Ваш код* продавцу в магазине"),
     *                   @OA\Property(property="sub_title", type="string", example="Код действителен только при предъявлении"),
     *                   @OA\Property(property="type_promocode_id", type="string", example="4"),
     *                   @OA\Property(property="instruction_title", type="string", example="В случае возникновения проблем в магазине с применением"),
     *                   @OA\Property(property="instruction_questions", type="string", example="[Отсканировать товар и Выручай карту,...]"),
     *                   @OA\Property(property="show_instruction", type="boolean", example="false"),
     *                   @OA\Property(property="product_id", type="string", example="4"),
     *                   @OA\Property(property="quiz_show", type="boolean", example="false"),
     *                   @OA\Property(property="quiz_own_answer", type="boolean", example="false"),
     *                   @OA\Property(property="quiz_type_answers", type="string", example="radio"),
     *                   @OA\Property(property="quiz_question", type="string", example="Откуда вы узнали о приобретении устройства по специальной цене?"),
     *                   @OA\Property(property="created_at", type="string", example="2022-02-15"),
     *                   @OA\Property(property="updated_at", type="string", example="2022-02-15"),
     *                   @OA\Property(property="quiz_answers", type="string", example="[В торговой точке,...]"),
     *                   @OA\Property(
     *                       property="type_promocode",
     *                       type="object",
     *                       @OA\Property(property="id", type="string", example="2"),
     *                       @OA\Property(property="name", type="string", example="QR"),
     *                       @OA\Property(property="short_name", type="string", example="qr"),
     *                       @OA\Property(property="status", type="boolean", example="true"),
     *                   ),
     *             ),
     *         )
     *     ),
     *     @OA\Response(response="404", ref="#/components/responses/error:not_found"),
     *      @OA\Response(response=500, description="Cервер в процессе обработки запроса, получил недопустимый ответ",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="possibly incorrectly encoded")
     *         ),
     *      ),
     *     security={ }
     * )
     *
     */
    public function showPromocode(string $code)
    {
        $actionCustomer = ActionCustomer::where('salt', $code)->first();

        if ($actionCustomer == null) {
            return \response()->json(['message' => 'Данные не найдены'])
                ->setStatusCode(Response::HTTP_NOT_FOUND);
        }

        $tradeNetwork = TradeNetwork::where('id', $actionCustomer->trade_network_id)->first();

        if ($tradeNetwork == null) {
            return \response()->json(['message' => 'Торговая сеть не найдена'])
                ->setStatusCode(Response::HTTP_NOT_FOUND);
        }

        $responseDataBarcode = $this->service->getBarcode($tradeNetwork->typePromocode->short_name, $actionCustomer->code);

        $mergeData['code'] =  $responseDataBarcode->toArray();
        $mergeData['tradeNetwork']['name'] =  $tradeNetwork->name;
        $mergeData['tradeNetwork']['title'] =  $tradeNetwork->title;
        $mergeData['tradeNetwork']['subTitle'] =  $tradeNetwork->sub_title;
        $mergeData['tradeNetwork']['instruction']['title'] =  $tradeNetwork->instruction_title;
        $mergeData['tradeNetwork']['instruction']['content'] =  $tradeNetwork->instruction_questions;
        $mergeData['tradeNetwork']['instruction']['active'] =  $tradeNetwork->show_instruction;

        return \response()->json($mergeData)->setStatusCode(Response::HTTP_OK);
    }
}
