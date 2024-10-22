<?php

namespace App\Plugins\Telegram\Commands;

use App\Models\User;
use App\Plugins\Telegram\Telegram;

class GetLatestUrl extends Telegram {
    public $command = '/getlatesturl';
    public $description = 'Liên kết tài khoản Telegram với trang web';

    public function handle($message, $match = []) {
        $telegramService = $this->telegramService;
        $text = sprintf(
            "%s Url mới nhất là ：%s",
            config('v2board.app_name', 'V2Board'),
            config('v2board.app_url')
        );
        $telegramService->sendMessage($message->chat_id, $text, 'markdown');
    }
}
