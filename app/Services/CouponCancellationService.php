<?php

declare(strict_types=1);

namespace App\Services;


use App\Models\Promocode;
use Illuminate\Support\Collection;

/**
 * Class SendingAlertService
 *
 * @package App\Services
 */
final class CouponCancellationService
{
    public function CouponCancellation($arrayCoupons): Collection
    {
        $data = new Collection();

        foreach ($arrayCoupons as $coupon) {
            if ($coupon['COUPON_STATUS'] == 0) {
                $status = 200;
                $promocode = Promocode::where('code', $coupon['COUPON_CODE'])->first();

                if (!$promocode) {
                    $status = 300;
                }elseif ($promocode->cancellation) {
                    $status = 302;
                }elseif ($promocode->free) {
                    $status = 311;
                }
                if ($promocode && $promocode->cancellation == false && $promocode->free == false) {
                    $promocode->cancellation_time = \gmdate("Y-m-d H:i:s", $coupon['TIMESTAMP']);
                    $promocode->cancellation = true;
                    $promocode->cancellation_status = $coupon['COUPON_STATUS'];
                    $promocode->save();
                }
                $data->push([
                    'coupon_code' => $coupon['COUPON_CODE'],
                    'coupon_id' => '',
                    'coupon_status' => $status,
                    'retailer_id' => $coupon['RETAILER_ID'],
                ]);
            }
        }
        return $data;
    }
}

