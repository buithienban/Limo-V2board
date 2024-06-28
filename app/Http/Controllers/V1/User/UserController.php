<?php
namespace App\Http\Controllers\V1\User;

class UserController extends \App\Http\Controllers\Controller
{
    public function getActiveSession(\Illuminate\Http\Request $request)
    {
        $user = \App\Models\User::find($request->user["id"]);
        if (!$user) {
            abort(500, __("The user does not exist"));
        }
        $authService = new \App\Services\AuthService($user);
        return response(["data" => $authService->getSessions()]);
    }
    public function removeActiveSession(\Illuminate\Http\Request $request)
    {
        $user = \App\Models\User::find($request->user["id"]);
        if (!$user) {
            abort(500, __("The user does not exist"));
        }
        $authService = new \App\Services\AuthService($user);
        return response(["data" => $authService->removeSession($request->input("session_id"))]);
    }
    public function checkLogin(\Illuminate\Http\Request $request)
    {
        $data = ["is_login" => $request->user["id"] ? true : false];
        if ($request->user["is_admin"]) {
            $data["is_admin"] = true;
        }
        return response(["data" => $data]);
    }
    public function changeSNI(\App\Http\Requests\User\UserChangeSNI $request)
    {
        $dname_sni = $request->input("dname_sni");
        $network_settings = $request->input("network_settings");
        $user = \App\Models\User::find($request->user["id"]);
        if (!$user) {
            abort(500, __("The user does not exist"));
        }
        $user->dname_sni = $dname_sni;
        $user->network_settings = $network_settings;
        if (!$user->save()) {
            abort(500, __("Update failed"));
        }
        return response(["data" => true, "message" => __("Cập Nhật SNI Thành Công")]);
    }
    public function changeAvatar(\App\Http\Requests\User\UserChangeAvatar $request)
    {
        $user = \App\Models\User::find($request->user["id"]);
        if (!$user) {
            abort(500, __("The user does not exist"));
        }
        $avatar_url_new = $request->input("avatar_url_new");
        $user->avatar_url = $avatar_url_new;
        if (!$user->save()) {
            abort(500, __("cập nhật thất bại"));
        }
        return response(["data" => true, "message" => __("Cập nhật avatar thành công")]);
    }
    public function changePassword(\App\Http\Requests\User\UserChangePassword $request)
    {
        $user = \App\Models\User::find($request->user["id"]);
        if (!$user) {
            abort(500, __("The user does not exist"));
        }
        if (!\App\Utils\Helper::multiPasswordVerify($user->password_algo, $user->password_salt, $request->input("old_password"), $user->password)) {
            abort(500, __("The old password is wrong"));
        }
        $user->password = password_hash($request->input("new_password"), PASSWORD_DEFAULT);
        $user->password_algo = NULL;
        $user->password_salt = NULL;
        if (!$user->save()) {
            abort(500, __("Save failed"));
        }
        return response(["data" => true]);
    }
    public function info(\Illuminate\Http\Request $request)
    {
        $user = \App\Models\User::where("id", $request->user["id"])->select(["email", "avatar_url", "transfer_enable", "device_limit", "dname_sni", "last_login_at", "created_at", "banned", "remind_expire", "remind_traffic", "expired_at", "balance", "commission_balance", "plan_id", "discount", "commission_rate", "telegram_id", "uuid"])->first();
        if (!$user) {
            abort(500, __("The user does not exist"));
        }
        if (!$user["avatar_url"]) {
            $user["avatar_url"] = config("v2board.avatar_def") ?? "http://www.gravatar.com/avatar/" . md5($user->email) . "?s=64&d=identicon";
        }
        return response(["data" => $user]);
    }
    public function getStat(\Illuminate\Http\Request $request)
    {
        $stat = [\App\Models\Order::where("status", 0)->where("user_id", $request->user["id"])->count(), \App\Models\Ticket::where("status", 0)->where("user_id", $request->user["id"])->count(), \App\Models\User::where("invite_user_id", $request->user["id"])->count()];
        return response(["data" => $stat]);
    }
    public function getSubscribe(\Illuminate\Http\Request $request)
    {
        $user = \App\Models\User::where("id", $request->user["id"])->select(["plan_id", "token", "expired_at", "u", "d", "transfer_enable", "device_limit", "dname_sni", "email", "avatar_url", "uuid", "created_at"])->first();
        if (!$user) {
            abort(500, __("The user does not exist"));
        }
        if ($user->plan_id) {
            $user["plan"] = \App\Models\Plan::find($user->plan_id);
            if (!$user["plan"]) {
                abort(500, __("Subscription plan does not exist"));
            }
        }
        $countalive = 0;
        $ips_array = \Illuminate\Support\Facades\Cache::get("ALIVE_IP_USER_" . $request->user["id"]);
        if ($ips_array) {
            $countalive = $ips_array["alive_ip"];
        }
        $user["alive_ip"] = $countalive;
        $user["subscribe_url"] = \App\Utils\Helper::getSubscribeUrl($user["token"]);
        $userService = new \App\Services\UserService();
        $user["reset_day"] = $userService->getResetDay($user);
        if (!$user["avatar_url"]) {
            $user["avatar_url"] = config("v2board.avatar_def") ?? "http://www.gravatar.com/avatar/" . md5($user->email) . "?s=64&d=identicon";
        }
        return response(["data" => $user]);
    }
    public function resetSecurity(\Illuminate\Http\Request $request)
    {
        $user = \App\Models\User::find($request->user["id"]);
        if (!$user) {
            abort(500, __("The user does not exist"));
        }
        $user->uuid = \App\Utils\Helper::guid(true);
        $user->token = \App\Utils\Helper::guid();
        if (!$user->save()) {
            abort(500, __("Reset failed"));
        }
        return response(["data" => \App\Utils\Helper::getSubscribeUrl($user["token"])]);
    }
    public function update(\App\Http\Requests\User\UserUpdate $request)
    {
        $updateData = $request->only(["remind_expire", "remind_traffic"]);
        $user = \App\Models\User::find($request->user["id"]);
        if (!$user) {
            abort(500, __("The user does not exist"));
        }
        try {
            $user->update($updateData);
        } catch (\Exception $ex) {
            abort(500, __("Save failed"));
        }
        return response(["data" => true]);
    }
    public function transfer(\App\Http\Requests\User\UserTransfer $request)
    {
        $user = \App\Models\User::find($request->user["id"]);
        if (!$user) {
            abort(500, __("The user does not exist"));
        }
        if ($user->commission_balance < $request->input("transfer_amount")) {
            abort(500, __("Insufficient commission balance"));
        }
        $user->commission_balance = $user->commission_balance - $request->input("transfer_amount");
        $user->balance = $user->balance + $request->input("transfer_amount");
        if (!$user->save()) {
            abort(500, __("Transfer failed"));
        }
        return response(["data" => true]);
    }
    public function getQuickLoginUrl(\Illuminate\Http\Request $request)
    {
        $user = \App\Models\User::find($request->user["id"]);
        if (!$user) {
            abort(500, __("The user does not exist"));
        }
        $code = \App\Utils\Helper::guid();
        $key = \App\Utils\CacheKey::get("TEMP_TOKEN", $code);
        \Illuminate\Support\Facades\Cache::put($key, $user->id, 60);
        $redirect = "/#/login?verify=" . $code . "&redirect=" . ($request->input("redirect") ? $request->input("redirect") : "dashboard");
        if (config("v2board.app_url")) {
            $url = config("v2board.app_url") . $redirect;
        } else {
            $url = url($redirect);
        }
        return response(["data" => $url]);
    }
}

?>