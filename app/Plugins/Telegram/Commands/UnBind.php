<?php

namespace App\Plugins\Telegram\Commands;

use App\Models\User;
use App\Plugins\Telegram\Telegram;

class UnBind extends Telegram {
    public $command = '/unbind';
    public $description = 'Hủy liên kết tài khoản Telegram khỏi trang web';

    public function handle($message, $match = []) {
        if (!$message->is_private) return;
        $user = User::where('telegram_id', $message->chat_id)->first();
        $telegramService = $this->telegramService;
        if (!$user) {
            $telegramService->sendMessage($message->chat_id, 'Không tìm thấy thông tin người dùng của bạn, vui lòng liên kết tài khoản của bạn trước', 'markdown');
            return;
        }
        $user->telegram_id = NULL;
        if (!$user->save()) {
            abort(500, 'Việc hủy liên kết không thành công');
        }
        $telegramService->sendMessage($message->chat_id, 'Hủy liên kết thành công', 'markdown');
    }
}
