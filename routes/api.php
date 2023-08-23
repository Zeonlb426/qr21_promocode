<?php

declare(strict_types=1);

use App\Http\Controllers\Api\ApiTokenController;
use App\Http\Controllers\Api\AuthorizationController;
use App\Http\Controllers\Api\CouponCancellationController;
use App\Http\Controllers\Api\PromocodeController;
use App\Http\Controllers\Api\ProfileCustomerController;
use App\Http\Controllers\Api\QuizController;
use App\Http\Controllers\Api\TradeNetworkController;
use Illuminate\Support\Facades\Route;

// Отправка смс с кодом подтверждения
Route::post('/send/code', [AuthorizationController::class, 'sendConfirmationCode'])->middleware('throttle:12,5');

// Проверка кода подтверждения
Route::post('/check/code', [AuthorizationController::class, 'checkConfirmationCode'])->middleware('throttle:12,5');

// Обновление информации о пользователе
Route::post('/customer/update', [ProfileCustomerController::class, 'updateCustomerData'])->middleware(['auth:sanctum', 'throttle:12,5']);

// Получение промокода
Route::post('/promocode', [PromocodeController::class, 'getPromocode'])->middleware(['auth:sanctum', 'throttle:12,5']);

// Получение qr/bar по ссылке из смс
Route::get('/show/{code}', [PromocodeController::class, 'showPromocode'])->middleware(['throttle:12,5']);

// Гашение использованых промокодов
Route::post('/cancellation', [CouponCancellationController::class, 'CouponCancellation'])->middleware(['auth:api_users', 'throttle:100,1']);

// Выдаче токена для подключения к API сторонних сервисов
Route::post('/token', [ApiTokenController::class, 'getToken'])->middleware(['throttle:100,10']);

Route::middleware(['auth:sanctum'])->group(function () {

    // Получение списка торговых сетей
    Route::get('/trade', [TradeNetworkController::class, 'getTradeNetworkList']);

    // Получение информации о пользователе
    Route::get('/customer/info', [ProfileCustomerController::class, 'getCustomerData']);

    // Отправка смс с промокодом
    Route::post('/send/sms/promocode', [PromocodeController::class, 'sendSmsPromocode']);

    // Сохранение ответов пользователя (Откуда узнали про нас?)
    Route::post('/quiz', [QuizController::class, 'sendAnswer']);

});


