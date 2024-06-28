<?php

namespace App\Plugins\Telegram\Commands;

use App\Models\User;
use App\Plugins\Telegram\Telegram;
use App\Services\TicketService;

class ReplyTicket extends Telegram {
    public $regex = '/[#](.*)/';
    public $description = 'Phản hồi ticket';

    public function handle($message, $match = []) {
        if (!$message->is_private) return;
        $this->replayTicket($message, $match[1]);
    }


    private function replayTicket($msg, $ticketId)
    {
        $user = User::where('telegram_id', $msg->chat_id)->first();
        if (!$user) {
            abort(500, 'Người dùng không tồn tại');
        }
        if (!$msg->text) return;
        if (!($user->is_admin || $user->is_staff)) return;
        $ticketService = new TicketService();
        $ticketService->replyByAdmin(
            $ticketId,
            $msg->text,
            $user->id
        );
        $telegramService = $this->telegramService;
        $telegramService->sendMessage($msg->chat_id, "#`{$ticketId}` Ticket đã được phản hồi", 'markdown');
        $telegramService->sendMessageWithAdmin("#`{$ticketId}` ticket đã được phản hổi bởi {$user->email} ", true);
    }
}
