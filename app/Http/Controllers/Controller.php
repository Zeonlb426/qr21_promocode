<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use OpenApi\Annotations as OA;

/**
 *
 * @OA\OpenApi(
 *     @OA\Info(
 *         version="1.0.0",
 *         title=L5_SWAGGER_APP_NAME,
 *     ),
 *     servers={
 *         @OA\Server(
 *             description="Описание",
 *             url=L5_SWAGGER_CONST_HOST,
 *             description="API"
 *         )
 *     }
 * )
 *
 * @OA\Response(
 *     response="error:not_found",
 *     description="Данные не найдены",
 *     @OA\JsonContent(
 *         @OA\Property(
 *              property="message",
 *              type="string",
 *              description="Текст ошибки",
 *              example="messages.exception.not_found"
 *         ),
 *    ),
 * )
 *
 * @OA\Response(
 *     response="error:validation",
 *     description="Ошибка валидации запроса",
 *     @OA\JsonContent(
 *         @OA\Property(
 *              property="status",
 *              type="string",
 *              description="Операция не выполнена",
 *              example="Failed"
 *         ),
 *         @OA\Property(
 *              property="message",
 *              type="string",
 *              description="Текст ошибки",
 *              example="messages.exception.given_data_invalid"
 *         ),
 *         @OA\Property(
 *              property="errors",
 *              type="object",
 *              description="Текст ошибки",
 *              @OA\Property(
 *                  property="field",
 *                  type="string",
 *                  description="Описание ошибки; ключ - имя соответствующего поля",
 *                  example="Поле обязательно для заполнения"
 *              ),
 *         ),
 *    ),
 * )
 *
 * Class Controller
 * @package App\Http\Controllers
 */
abstract class Controller extends BaseController
{
    use AuthorizesRequests;
    use DispatchesJobs;
    use ValidatesRequests;
}
