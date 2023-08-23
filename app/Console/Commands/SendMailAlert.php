<?php

namespace App\Console\Commands;

use App\Mail\SendingAlertMail;
use App\Models\Mail;
use App\Models\Promocode;
use App\Models\TradeNetwork;
use Mail as Mails;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;

class SendMailAlert extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mails:send';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sending letters warning about a small number of promotional codes';

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
    public function handle(): int
    {
        $tradeNetworks = TradeNetwork::where('status',true)
            ->where('send_status', true)
            ->select(['id', 'name'])
            ->get()
            ->keyBy('id')
        ;
        $data = new Collection();
        foreach ($tradeNetworks as $tradeNetwork) {
            $promocodesCount = Promocode::where('trade_network_id', $tradeNetwork->id)
                ->where('free', true)
                ->count()
            ;
            if ($promocodesCount < 300) {
                $data->push([
                    'title' => $tradeNetwork->name,
                    'free' => $promocodesCount,
                ]);
            }
        }
        $mails = Mail::where('status', true)->pluck('mail');
        if (($data->count() > 0) && ($mails->count() > 0) ) {
            Mails::to($mails)->send(new SendingAlertMail($data));
        }
        return Command::SUCCESS;
    }
}
