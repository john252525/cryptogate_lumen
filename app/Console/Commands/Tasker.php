<?php

namespace App\Console\Commands;

use App\Models\OrderBinance;
use App\Models\OrderBinanceLog;
use App\Models\Task;
use DateTime;
use Illuminate\Console\Command;

class Tasker extends Command
{
    protected $signature = 'tasker {state}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
     * @return mixed
     */
    public function handle()
    {
        $state = $this->argument('state');
        $task = Task::where('state', $state)->first();


        if (is_numeric($state)) {
            $rand = $state;
        } else {
            $rand = mt_rand(1000000, 9999999);
            $date = new DateTime();
            $task->state = $rand;
            $task->dt_upd = $date;
            $task->ts_upd = $date->getTimestamp();
            $task->save();
        }
        $date = new DateTime();

        if ($task->action == 'create') {
            $binanceOrder = new OrderBinance();
            $binanceOrder->dt_ins = $date;
            $binanceOrder->ts_ins = $date->getTimestamp();
            $binanceOrder->preorder_id = $task->preorder()->id;
            $binanceOrder->stock_id =$task->preorder()->stock_id;
            $binanceOrder->data = null;
            $binanceOrder->state = 'created';
            $binanceOrder->dt_upd = $date;
            $binanceOrder->ts_upd = $date->getTimestamp();
            $binanceOrder->dt_check = $date;
            $binanceOrder->ts_check = $date->getTimestamp();
            $binanceOrder->save();
        }

        $date = new DateTime();

        $binanceOrderLog = new OrderBinanceLog();
        $binanceOrderLog->dt_ins = $date;
        $binanceOrderLog->ts_ins = $date->getTimestamp();
        $binanceOrderLog->user_id = $task->preorder()->user_id;
        $binanceOrderLog->stock_id = $task->preorder()->stock_id;
        $binanceOrderLog->data = null;
        $binanceOrderLog->action = $task->action;
        $binanceOrderLog->weight_ip = " ";
        $binanceOrderLog->weight_uid = " ";
        $binanceOrderLog->save();

        // todo api_binance_order(action) (create, cancel or get)

        $binanceResponse = [
            "test" => true,
            "successful" => true,
            "state" => "canceled",
        ];

        $binanceOrderLog = new OrderBinanceLog();
        $binanceOrderLog->dt_ins = $date;
        $binanceOrderLog->ts_ins = $date->getTimestamp();
        $binanceOrderLog->user_id = $task->preorder()->user_id;
        $binanceOrderLog->stock_id = $task->preorder()->stock_id;
        $binanceOrderLog->data = $binanceResponse['test'] !== true ? json_encode($binanceResponse) : null;
        $binanceOrderLog->action = $task->action;
        $binanceOrderLog->weight_ip = " ";
        $binanceOrderLog->weight_uid = " ";
        $binanceOrderLog->save();

        if ($binanceResponse["state"] !== $task->state) {
            $binanceOrder = new OrderBinance();
            $binanceOrder->dt_ins = $date;
            $binanceOrder->ts_ins = $date->getTimestamp();
            $binanceOrder->preorder_id = $task->preorder()->id;
            $binanceOrder->stock_id =$task->preorder()->stock_id;
            $binanceOrder->data = null;
            $binanceOrder->state = $binanceResponse["state"];
            $binanceOrder->dt_check = $date;
            $binanceOrder->ts_check = $date->getTimestamp();
            $date = new DateTime();
            $binanceOrder->dt_upd = $date;
            $binanceOrder->ts_upd = $date->getTimestamp();
            $binanceOrder->save();
        }

        if ($binanceResponse["successful"]) {
            $task->state = 1;
            $task->save();
        }
    }
}
