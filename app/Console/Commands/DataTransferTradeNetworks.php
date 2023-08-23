<?php

namespace App\Console\Commands;

use App\Models\TradeNetwork;
use Carbon\Carbon;
use Illuminate\Console\Command;

class DataTransferTradeNetworks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'transfer:tradeNetwork';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Transferring trade network data from the old DB to the new one';

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
        $this->info('Получение данных от источника...');
        $mySQL = \DB::connection('mysql');
        $oldTradeNetworks = $mySQL->table('wf_stores')->where('active', '=', 1)->get();
        \DB::disconnect('mysql');
        foreach ($oldTradeNetworks as $oldTradeNetwork) {
            $data[] = [
                'id' => $oldTradeNetwork->id,
                'name' => $oldTradeNetwork->name,
                'url' => 'https://.famdev.ru',
                'title' => 'Предъявите ваш код продавцу в магазине «'. $oldTradeNetwork->name .'»',
                'sub_title' => '*Код действителен только при предъявлении карты лояльности',
                'type_promocode_id' => $oldTradeNetwork->barcode_type == '1d' ? 2 : 1,
                'instruction_title' => 'В случае возникновения проблем в магазине с применением кода необходимо довести до продавца следующую информацию:',
                'instruction_questions' => json_encode(["Отсканировать товар и Выручай карту", "Нажать кнопку [СКИДКИ] => Выбрать тип скидки «Списание баллов «ВЫРУЧАЙ-КИ», СКИДКА ПО VIP карте и сканирование купонов», нажав кнопку [3] или кнопками [↓↑] и [ВВОД]", "Просканировать ШК купона => Купон на начисление зарегистрирован => нажать [ВВОД]", "Произвести расчет"]),
                'show_instruction' => true,
                'product_id' => $oldTradeNetwork->bundle == 1 ? 3 : 1,
                'quiz_show' => true,
                'quiz_own_answer' => false,
                'quiz_type_answers' => 'radio',
                'quiz_question' => 'Откуда вы узнали о приобретении устройства по специальной цене?',
                'quiz_answers' => json_encode(["В торговой точке", "Из приложения или домашней страницы сети", "Из другого источника"]),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];
        }
        $this->info('Данные получены');
        $this->newLine();
        $this->info('Запись данных в новую базу...');

        TradeNetwork::truncate();
        TradeNetwork::insert($data);

        $this->info('Перенос торговых сетей закончен.');

        return Command::SUCCESS;
    }
}
