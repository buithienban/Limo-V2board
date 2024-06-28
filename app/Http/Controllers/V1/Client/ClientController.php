<?php

namespace App\Http\Controllers\V1\Client;

use App\Http\Controllers\Controller;
use App\Protocols\General;
use App\Services\ServerService;
use App\Services\UserService;
use App\Utils\Helper;
use App\Models\Plan;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function subscribe(Request $request)
    {
        $flag = $request->input('flag')
            ?? ($_SERVER['HTTP_USER_AGENT'] ?? '');
        $flag = strtolower($flag);
        $user = $request->user;
        // account not expired and is not banned.
        $userService = new UserService();
        if ($userService->isAvailable($user)) {
            $serverService = new ServerService();
            $servers = $serverService->getAvailableServers($user);
            $this->setSubscribeInfoToServers($servers, $user);
            if ($flag) {
                foreach (array_reverse(glob(app_path('Protocols') . '/*.php')) as $file) {
                    $file = 'App\\Protocols\\' . basename($file, '.php');
                    $class = new $file($user, $servers);
                    if (strpos($flag, $class->flag) !== false) {
                        return $class->handle();
                    }
                }
            }
            $class = new General($user, $servers);
            return $class->handle();
        }
    }

    private function setSubscribeInfoToServers(&$servers, $user)
    {
        if (!isset($servers[0])) return;
        if (!(int)config('v2board.show_info_to_server_enable', 0)) return;
        $useTraffic = $user['u'] + $user['d'];
        $totalTraffic = $user['transfer_enable'];
        $TenSNI = $user['dname_sni'];
        $remainingTraffic = Helper::trafficConvert($totalTraffic - $useTraffic);
        $expiredDate = $user['expired_at'] ? date('d-m-Y', $user['expired_at']) : 'Vĩnh viễn';
        $userService = new UserService();
        $resetDay = $userService->getResetDay($user);
        $userPlanId = $user['plan_id'];
        $v2Plan = Plan::find($userPlanId);
        $planName = $v2Plan ? $v2Plan->name : 'Không xác định';
        
        array_unshift($servers, array_merge($servers[0], [
            'name' => "Hạn SD: {$expiredDate}",
        ]));
        if ($resetDay) {
            array_unshift($servers, array_merge($servers[0], [
                'name' => "Reset data sau：{$resetDay} Ngày",
            ]));
        }
        array_unshift($servers, array_merge($servers[0], [
            'name' => "Data Còn lại：{$remainingTraffic}",
        ]));
        array_unshift($servers, array_merge($servers[0], [
            'name' => "SNI：{$TenSNI}",
        ]));
        array_unshift($servers, array_merge($servers[0], [
            'name' => "Gói: {$planName}",
        ]));
    
    }
}
