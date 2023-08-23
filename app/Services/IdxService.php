<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Product;
use App\Models\TradeNetwork;
use App\Operations\Idx\IdxOperation;
use App\Repositories\IdxlogRepository;
use Carbon\Carbon;
use GuzzleHttp\Exception\GuzzleException;
use Mindbox\Exceptions\MindboxClientException;

/**
 * Class
 *
 * @package App\Services
 */
final class IdxService
{

    const IDX_VERIFY_AGE = 'verifyAge';
    const IDX_VERIFY_AGE_V2_SPEC = 'verifyAgeV2Spec';

    /**
     * @var \App\Operations\Idx\IdxOperation
     */
    private IdxOperation $idxManager;

    /**
     * @var \App\Repositories\IdxlogRepository
     */
    private IdxlogRepository $idxlogRepository;


    private ProfileCustomerService $customer;

    /**
     * @param \App\Operations\Idx\IdxOperation $idxManager
     * @param \App\Repositories\IdxlogRepository $idxlogRepository
     * @param \App\Services\ProfileCustomerService $customer
     */
    public function __construct(IdxOperation $idxManager, IdxlogRepository $idxlogRepository, ProfileCustomerService $customer)
    {
        $this->idxManager = $idxManager;

        $this->idxlogRepository = $idxlogRepository;

        $this->customer = $customer;
    }


    /**
     * @param $phone
     * @param $dataRequest
     * @return mixed|null
     */
    public function verifyAge($phone, $dataRequest)
    {
        $startTime = microtime(true);

        try {
            $responseCustomerData = $this->customer->getCustomerData()->getCustomer();
            $responseIdxVerifyAge = $this->idxManager->processRequest(self::IDX_VERIFY_AGE_V2_SPEC,
                [
                    'lastName' => $responseCustomerData->getLastName(),
                    'firstName' => $responseCustomerData->getFirstName(),
                    'birthDate' => Carbon::createFromFormat('Y-m-d',$responseCustomerData->getBirthDate())->format('d.m.Y'),
                ]
            );
        } catch (GuzzleException $exception) {
            \Log::error($exception->getMessage());
        } catch (MindboxClientException $exception) {
            \Log::error($exception->getMessage());
        }

        $endTime = microtime(true);

        if (!isset($responseIdxVerifyAge)) {
            return null;
        }

        $difTime = round($endTime - $startTime, 5);

        $responseContent = json_decode($responseIdxVerifyAge->getBody()->getContents());

        $tradeNetwork = TradeNetwork::where('id', $dataRequest->tradeNetworkId)->first();
        $product = Product::where('id', $dataRequest->productId)->first();

        $data = [
            'method' => self::IDX_VERIFY_AGE_V2_SPEC,
            'phone' => $phone,
            'params' => '{"phone": '.$phone.'}',
            'result_code' => $responseContent->resultCode,
            'result_code_text' => array_key_exists($responseContent->resultCode, $this->status()) ? $this->status()[$responseContent->resultCode] : null,
            'score' => $responseContent->score,
            'score_text' => $responseContent->scoreText,
            'response' => var_export($responseContent, true),
            'duration' => $difTime,
            'url' => $tradeNetwork->url,
            'trade_network' => $tradeNetwork->name,
            'product' => $product->name
        ];

        $this->idxlogRepository->saveLogs($data);

        return $responseContent;
    }

    /**
     * @return string[]
     */
    private function status(): array
    {
        return [
            '0'    => 'Успешное выполнение',
            '-1'   => 'Не удалось определить причину ошибки',
            '-2'   => 'Неверный ключ доступа',
            '-3'   => 'Аккаунт неактивен',
            '-5'   => 'Неверный идентификатор операции',
            '-8'   => 'Операция относится к другому лицевому счету',
            '-10'  => 'Поставщик услуг не найден',
            '-12'  => 'Обязательный параметр отсутствует или не заполнен',
            '-17'  => 'Неверный ключ',
            '-19'  => 'Ошибка при проверке реквизитов доступа',
            '-24'  => 'Не настроены поставщики услуг/сервисов',
            '-25'  => 'Ошибка при подключении/обращении к поставщику сервиса',
            '-27'  => 'Некорректный атрибут',
            '-28'  => 'Ошибка при сохранении результата операции',
            '-31'  => 'Некоректный параметр',
            '-32'  => 'Превышен лимит запросов',
            '-50'  => 'Нет прав для выполнения операции',
            '-150' => 'Превышен лимит запросов к источнику',
            '-100' => 'Информация не найдена',
            '-110' => 'Получена некорректная информация / некорректный ответ от поставщика сервиса',
            '-200' => 'Ошибка при распознавании документа',
        ];
    }
}
