<?php
namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ConfigSave extends FormRequest
{
    const RULES = [
        // invite & commission
        'invite_force' => 'in:0,1',
        'invite_commission' => 'integer',
        'invite_gen_limit' => 'integer',
        'invite_never_expire' => 'in:0,1',
        'commission_first_time_enable' => 'in:0,1',
        'commission_auto_check_enable' => 'in:0,1',
        'commission_withdraw_limit' => 'nullable|numeric',
        'commission_withdraw_method' => 'nullable|array',
        'withdraw_close_enable' => 'in:0,1',
        'commission_distribution_enable' => 'in:0,1',
        'commission_distribution_l1' => 'nullable|numeric',
        'commission_distribution_l2' => 'nullable|numeric',
        'commission_distribution_l3' => 'nullable|numeric',
        // site
        'logo' => 'nullable|url',
        'avatar_def' => 'nullable|url',
        'force_https' => 'in:0,1',
        'stop_register' => 'in:0,1',
        'ap_name' => 'in:0,1',
        'app_name' => '',
        'app_description' => '',
        'app_url' => 'nullable|url',
        'ad_setsni' => 'nullable',
        'subscribe_url' => 'nullable',
        'subscribe_path' => 'nullable',
        'try_out_enable' => 'in:0,1',
        'try_out_plan_id' => 'integer',
        'try_out_hour' => 'numeric',
        'tos_url' => 'nullable|url',
        'currency' => '',
        'currency_symbol' => '',
        // subscribe
        'plan_change_enable' => 'in:0,1',
        'reset_traffic_method' => 'in:0,1,2,3,4,5',
        'surplus_enable' => 'in:0,1',
        'new_order_event_id' => 'in:0,1',
        'renew_order_event_id' => 'in:0,1',
        'change_order_event_id' => 'in:0,1',
        'show_info_to_server_enable' => 'in:0,1',
        'show_total' => 'in:0,1',
        // server
        'server_token' => 'nullable|min:16',
        'server_pull_interval' => 'integer',
        'server_push_interval' => 'integer',
        // frontend
        'frontend_theme' => '',
        'frontend_theme_sidebar' => 'nullable|in:dark,light',
        'frontend_theme_header' => 'nullable|in:dark,light',
        'frontend_theme_color' => 'nullable|in:default,darkblue,black,green',
        'frontend_background_url' => 'nullable|url',
        // email
        'email_template' => '',
        'email_host' => '',
        'email_port' => '',
        'email_username' => '',
        'email_password' => '',
        'email_encryption' => '',
        'email_from_address' => '',
        // telegram
        'telegram_bot_enable' => 'in:0,1',
        'telegram_bot_token' => '',
        'telegram_discuss_id' => '',
        'telegram_channel_id' => '',
        'telegram_discuss_link' => 'nullable|url',
        'zalo_discuss_link' => 'nullable|url',
        'idapple_discuss_link' => 'nullable|url',
        'idhd_channel_id' => '',
        'idapple_enable' => 'in:1,0',
        // app
        'windows_version' => '',
        'windows_download_url' => '',
        'macos_version' => '',
        'macos_download_url' => '',
        'android_version' => '',
        'android_download_url' => '',
        // safe
        'email_whitelist_enable' => 'in:0,1',
        'email_whitelist_suffix' => 'nullable|array',
        'email_gmail_limit_enable' => 'in:0,1',
        'recaptcha_enable' => 'in:0,1',
        'recaptcha_key' => '',
        'recaptcha_site_key' => '',
        'email_verify' => 'in:0,1',
        'safe_mode_enable' => 'in:0,1',
        'register_limit_by_ip_enable' => 'in:0,1',
        'register_limit_count' => 'integer',
        'register_limit_expire' => 'integer',
        'secure_path' => 'min:8|regex:/^[\w-]*$/',
        'staff_path' => 'min:5|regex:/^[\w-]*$/',
        'webcon_on' => 'in:0,1',
        'cloudflare_ns_1' => 'nullable',
        'cloudflare_ns_2' => 'nullable',
        'linkaff_domain' => 'nullable|url',
        'password_limit_enable' => 'in:0,1',
        'password_limit_count' => 'integer',
        'password_limit_expire' => 'integer',
    ];
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return self::RULES;
    }

    public function messages()
    {
        // illiteracy prompt
        return [
            'app_url.url' => 'Url hợp lệ phải có http(s)://',
            'subscribe_url.url' => 'Url hợp lệ phải có http(s)://',
            'server_token.min' => 'token phải có ít nhất 16 ký tự',
            'tos_url.url' => 'Url hợp lệ phải có  http(s)://',
            'telegram_discuss_link.url' => 'Telegram Url hợp lệ phải có http(s)://',
            'zalo_discuss_link.url' => 'zalo Url hợp lệ phải có http(s)://',
            'idapple_discuss_link.url' => 'idapple Url hợp lệ phải có http(s)://',
            'logo.url' => 'LOGO Url hợp lệ phải có https(s)://',
            'avatar_def.url' => 'Url avatar mặc định hợp lệ phải có https(s)://',
            'linkaff_domain.url' => 'AFF domain Url hợp lệ phải có https(s)://',
            'secure_path.min' => 'Đường dẫn an toàn phải có ít nhất 8 ký tự',
            'staff_path.min' => 'Đường dẫn CTV phải có ít nhất 5 ký tự',
            'secure_path.regex' => 'Đường dẫn an toàn chỉ chứa chữ cái, số',
            'staff_path.regex' => 'Đường dẫn an toàn chỉ chứa chữ cái, số',
        ];
    }
}
