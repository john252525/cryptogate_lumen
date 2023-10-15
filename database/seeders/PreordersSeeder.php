<?php

namespace Database\Seeders;

use App\Models\Deal;
use App\Models\Preorder;
use App\Models\Stock;
use DateTime;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PreordersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ($i = 0; $i < 20; $i++) {
            $user_id = rand(1, 5);
            $date = new DateTime();
            $type = ['market', 'limit', 'oco'][rand(0, 2)];
            $side = ['buy', 'sell'][rand(0, 1)];
            $positionSide = ['long', 'short'][rand(0, 1)];
            $state = ['new', 'pending', 'created', 'canceled', 'filled'][rand(0, 4)];
            $deal = Deal::where('user_id', $user_id);
            $stock = Stock::where('user_id', $user_id);
            $deal_id = $deal->get()[rand(0, $deal->count() - 1)]->id;
            $stock_id = $stock->get()[rand(0, $stock->count() - 1)]->id;

            if (rand(0, 1) == 0) {
                $data = [
                    "qty" => rand(0, 100),
                    "price" => rand(1000, 9999),
                    "stoploss" => rand(1000, 9999)
                ];
            } else {
                $data = [
                    "qty" => rand(0, 100),
                    "price" => rand(1000, 9999)
                ];
            }

            $preorders = new Preorder();
            $preorders->uuid = $this->format_uuidv4(random_bytes(16));
            $preorders->dt_ins = $date;
            $preorders->ts_ins = $date->getTimestamp();
            $preorders->user_id = $user_id;
            $preorders->deal_id = $deal_id;
            $preorders->stock_id = $stock_id;
            $preorders->type = $type;
            $preorders->side = $side;
            $preorders->positionSide = $positionSide;
            $preorders->pair = "btc_usdt";
            $preorders->state = $state;
            $preorders->data = json_encode($data);
            $preorders->save();

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
