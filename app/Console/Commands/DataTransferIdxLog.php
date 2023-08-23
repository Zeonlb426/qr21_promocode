<?php

namespace App\Console\Commands;

use App\Models\IdxLog;
use Illuminate\Console\Command;

class DataTransferIdxLog extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'transfer:idx-log';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Transferring idx log from the old DB to the new one';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->newLine(2);
        $this->info('=== Начат процесс переноса данных журнала IDX ===');

        IdxLog::truncate();
        $mySQL = \DB::connection('mysql');

        $this->line('Идёт подсчёт объема данных ...');

        $stores = $mySQL->table('wf_stores')
            ->select(['id', 'name'])
            ->get()
            ->pluck('name','id')
        ;

        $countRowsIdxLog = $mySQL->table('wf_log_idx')
            ->whereNotNull('store_id')
            ->count()
        ;

        $this->line('Найдено '. $countRowsIdxLog .' записей');
        $this->newLine();
        $this->line('Идёт перенос данных ...');

        $progressBar = $this->output->createProgressBar($countRowsIdxLog);

        $mySQL->table('wf_log_idx')
            ->whereNotNull('store_id')
            ->select('id', 'method','mobile_phone', 'params', 'result_code', 'response', 'date_add', 'duration', 'store_id', 'site', 'reg_type')
            ->orderBy('id')
            ->chunk(500, function ($rows) use ($progressBar, $stores) {
                foreach($rows as $row) {
                    $response = json_decode($row->response);
                    $date = \date('Y-m-d H:i:s', $row->date_add);
                    $data[] = [
                        'method' => $row->method,
                        'phone' => $row->mobile_phone,
                        'params' => $row->params,
                        'result_code' => $row->result_code,
                        'result_code_text' => $this->status()[$row->result_code] ?? null,
                        'response' => $row->response,
                        'score' => $response->score ?? null,
                        'score_text' => $response->scoreText ?? null,
                        'duration' => $row->duration,
                        'url' => $row->site,
                        'trade_network' => $stores[$row->store_id],
                        'product' => $row->reg_type == 'qr' ? 'Устройство Ploom' : 'Набор Ploom',
                        'created_at' => $date,
                        'updated_at' => $date,
                    ];
                }
                \DB::table('idx_logs')->insert($data);
                $progressBar->advance(500);
            })
        ;
        \DB::disconnect('mysql');

        $progressBar->finish();

        $this->newLine(2);
        $this->info('=== Перенос данных журнала IDX закончен. ===');
        return Command::SUCCESS;
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
