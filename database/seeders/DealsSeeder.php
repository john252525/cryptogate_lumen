<?php

namespace Database\Seeders;

use App\Models\Deal;
use DateTime;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DealsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * @throws \Exception
     */
    public function run(): void
    {
        for ($i = 0; $i < 10; $i++) {
            $user_id = rand(1, 5);
            $count_order = rand(1, 12);
            $date = new DateTime();
            $deal = new Deal();
            $deal->uuid = $this->format_uuidv4(random_bytes(16));
            $deal->dt_ins = $date;
            $deal->ts_ins = $date->getTimestamp();
            $deal->user_id = $user_id;
            $deal->count_order = $count_order;
            $deal->save();
        }
    }

    private function format_uuidv4($data)
    {
        assert(strlen($data) == 16);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }
}
