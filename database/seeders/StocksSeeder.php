<?php

namespace Database\Seeders;

use App\Models\Stock;
use DateTime;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StocksSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * @throws \Exception
     */
    public function run(): void
    {
        for ($i = 0; $i < 14; $i++) {
            $user_id = rand(1, 5);
            $stock_id = rand(0, 1);
            $stocks = ['binance_spot', 'binance_futures'];
            $date = new DateTime();
            $stock = new Stock();
            $stock->dt_ins = $date;
            $stock->ts_ins = $date->getTimestamp();
            $stock->stock = $stocks[$stock_id];
            $stock->user_id = $user_id;
            $stock->key = json_encode([
                'key' => $this->format_uuidv4(random_bytes(16)),
                'secret' => $this->gen_token()
            ]);
            $stock->save();
        }
    }

    private function gen_token(): string
    {
        return sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff)
        );
    }

    private function format_uuidv4($data)
    {
        assert(strlen($data) == 16);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }
}
