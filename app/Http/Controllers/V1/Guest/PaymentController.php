<?php

namespace App\Http\Controllers\V1\Guest;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\OrderService;
use App\Services\PaymentService;
use App\Services\TelegramService;
use Illuminate\Http\Request;
use App\Models\User;

class PaymentController extends Controller
{
    public function notify($method, $uuid, Request $request)
    {
        try {
            $paymentService = new PaymentService($method, null, $uuid);
            $verify = $paymentService->notify($request->input());
            if (!$verify) abort(500, 'verify error');
            if (!$this->handle($verify['trade_no'], $verify['callback_no'])) {
                abort(500, 'handle error');
            }
            die(isset($verify['custom_result']) ? $verify['custom_result'] : 'success');
        } catch (\Exception $e) {
            abort(500, 'fail');
        }
    }

    private function handle($tradeNo, $callbackNo)
{
    $order = Order::where('trade_no', $tradeNo)->first();
    if (!$order) {
        abort(500, 'order is not found');
    }
    if ($order->status !== 0) return true;
    $orderService = new OrderService($order);
    if (!$orderService->paid($callbackNo)) {
        return false;
    }
    $telegramService = new TelegramService();

    // Get user email
    $user = User::find($order->user_id);
    $userEmail = $user ? $user->email : 'Email not found';

    $message = sprintf(
        "💰Thanh Toán Thành Công %s VNĐ\n———————————————\nNội Dung：%s\n———————————————\nID Đơn Hàng: %s\n———————————————\nEmail: %s",
        $order->total_amount / 100,
        $order->trade_no,
        $order->id,
        $userEmail
    );
    $telegramService->sendMessageWithAdmin($message);
    return true;
}
}
