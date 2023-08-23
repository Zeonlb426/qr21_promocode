<?php

namespace App\Console\Commands;

use App\Models\Promocode;
use Carbon\Carbon;
use Illuminate\Console\Command;

class DataTransferPromocodes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'transfer:promocodes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Transferring promocodes from the old DB to the new one';

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
        $this->info('--- Начат процесс переноса промокодов ---');

        Promocode::truncate();
        $mySQL = \DB::connection('mysql');

        $this->line('Идёт подсчёт объема данных ...');

        $countOldPromocodes = $mySQL->table('wf_promocodes')
            ->whereIn('store_id', [1,2,3,5,7,8,18,20,24,27,32,35,36])
            ->count()
        ;

        $this->line('Найдено '. $countOldPromocodes .' промокодов');
        $this->newLine();
        $this->line('Идёт перенос данных ...');

        $progressBar = $this->output->createProgressBar($countOldPromocodes);

        $mySQL->table('wf_promocodes')
            ->whereIn('store_id', [1,2,3,5,7,8,18,20,24,27,32,35,36])
            ->select('id', 'code','store_id','times_used','bundle')
            ->orderBy('id')
            ->chunk(2000, function ($rows) use ($progressBar) {
                foreach($rows as $row) {
                    $data[] = [
                        'code' => $row->code,
                        'trade_network_id' => $row->store_id,
                        'product_id' => 4,
                        'free' => $row->times_used == 0,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ];
                }
                \DB::table('promocodes')->insert($data);
                $progressBar->advance(2000);
            })
        ;
        \DB::disconnect('mysql');

        $progressBar->finish();

        $this->newLine(2);
        $this->info('--- Перенос промокодов закончен. ---');
        return Command::SUCCESS;
    }
}
