<?php

namespace App\Console\Commands;

use App\Models\PromocodeLog;
use Illuminate\Console\Command;

class DataTransferPromocodeLog extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'transfer:promocode-log';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Transferring promocodeLog from the old DB to the new one';

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
        $this->info('*** Начат процесс переноса данных журнала промокодов ***');

        PromocodeLog::truncate();
        $mySQL = \DB::connection('mysql');

        $this->line('Идёт подсчёт объема данных ...');

        $stores = $mySQL->table('wf_stores')
            ->select(['id', 'name'])
            ->get()
            ->pluck('name','id')
        ;

        $countRowsPromocodesLog = $mySQL->table('wf_promocodes_log')
            ->whereNotNull('promocode_id')
            ->count()
        ;

        $this->line('Найдено '. $countRowsPromocodesLog .' записей');
        $this->newLine();
        $this->line('Идёт перенос данных ...');

        $progressBar = $this->output->createProgressBar($countRowsPromocodesLog);

        $mySQL->table('wf_promocodes_log')
            ->whereNotNull('promocode_id')
            ->select('id', 'code','store_id', 'mindbox_id', 'referer', 'date_add')
            ->orderBy('id')
            ->chunk(2000, function ($rows) use ($progressBar, $stores) {
                foreach($rows as $row) {
                    $date = \date('Y-m-d H:i:s', $row->date_add);
                    $data[] = [
                        'promocode' => $row->code,
                        'trade_network' => $stores[$row->store_id],
                        'user_id' => 1,
                        'mindbox_id' => $row->mindbox_id,
                        'product_id' => 4,
                        'type_promocode_id' => 1,
                        'url' => $row->referer,
                        'created_at' => $date,
                        'updated_at' => $date,
                    ];
                }
                \DB::table('promocode_logs')->insert($data);
                $progressBar->advance(2000);
            })
        ;
        \DB::disconnect('mysql');

        $progressBar->finish();

        $this->newLine(2);
        $this->info('*** Перенос данных журнала промокодов закончен. ***');
        return Command::SUCCESS;
    }
}
