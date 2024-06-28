<?php

namespace App\Http\Controllers\V1\User;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Utils\Dict;
use App\Models\User;
use Illuminate\Http\Request;


class CommController extends Controller
{
    
    public function config(Request $request)
    {
        $domain = $request->getHost();

        $user = User::where('staff_url', $domain)->first();

        
        $telegramDiscussLink = config('v2board.telegram_discuss_link');
        $zaloDiscussLink = config('v2board.zalo_discuss_link');
        if ($user && $user->staff_url == $domain) {
            $webcon = 0;
        } else {
            $webcon = config('v2board.webcon_on', 0);
        }

        if ($user) {
            if (!empty($user->staff_telegram)) {
                $telegramDiscussLink = $user->staff_telegram;
            }
            if (!empty($user->staff_zalo)) {
                $zaloDiscussLink = $user->staff_zalo;
            }
            
        }

        return response([
            'data' => [
                'is_telegram' => (int)config('v2board.telegram_bot_enable', 0),
                'telegram_discuss_link' => $telegramDiscussLink,
                'zalo_discuss_link' => $zaloDiscussLink,
                'idapple_discuss_link' => config('v2board.idapple_discuss_link'),
                'idapple_enable' => config('v2board.idapple_enable', 1),
                'ad_setsni' => config('v2board.ad_setsni'),
                'stripe_pk' => config('v2board.stripe_pk_live'),
                'withdraw_methods' => config('v2board.commission_withdraw_method', Dict::WITHDRAW_METHOD_WHITELIST_DEFAULT),
                'withdraw_close' => (int)config('v2board.withdraw_close_enable', 0),
                'currency' => config('v2board.currency', 'CNY'),
                'currency_symbol' => config('v2board.currency_symbol', 'đ'),
                'show_total' => (int)config('v2board.show_total', 0),
                'webcon_on' => (int)$webcon,
                'cloudflare_ns_1' => config('v2board.cloudflare_ns_1'),
                'cloudflare_ns_2' => config('v2board.cloudflare_ns_2'),
                'linkaff_domain' => config('v2board.linkaff_domain'),
                'commission_distribution_enable' => (int)config('v2board.commission_distribution_enable', 0),
                'commission_distribution_l1' => config('v2board.commission_distribution_l1'),
                'commission_distribution_l2' => config('v2board.commission_distribution_l2'),
                'commission_distribution_l3' => config('v2board.commission_distribution_l3')
            ]
        ]);
    }

    public function getStripePublicKey(Request $request)
    {
        $payment = Payment::where('id', $request->input('id'))
            ->where('payment', 'StripeCredit')
            ->first();
        if (!$payment) abort(500, 'payment is not found');
        return response([
            'data' => $payment->config['stripe_pk_live']
        ]);
    }
}
