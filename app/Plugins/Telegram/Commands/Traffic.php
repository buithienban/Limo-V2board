<?php

namespace App\Plugins\Telegram\Commands;

use App\Models\User;
use App\Plugins\Telegram\Telegram;
use App\Utils\Helper;

class Traffic extends Telegram {
    public $command = '/traffic';
    public $description = 'Truy vấn thông tin traffic';

    public function handle($message, $match = []) {
        $telegramService = $this->telegramService;
        if (!$message->is_private) return;
        $user = User::where('telegram_id', $message->chat_id)->first();
        if (!$user) {
            $telegramService->sendMessage($message->chat_id, 'Không tìm thấy thông tin người dùng của bạn, vui lòng liên kết tài khoản của bạn trước', 'markdown');
            return;
        }
        $transferEnable = Helper::trafficConvert($user->transfer_enable);
        $up = Helper::trafficConvert($user->u);
        $down = Helper::trafficConvert($user->d);
        $remaining = Helper::trafficConvert($user->transfer_enable - ($user->u + $user->d));
        $text = "🚥Kiểm tra lưu lượng\n———————————————\nTổng data: `{$transferEnable}`\nUpload: `{$up}`\nDownload: `{$down}`\nLưu lượng còn lại: `{$remaining}`";
        $telegramService->sendMessage($message->chat_id, $text, 'markdown');
    }
}
