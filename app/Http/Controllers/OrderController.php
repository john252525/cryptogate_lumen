<?php

namespace App\Http\Controllers;

use App\Models\Deal;
use App\Models\Preorder;
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
        $this->validate($request, [
            'type' => ['required', 'in:sync,async'],
            'token' => 'required|string',
            'data' => 'required|json',
        ]);

        $state = 0;
        if ($request->get('type') == 'sync') {
            $state = mt_rand(1000000,9999999);
        }

        $user = User::where('token', $request->get('token'))->first();

        $data = json_decode($request->get('data'));
        $data_count = count($data);
        if ($data_count === 0) {
            return response()->json(["data" => "The data field must not contain at least one element"]);
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

        if ($request->get('type') === 'sync') {
            // tasker
        }

        return response()->json([
            "ok" => true,
            "deal" => isset($deal) ? $deal : null,
            "orders" => $preorders
        ]);
    }

    public function getOrder(Request $request)
    {
        $this->validate($request, [
            'type' => ['required', 'in:sync,async'],
            'token' => 'required|string',
            'order_id' => 'required|integer|string',
        ]);

        $state = 0;
        if($request->get('type' === 'sync')) {
            $state = mt_rand(1000000,9999999);
        }


        $user = User::where('token', $request->get('token'))->first();
        $preorder = Preorder::find($request->get('order_id'));
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

        if ($request->get('type') === 'sync') {
            // tasker
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
        $this->validate($request, [
            'type' => ['required', 'in:sync,async'],
            'token' => 'required|string',
            'order_id' => 'required|integer|string',
        ]);

        $state = 0;
        if($request->get('type' === 'sync')) {
            $state = mt_rand(1000000,9999999);
        }


        $user = User::where('token', $request->get('token'))->first();
        $preorder = Preorder::find($request->get('order_id'));
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

        if ($request->get('type') === 'sync') {
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
