<?php

namespace App\Protocols;

use App\Utils\Helper;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class Shadowrocket
{
    public $flag = 'shadowrocket';
    private $servers;
    private $user;

    public function __construct($user, $servers)
    {
        $this->user = $user;
        $this->servers = $servers;
    }

    public function handle()
    {
        $servers = $this->servers;
        $user = $this->user;

        $uri = '';
        
        $upload = round($user['u'] / (1024*1024*1024), 2);
        $download = round($user['d'] / (1024*1024*1024), 2);
        $totalTraffic = round($user['transfer_enable'] / (1024*1024*1024), 2);
        $expiredDate = date('Y-m-d', $user['expired_at']);
        $uri .= "STATUS=🚀↑:{$upload}GB,↓:{$download}GB,TOT:{$totalTraffic}GB💡Expires:{$expiredDate}\r\n";
        foreach ($servers as $item) {
            if ($item['type'] === 'shadowsocks') {
                $uri .= self::buildShadowsocks($user['uuid'], $item);
            }
            if ($item['type'] === 'vmess') {
                $uri .= self::buildVmess($user['uuid'], $item);
            }
            if ($item['type'] === 'vless') {
                $uri .= self::buildVless($user['uuid'], $item);
            }
            if ($item['type'] === 'trojan') {
                $uri .= self::buildTrojan($user['uuid'], $item);
            }
            if ($item['type'] === 'hysteria') {
                $uri .= self::buildHysteria($user['uuid'], $item);
            }
        }
        return base64_encode($uri);
    }


    public static function buildShadowsocks($password, $server)
    {
        if ($server['cipher'] === '2022-blake3-aes-128-gcm') {
            $serverKey = Helper::getServerKey($server['created_at'], 16);
            $userKey = Helper::uuidToBase64($password, 16);
            $password = "{$serverKey}:{$userKey}";
        }
        if ($server['cipher'] === '2022-blake3-aes-256-gcm') {
            $serverKey = Helper::getServerKey($server['created_at'], 32);
            $userKey = Helper::uuidToBase64($password, 32);
            $password = "{$serverKey}:{$userKey}";
        }
        $name = rawurlencode($server['name']);
        $str = str_replace(
            ['+', '/', '='],
            ['-', '_', ''],
            base64_encode("{$server['cipher']}:{$password}")
        );
        return "ss://{$str}@{$server['host']}:{$server['port']}#{$name}\r\n";
    }

    public static function buildVmess($uuid, $server)
    {

        $IDuu = User::where('uuid', $uuid)->first();
        $sniSetting = isset($_GET['sni']) ? $_GET['sni'] : $IDuu->network_settings;
        
        
        $userinfo = base64_encode('auto:' . $uuid . '@' . $server['host'] . ':' . $server['port']);
        $config = [
            'tfo' => 1,
            'remark' => $server['name'],
            'alterId' => 0
        ];
        if ($server['tls']) {
            $config['tls'] = 1;
            if ($server['tlsSettings']) {
                $tlsSettings = $server['tlsSettings'];
                if (isset($tlsSettings['allowInsecure']) && !empty($tlsSettings['allowInsecure']))
                    $config['allowInsecure'] = (int)$tlsSettings['allowInsecure'];
                if (isset($tlsSettings['serverName']) && !empty($tlsSettings['serverName']))
                    $config['peer'] = $tlsSettings['serverName'];
            }
        }
        if ($server['network'] === 'tcp') {
            if ($server['networkSettings']) {
                $tcpSettings = $server['networkSettings'];
                if (isset($tcpSettings['header']['type']) && !empty($tcpSettings['header']['type']))
                    $config['obfs'] = $tcpSettings['header']['type'];
                if (isset($tcpSettings['header']['request']['path'][0]) && !empty($tcpSettings['header']['request']['path'][0]))
                    $config['path'] = $tcpSettings['header']['request']['path'][0];
                if (isset($tcpSettings['header']['request']['headers']['Host'][0]))
                    $config['obfs-host'] = $tcpSettings['header']['request']['headers']['Host'][0];
            }
        }
        if ($server['network'] === 'ws') {
            $config['obfs'] = "websocket";
            if ($server['networkSettings']) {
                $wsSettings = $server['networkSettings'];
                if (isset($wsSettings['path']) && !empty($wsSettings['path']))
                    $config['path'] = $wsSettings['path'];
                if (!empty($sniSetting))
                $config['obfsParam'] = $sniSetting;
                else
                if (isset($wsSettings['headers']['Host']) && !empty($wsSettings['headers']['Host']))
                    $config['obfsParam'] = $wsSettings['headers']['Host'];
            }
        }
        if ($server['network'] === 'grpc') {
            $config['obfs'] = "grpc";
            if ($server['networkSettings']) {
                $grpcSettings = $server['networkSettings'];
                if (isset($grpcSettings['serviceName']) && !empty($grpcSettings['serviceName']))
                    $config['path'] = $grpcSettings['serviceName'];
            }
            if (isset($tlsSettings)) {
                $config['host'] = $tlsSettings['serverName'];
            } else {
                $config['host'] = $server['host'];
            }
        }
        $query = http_build_query($config, '', '&', PHP_QUERY_RFC3986);
        $uri = "vmess://{$userinfo}?{$query}";
        $uri .= "\r\n";
        return $uri;
    }

    public static function buildVless($uuid, $server)
    {
        $IDuu = User::where('uuid', $uuid)->first();
        $sniSetting = isset($_GET['sni']) ? $_GET['sni'] : $IDuu->network_settings;
        
        $config = [
            "name" => Helper::encodeURIComponent($server['name']),
            "add" => $server['host'],
            "port" => (string)$server['port'],
            "type" => $server['network'],
            "encryption" => "none",
            "host" => "",
            "path" => "",
            "headerType" => "none",
            "quicSecurity" => "none",
            "serviceName" => "",
            "mode" => "gun",
            "security" => $server['tls'] !=0 ? ($server['tls'] == 2 ? "reality":"tls") : "",
            "flow" => $server['flow'],
            "fp" => isset($server['tls_settings']['fingerprint']) ? $server['tls_settings']['fingerprint'] : 'chrome',
            "sni" => "",
            "pbk" => "",
            "sid" =>"",
        ];

        $output = "vless://" . $uuid . "@" . $config['add'] . ":" . $config['port'];
        $output .= "?" . "type={$config['type']}" . "&encryption={$config['encryption']}" . "&security={$config['security']}";

        if ($server['tls']) {
            if ($config['flow'] != "") $output .= "&flow={$config['flow']}";
            if ($server['tls_settings']) {
                $tlsSettings = $server['tls_settings'];
                if (isset($tlsSettings['server_name']) && !empty($tlsSettings['server_name'])) $config['sni'] = $tlsSettings['server_name'];
                $output .= "&sni={$config['sni']}";
                if ($server['tls'] == 2) {
                    $config['pbk'] = $tlsSettings['public_key'];
                    $config['sid'] = $tlsSettings['short_id'];
                    $output .= "&pbk={$config['pbk']}" . "&sid={$config['sid']}";
                }
            }
        }
        if ((string)$server['network'] === 'tcp') {
            $tcpSettings = $server['network_settings'];
            if (isset($tcpSettings['header']['type']) && $tcpSettings['header']['type'] == 'http') {
                $config['headerType'] = $tcpSettings['header']['type'];
                if (isset($tcpSettings['header']['request']['headers']['Host'][0])) $config['host'] = $tcpSettings['header']['request']['headers']['Host'][0];
                if (isset($tcpSettings['header']['request']['path'][0])) $config['path'] = $tcpSettings['header']['request']['path'][0];
            }
            $output .= "&headerType={$config['headerType']}" . "&host={$config['host']}" . "&path={$config['path']}";
        }
        if ((string)$server['network'] === 'kcp') {
            $kcpSettings = $server['network_settings'];
            if (isset($kcpSettings['header']['type'])) $config['headerType'] = $kcpSettings['header']['type'];
            if (isset($kcpSettings['seed'])) $config['path'] = Helper::encodeURIComponent($kcpSettings['seed']);
            $output .= "&headerType={$config['headerType']}" . "&seed={$config['path']}";
        }
        if ((string)$server['network'] === 'ws') {
            $wsSettings = $server['network_settings'];
            if (isset($wsSettings['path'])) $config['path'] = Helper::encodeURIComponent($wsSettings['path']);
            if (!empty($sniSetting))
                $config['host'] = $sniSetting;
                else
            if (isset($wsSettings['headers']['Host'])) $config['host'] = Helper::encodeURIComponent($wsSettings['headers']['Host']);
            $output .= "&path={$config['path']}" . "&host={$config['host']}";
        }
        if ((string)$server['network'] === 'h2') {
            $h2Settings = $server['network_settings'];
            if (isset($h2Settings['path'])) $config['path'] = Helper::encodeURIComponent($h2Settings['path']);
            if (isset($h2Settings['host'])) $config['host'] = Helper::encodeURIComponent($h2Settings['host']);
            $output .= "&path={$config['path']}" . "&host={$config['host']}";
        }
        if ((string)$server['network'] === 'quic') {
            $quicSettings = $server['network_settings'];
            if (isset($quicSettings['security'])) $config['quicSecurity'] = $quicSettings['security'];
            if (isset($quicSettings['header']['type'])) $config['headerType'] = $quicSettings['header']['type'];

            $output .= "&quicSecurity={$config['quicSecurity']}" . "&headerType={$config['headerType']}";

            if ((string)$quicSettings['security'] !== 'none' && isset($quicSettings['key'])) $config['path'] = Helper::encodeURIComponent($quicSettings['key']);

            $output .= "&key={$config['path']}";
        }
        if ((string)$server['network'] === 'grpc') {
            $grpcSettings = $server['network_settings'];
            if (isset($grpcSettings['serviceName'])) $config['serviceName'] = Helper::encodeURIComponent($grpcSettings['serviceName']);
            if (isset($grpcSettings['multiMode'])) $config['mode'] = $grpcSettings['multiMode'] ? "multi" : "gun";
            $output .= "&serviceName={$config['serviceName']}" . "&mode={$config['mode']}";
        }

        $output .= "&fp={$config['fp']}" . "#" . $config['name'];

        return $output . "\r\n";
    }

    public static function buildTrojan($password, $server)
    {
        $IDuu = User::where('uuid', $password)->first();
        $sniSetting = isset($_GET['sni']) ? $_GET['sni'] : $IDuu->network_settings;
        if (isset($sniSetting))
            $sni = $sniSetting;
        else 
            $sni = $serveName;

        $name = rawurlencode($server['name']);
        $query = http_build_query([
            'allowInsecure' => $server['allow_insecure'],
            'peer' => $sni,
            'sni' => $sni
        ]);
        $uri = "trojan://{$password}@{$server['host']}:{$server['port']}?{$query}";
        if(isset($server['network']) && in_array($server['network'], ["grpc", "ws"])){
            $uri .= "&type={$server['network']}";
            if($server['network'] === "grpc" && isset($server['network_settings']['serviceName'])) {
                $uri .= "&serviceName={$server['network_settings']['serviceName']}";
            }
            if($server['network'] === "ws") {
                if(isset($server['network_settings']['path'])) {
                    $uri .= "&path={$server['network_settings']['path']}";
                }
                if(isset($server['network_settings']['headers']['Host'])) {
                    $uri .= "&host={$sni}";
                }
            }
        }
        
        $uri .= "#{$name}\r\n";
        return $uri;
    }

    public static function buildHysteria($password, $server)
    {   

        $remote = filter_var($server['host'], FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) ? '[' . $server['host'] . ']' : $server['host'];
     	$name = Helper::encodeURIComponent($server['name']);

        if ($server['version'] == 2) {
            $uri = "hysteria2://{$password}@{$remote}:{$server['port']}/?insecure={$server['insecure']}&sni={$server['server_name']}";
            if (isset($server['obfs']) && isset($server['obfs-password'])) {
                $uri .= "&obfs={$server['obfs']}&obfs-password={$server['obfs-password']}";
            }
        } else {
            $uri = "hysteria://{$remote}:{$server['port']}/?";
            $query = http_build_query([
                'protocol' => 'udp',
                'auth' => $password,
                'insecure' => $server['insecure'],
                'peer' => $server['server_name'],
                'upmbps' => $server['down_mbps'],
                'downmbps' => $server['up_mbps']
            ]);
            $uri .= $query;
            if (isset($server['obfs']) && isset($server['obfs-password'])) {
                $uri .= "&obfs={$server['obfs']}&obfsParam{$server['obfs-password']}";
            }
        }
        $uri .= "#{$name}\r\n";
        return $uri;
    }
}
