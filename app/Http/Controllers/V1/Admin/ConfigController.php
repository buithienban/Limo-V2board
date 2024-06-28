<?php

namespace App\Http\Controllers\V1\Admin;

class ConfigController extends \App\Http\Controllers\Controller
{
    public function getEmailTemplate()
    {
        $path = resource_path("views/mail");
        $list = glob($path . '/*');
        $data = [];
        foreach ($list as $item) {
            $pathinfo = pathinfo($item);
            $data[] = $pathinfo['basename'];
        }
        return response(["data" => $data]);
    }
    public function getThemeTemplate()
    {
        $path = public_path("theme");
        $list = glob($path . '/*');
        $data = [];
        foreach ($list as $item) {
            $pathinfo = pathinfo($item);
            $data[] = $pathinfo['basename'];
        }
        return response(["data" => $data]);
    }
    public function testSendMail(\Illuminate\Http\Request $request)
    {
        $obj = new \App\Jobs\SendEmailJob(["email" => $request->user["email"], "subject" => "This is v2board test email", "template_name" => "notify", "template_value" => ["name" => config("v2board.app_name", "V2Board"), "content" => "This is v2board test email", "url" => config("v2board.app_url")]]);
        return response(["data" => true, "log" => $obj->handle()]);
    }
    public function setTelegramWebhook(\Illuminate\Http\Request $request)
    {
        $hookUrl = url("/api/v1/guest/telegram/webhook?access_token=" . md5(config("v2board.telegram_bot_token", $request->input("telegram_bot_token"))));
        $telegramService = new \App\Services\TelegramService($request->input("telegram_bot_token"));
        $telegramService->getMe();
        $telegramService->setWebhook($hookUrl);
        return response(["data" => true]);
    }
    public function fetch(\Illuminate\Http\Request $request)
    {
        $key = $request->input("key");
        $data = ["invite" => ["invite_force" => (int) config("v2board.invite_force", 0), "invite_commission" => config("v2board.invite_commission", 10), "invite_gen_limit" => config("v2board.invite_gen_limit", 5), "invite_never_expire" => config("v2board.invite_never_expire", 0), "commission_first_time_enable" => config("v2board.commission_first_time_enable", 0), "commission_auto_check_enable" => config("v2board.commission_auto_check_enable", 1), "commission_withdraw_limit" => config("v2board.commission_withdraw_limit", 100), "commission_withdraw_method" => config("v2board.commission_withdraw_method", \App\Utils\Dict::WITHDRAW_METHOD_WHITELIST_DEFAULT), "withdraw_close_enable" => config("v2board.withdraw_close_enable", 0), "commission_distribution_enable" => config("v2board.commission_distribution_enable", 0), "commission_distribution_l1" => config("v2board.commission_distribution_l1"), "commission_distribution_l2" => config("v2board.commission_distribution_l2"), "commission_distribution_l3" => config("v2board.commission_distribution_l3")], "site" => ["logo" => config("v2board.logo"), "avatar_def" => config("v2board.avatar_def"), "force_https" => (int) config("v2board.force_https", 0), "stop_register" => (int) config("v2board.stop_register", 0), "ap_name" => config("v2board.ap_name", 0), "app_name" => config("v2board.app_name", "FASTVNTEAM"), "app_description" => config("v2board.app_description", "FASTVNTEAM PANEL"), "app_url" => config("v2board.app_url"), "ad_setsni" => config("v2board.ad_setsni"), "subscribe_url" => config("v2board.subscribe_url"), "try_out_plan_id" => (int) config("v2board.try_out_plan_id", 0), "try_out_hour" => (int) config("v2board.try_out_hour", 1), "tos_url" => config("v2board.tos_url"), "currency" => config("v2board.currency", "VNĐ"), "currency_symbol" => config("v2board.currency_symbol", "VNĐ")], "subscribe" => ["plan_change_enable" => (int) config("v2board.plan_change_enable", 1), "reset_traffic_method" => (int) config("v2board.reset_traffic_method", 0), "surplus_enable" => (int) config("v2board.surplus_enable", 0), "new_order_event_id" => (int) config("v2board.new_order_event_id", 0), "renew_order_event_id" => (int) config("v2board.renew_order_event_id", 0), "change_order_event_id" => (int) config("v2board.change_order_event_id", 0), "show_total" => (int) config("v2board.show_total", 0), "show_info_to_server_enable" => (int) config("v2board.show_info_to_server_enable", 0)], "frontend" => ["frontend_theme" => config("v2board.frontend_theme", "default"), "frontend_theme_sidebar" => config("v2board.frontend_theme_sidebar", "light"), "frontend_theme_header" => config("v2board.frontend_theme_header", "dark"), "frontend_theme_color" => config("v2board.frontend_theme_color", "default"), "frontend_background_url" => config("v2board.frontend_background_url")], "server" => ["server_token" => config("v2board.server_token"), "server_pull_interval" => config("v2board.server_pull_interval", 60), "server_push_interval" => config("v2board.server_push_interval", 60)], "email" => ["email_template" => config("v2board.email_template", "default"), "email_host" => config("v2board.email_host"), "email_port" => config("v2board.email_port"), "email_username" => config("v2board.email_username"), "email_password" => config("v2board.email_password"), "email_encryption" => config("v2board.email_encryption"), "email_from_address" => config("v2board.email_from_address")], "telegram" => ["telegram_bot_enable" => config("v2board.telegram_bot_enable", 0), "telegram_bot_token" => config("v2board.telegram_bot_token"), "telegram_discuss_link" => config("v2board.telegram_discuss_link"), "zalo_discuss_link" => config("v2board.zalo_discuss_link"), "idapple_discuss_link" => config("v2board.idapple_discuss_link"), "idhd_channel_id" => config("v2board.idhd_channel_id"), "idapple_enable" => config("v2board.idapple_enable", 1)], "app" => ["windows_version" => config("v2board.windows_version"), "windows_download_url" => config("v2board.windows_download_url"), "macos_version" => config("v2board.macos_version"), "macos_download_url" => config("v2board.macos_download_url"), "android_version" => config("v2board.android_version"), "android_download_url" => config("v2board.android_download_url")], "safe" => ["email_verify" => (int) config("v2board.email_verify", 0), "safe_mode_enable" => (int) config("v2board.safe_mode_enable", 0), "secure_path" => config("v2board.secure_path", config("v2board.frontend_admin_path", hash("crc32b", config("app.key")))), "staff_path" => config("v2board.staff_path", "webcon"), "webcon_on" => config("v2board.webcon_on", 0), "cloudflare_ns_1" => config("v2board.cloudflare_ns_1"), "cloudflare_ns_2" => config("v2board.cloudflare_ns_2"), "linkaff_domain" => config("v2board.linkaff_domain"), "email_whitelist_enable" => (int) config("v2board.email_whitelist_enable", 0), "email_whitelist_suffix" => config("v2board.email_whitelist_suffix", \App\Utils\Dict::EMAIL_WHITELIST_SUFFIX_DEFAULT), "email_gmail_limit_enable" => config("v2board.email_gmail_limit_enable", 0), "recaptcha_enable" => (int) config("v2board.recaptcha_enable", 0), "recaptcha_key" => config("v2board.recaptcha_key"), "recaptcha_site_key" => config("v2board.recaptcha_site_key"), "register_limit_by_ip_enable" => (int) config("v2board.register_limit_by_ip_enable", 0), "register_limit_count" => config("v2board.register_limit_count", 3), "register_limit_expire" => config("v2board.register_limit_expire", 60), "password_limit_enable" => (int) config("v2board.password_limit_enable", 1), "password_limit_count" => config("v2board.password_limit_count", 5), "password_limit_expire" => config("v2board.password_limit_expire", 60)]];
        if ($key && isset($data[$key])) {
            return response(["data" => [$key => $data[$key]]]);
        }
        return response(["data" => $data]);
    }
    public function save(\App\Http\Requests\Admin\ConfigSave $request)
    {
        $data = $request->validated();
        $config = config("v2board");
        foreach (\App\Http\Requests\Admin\ConfigSave::RULES as $k => $v) {
            if (!in_array($k, array_keys(\App\Http\Requests\Admin\ConfigSave::RULES))) {
                unset($config[$k]);
            } else {
                if (array_key_exists($k, $data)) {
                    $config[$k] = $data[$k];
                }
            }
        }
        $data = var_export($config, 1);
        if (!\Illuminate\Support\Facades\File::put(base_path() . "/config/v2board.php", "<?php\n return " . $data . " ;")) {
            abort(500, "không chỉnh sửa được");
        }
        if (function_exists("opcache_reset") && opcache_reset() === false) {
            abort(500, "Xóa bộ nhớ đệm không thành công, vui lòng gỡ cài đặt hoặc kiểm tra trạng thái cấu hình opcache");
        }
        \Illuminate\Support\Facades\Artisan::call("config:cache");
        return response(["data" => true]);
    }
}

?>