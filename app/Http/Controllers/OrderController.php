<?php

namespace App\Http\Controllers;

use App\Models\Deal;
use App\Models\Preorder;
use App\Models\Stock;
use App\Models\Task;
use App\Models\User;
use DateTime;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Validation\Rules\Enum;

class OrderController extends Controller
{
    /**
     * @throws \Exception
     */
    public function createOrder(Request $request): JsonResponse
    {
        $type = env('DEFAULT_MODE_CREATE');
        $this->validate($request, [
            'type' => ['in:sync,async'],
            'token' => 'required|string',
        ]);

        if ($request->method() === "GET") {
            if (!$request->has('json')) {
                return response()->json([
                    "ok" => false,
                    "error" => "wrong json"
                ]);
            }

            $data = $request->get('json');
            if (gettype($data) === "string") {
                $data = json_decode($data);
            }
        } else {
            if ($request->has('json')) {
                $data = $request->get('json');
                if (gettype($data) === "string") {
                    $data = json_decode($data);
                }
            } else {
                $data = json_decode(file_get_contents('php://input'), 1);;
            }
        }


        if ($request->has('type')) {
            $type = $request->get('type');
        }

        $state = 0;
        if ($type == 'sync') {
            $state = mt_rand(1000000,9999999);
        }

        $user = User::where('token', $request->get('token'))->first();

        if (!$user) {
            return response()->json([
                "ok" => false,
                "error" => "wrong token"
            ]);
        }



        if (!is_array($data) || null) {
            return response()->json([
                "ok" => false,
                "error" => "wrong json"
            ]);
        }

        $data_count = count($data);
        if ($data_count === 0) {
            return response()->json(["json" => "The json field must not contain at least one element"]);
        }

        $date = new DateTime();
        $deal_id = -1;
        if ($data_count > 1) {
            $deal = new Deal();
            $deal->uuid = $this->format_uuidv4(random_bytes(16));
            $deal->user_id = $user->id;
            $deal->count_order = $data_count;
            $deal->dt_ins = $date;
            $deal->ts_ins = $date->getTimestamp();
            $deal->save();
            $deal_id = $deal->id;
        }

        $preorders = [];


        foreach ($data as $value) {
            if (gettype($value) === 'array') {
                $value = (object) $value;
            }

            if (!isset($value->stock)) {
                return response()->json([
                    "ok" => false,
                    "error" => "wrong stock"
                ]);
            }

            if (gettype($value->stock) === "string") {
                if ($value->stock === "binance_spot" || $value->stock === "binance_futures") {
                    $stock = Stock::where('user_id', $user->id)->where('stock', $value->stock)->first();
                    if (!$stock) {
                        return response()->json([
                            "ok" => false,
                            "error" => "wrong stock"
                        ]);
                    }
                    $value->stock = $stock->id;
                } else {
                    return response()->json([
                        "ok" => false,
                        "error" => "wrong stock"
                    ]);
                }


            }

            $typeValidate = ['market', 'limit', 'oco'];
            $sideValidate = ['buy', 'sell'];
            $positionSide = ['long', 'short'];
            $stateValidate = ['new', 'pending', 'created', 'canceled', 'filled'];

            if (!isset($value->type) || !in_array($value->type, $typeValidate)) {
                return response()->json([
                    "ok" => false,
                    "error" => "wrong type"
                ]);
            }

            if (!isset($value->side) || !in_array($value->side, $sideValidate)) {
                return response()->json([
                    "ok" => false,
                    "error" => "wrong side"
                ]);
            }

            if (!isset($value->positionSide) || !in_array($value->positionSide, $positionSide)) {
                return response()->json([
                    "ok" => false,
                    "error" => "wrong positionSide"
                ]);
            }

            if (!isset($value->pair)) {
                return response()->json([
                    "ok" => false,
                    "error" => "wrong pair"
                ]);
            }

            if (!isset($value->data)) {
                return response()->json([
                    "ok" => false,
                    "error" => "wrong data"
                ]);
            }


            $preorder = new Preorder();
            $preorder->uuid = $this->format_uuidv4(random_bytes(16));
            $preorder->dt_ins = $date;
            $preorder->ts_ins = $date->getTimestamp();
            $preorder->user_id = $user->id;
            $preorder->deal_id = $deal_id;
            $preorder->stock_id = $value->stock;
            $preorder->type = $value->type;
            $preorder->side = $value->side;
            $preorder->positionSide = $value->positionSide;
            $preorder->pair = $value->pair;
            $preorder->state = "new";
            $preorder->data = json_encode($value->data);
            $preorder->save();
            $preorders[] = $preorder;
            $task = new Task();
            $task->dt_ins = $date;
            $task->ts_ins = $date->getTimestamp();
            $task->action = "create";
            $task->state = $state;
            $task->preorder_id = $preorder->id;
            $task->dt_upd = $date;
            $task->ts_upd = $date->getTimestamp();
            $task->save();
        }

        if ($type === 'sync') {
            Artisan::call('tasker', ['state' => $state]);
        }

        return response()->json([
            "ok" => true,
            "deal" => isset($deal) ? $deal : null,
            "orders" => $preorders
        ]);
    }
    public function getOrder(Request $request)
    {
        $type = env('DEFAULT_MODE_GET');

        $this->validate($request, [
            'type' => ['in:sync,async'],
            'token' => 'required|string',
            'order_id' => 'required|integer|string',
        ]);

        if ($request->has('type')) {
            $type = $request->get('type');
        }

        $state = 0;
        if($type === 'sync') {
            $state = mt_rand(1000000,9999999);
        }


        $user = User::where('token', $request->get('token'))->first();

        if (!$user) {
            return response()->json([
                "ok" => false,
                "error" => "wrong token"
            ]);
        }

        $preorder = Preorder::find($request->get('order_id'));

        if (!$preorder) {
            return response()->json([
                "ok" => false,
                "error" => "wrong id"
            ]);
        }

        $date = new DateTime();
        $task = new Task();
        $task->dt_ins = $date;
        $task->ts_ins = $date->getTimestamp();
        $task->action = "get";
        $task->state = $state;
        $task->preorder_id = $preorder->id;
        $task->dt_upd = $date;
        $task->ts_upd = $date->getTimestamp();
        $task->save();

        $deal = null;

        if ($preorder->deal_id !== -1) {
            $deal = Deal::find($preorder->deal_id);
        }

        if ($type === 'sync') {
            Artisan::call('tasker', ['state' => $state]);
        }

        return response()->json(
            [
                "ok" => true,
                "deal" => $deal,
                "orders" => $preorder
            ]
        );
    }
    public function cancelOrder(Request $request)
    {
        $type = env('DEFAULT_MODE_GET');
        $this->validate($request, [
            'type' => ['in:sync,async'],
            'token' => 'required|string',
            'order_id' => 'required|integer|string',
        ]);

        $state = 0;
        if($type === 'sync') {
            $state = mt_rand(1000000,9999999);
        }


        $user = User::where('token', $request->get('token'))->first();

        if (!$user) {
            return response()->json([
                "ok" => false,
                "error" => "wrong token"
            ]);
        }

        $preorder = Preorder::find($request->get('order_id'));

        if (!$preorder) {
            return response()->json([
                "ok" => false,
                "error" => "wrong id"
            ]);
        }

        $date = new DateTime();
        $task = new Task();
        $task->dt_ins = $date;
        $task->ts_ins = $date->getTimestamp();
        $task->action = "cancel";
        $task->state = $state;
        $task->preorder_id = $preorder->id;
        $task->dt_upd = $date;
        $task->ts_upd = $date->getTimestamp();
        $task->save();

        $deal = null;

        if ($preorder->deal_id !== -1) {
            $deal = Deal::find($preorder->deal_id);
        }

        if ($type === 'sync') {
            Artisan::call('tasker', ['state' => $state]);
        }

        return response()->json(
            [
                "ok" => true,
                "deal" => $deal,
                "orders" => $preorder
            ]
        );
    }
}
