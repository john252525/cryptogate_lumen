<?php

namespace Database\Seeders;

use App\Models\Task;
use DateTime;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TasksSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $date = new DateTime();
        for ($i = 0; $i <= 30; $i++) {
            $action = ['create', 'cancel', 'get'][rand(0, 2)];
            $mode = ['sync', 'async'][rand(0, 1)];
            $state = rand(0, 1);
            $preorder_id = rand(1, 20);

            $task = new Task();
            $task->dt_ins = $date;
            $task->ts_ins = $date->getTimestamp();
            $task->preorder_id = $preorder_id;
            $task->action = $action;
            $task->mode = $mode;
            $task->state = $state;
            $task->dt_upd = $date;
            $task->ts_upd = $date->getTimestamp();
            $task->save();
        }
    }
}
