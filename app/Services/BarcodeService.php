<?php

declare(strict_types=1);

namespace App\Services;

use App\DataTransfersObject\RequestDataBarcodeDTO;
use App\DataTransfersObject\ResponseDataBarcodeDTO;
use App\Models\ActionCustomer;
use App\Models\Promocode;
use App\Models\PromocodeLog;
use App\Models\TradeNetwork;
use App\Operations\Mindbox\MindboxOperation;
use \Milon\Barcode\DNS1D;
use Milon\Barcode\DNS2D;
use Mindbox\Exceptions\MindboxClientException;

/**
 * Class BarcodeService
 *
 * @package App\Services
 */
final class BarcodeService
{
    /**
     * @param \App\DataTransfersObject\RequestDataBarcodeDTO $dataRequest
     *
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|object|null
     */
    public function getPromocode(RequestDataBarcodeDTO $dataRequest)
    {
        return Promocode::query()
            ->where('trade_network_id', '=', $dataRequest->tradeNetworkId)
            ->where('product_id', '=', $dataRequest->productId)
            ->where('free', '=', true)
            ->with('tradeNetwork.typePromocode')
            ->first()
        ;
    }

    /**
     * @param \App\DataTransfersObject\RequestDataBarcodeDTO $dataRequest
     *
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|object|null
     */
    public function getTradeNetwork(RequestDataBarcodeDTO $dataRequest)
    {
        return TradeNetwork::query()
            ->where('id', '=', $dataRequest->tradeNetworkId)
            ->where('status', '=', true)
            ->with(['typePromocode', 'products'])
            ->first()
        ;
    }

    /**
     * @param $promocode
     *
     * @return bool
     */
    public function setPromocodeBusy($promocode): bool
    {
        $promocode->free = false;
        return $promocode->save();
    }


    /**
     * @param $promocode
     *
     * @return bool
     */
    public function logPromocode($promocode): bool
    {
        $user = \Auth::user();
        $logPromocode = new PromocodeLog();

        $logPromocode->promocode = $promocode->code;
        $logPromocode->trade_network = $promocode->tradeNetwork->name;
        $logPromocode->user_id = $user->id;
        $logPromocode->mindbox_id = $user->mindbox_id;
        $logPromocode->product_id = $promocode->product_id;
        $logPromocode->type_promocode_id = $promocode->tradeNetwork->type_promocode_id;
        $logPromocode->url = $promocode->tradeNetwork->url;

        return $logPromocode->save();
    }

    /**
     * @param $tradeNetworkShortName
     * @param $promocode
     *
     * @return \App\DataTransfersObject\ResponseDataBarcodeDTO
     */
    public function getBarcode($tradeNetworkShortName, $promocode): ResponseDataBarcodeDTO
    {
        if ($promocode) {
            // Если промокод есть
            switch ($tradeNetworkShortName) {
                case 'qr':
                    $qrCode = new DNS2D();
                    $type = 'qr';
                    $code = $promocode;
                    $content = 'data:image/png;base64,' . $qrCode->getBarcodePNG($promocode, 'QRCODE', 10, 10);
                    break;
                case 'bar':
                    $barCode = new DNS1D();
                    $type = 'bar';
                    $code = $promocode;
                    $content = 'data:image/png;base64,' . $barCode->getBarcodePNG($promocode, 'C128', 5, 200, [0,0,0] ,false);
                    break;
                case 'symbol':
                    $type = 'symbol';
                    $code = $promocode;
                    $content = $promocode;
                    break;
                default:
                    $type = 'screen';
                    $code = '';
                    $content = '';
            }

        }elseif ($tradeNetworkShortName === 'screen'){
            // Если промокода нет, но и отображать его не нужно
            $type = 'screen';
            $code = '';
            $content = '';
        }else{
            // Когда нет кода, а отображать нужно
            $type = 'none';
            $code = '';
            $content = '';
        }
        $date = new \DateTime();
        $timestamp = \time();

        return new ResponseDataBarcodeDTO([
            'type' => $type,
            'promocode' => $code,
            'content' => $content,
            'date' => $date,
            'timestamp' => $timestamp
        ]);
    }

    /**
     * @param $promocode
     * @param $salt
     *
     * @return \Mindbox\DTO\ResultDTO
     * @throws \Mindbox\Exceptions\MindboxClientException
     */
    public function sendSmsPromocode($promocode, $salt): \Mindbox\DTO\ResultDTO
    {
        $customerPhone = trim(\Auth::user()->phone, '+');

        try {
            $sendSmsPromocode = (new MindboxOperation)->sendSmsPromocode($customerPhone, $promocode, $salt);
        } catch (\Exception $exception) {
            throw new MindboxClientException($exception->getMessage(), $exception->getCode(), $exception);
        }

        return $sendSmsPromocode;
    }

    /**
     * @param $promocode
     *
     * @return string
     * @throws \Exception
     */
    public function generateSalt($promocode): string
    {
        $user = \Auth::user();

        do {
            $salt = bin2hex(random_bytes(8));
            $result = ActionCustomer::where('salt', '=', $salt)->exists();
        } while ($result);

        ActionCustomer::create([
            'user_id' => $user->id,
            'trade_network_id' => $user->trade_network_id,
            'code' => $promocode,
            'salt' => $salt,
        ]);

        return $salt;
    }

    /**
     * @param $resultVerifyAge
     *
     * @return \Mindbox\DTO\ResultDTO|null
     */
    public function setVerifyAgeMindbox($resultVerifyAge): ?\Mindbox\DTO\ResultDTO
    {
        $customerMindboxId = \Auth::user()->mindbox_id;

        $sendVerifyAge= null;

        try {
            $sendVerifyAge = (new MindboxOperation)->sendVerifyAge($customerMindboxId, $resultVerifyAge);
        } catch (\Exception $exception) {
            \Log::error($exception->getMessage());
        }

        return $sendVerifyAge;
    }
}
