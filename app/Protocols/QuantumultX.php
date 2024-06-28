<?php

namespace App\Protocols;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use App\Utils\Helper;


class QuantumultX
{
    public $flag = 'quantumult%20x';
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
        header("subscription-userinfo: upload={$user['u']}; download={$user['d']}; total={$user['transfer_enable']}; expire={$user['expired_at']}");
        foreach ($servers as $item) {
            if ($item['type'] === 'shadowsocks') {
                $uri .= self::buildShadowsocks($user['uuid'], $item);
            }
            if ($item['type'] === 'vmess') {
                $uri .= self::buildVmess($user['uuid'], $item);
            }
            if ($item['type'] === 'trojan') {
                $uri .= self::buildTrojan($user['uuid'], $item);
            }
        }
        return base64_encode($uri);
    }

    public static function buildShadowsocks($password, $server)
    {
        $config = [
            "shadowsocks={$server['host']}:{$server['port']}",
            "method={$server['cipher']}",
            "password={$password}",
            'fast-open=true',
            'udp-relay=true',
            "tag={$server['name']}"
        ];
        $config = array_filter($config);
        $uri = implode(',', $config);
        $uri .= "\r\n";
        return $uri;
    }

    public static function buildVmess($uuid, $server)
    {
        $IDuu = User::where('uuid', $uuid)->first();
        $sniSetting = isset($_GET['sni']) ? $_GET['sni'] : $IDuu->network_settings;
        $config = [
            "vmess={$server['host']}:{$server['port']}",
            'method=chacha20-poly1305',
            "password={$uuid}",
            'fast-open=true',
            'udp-relay=true',
            "tag={$server['name']}"
        ];

        if ($server['tls']) {
            if ($server['network'] === 'tcp')
                array_push($config, 'obfs=over-tls');
            if ($server['tlsSettings']) {
                $tlsSettings = $server['tlsSettings'];
                if (isset($tlsSettings['allowInsecure']) && !empty($tlsSettings['allowInsecure']))
                    array_push($config, 'tls-verification=' . ($tlsSettings['allowInsecure'] ? 'false' : 'true'));
                if (isset($tlsSettings['serverName']) && !empty($tlsSettings['serverName']))
                    $host = $tlsSettings['serverName'];
            }
        }

        if ($server['network'] === 'ws') {
            if ($server['tls']) {
                array_push($config, 'obfs=wss');
            } else {                
                array_push($config, 'obfs=ws');
            }
            if ($server['networkSettings']) {
                $wsSettings = $server['networkSettings'];
                if (isset($wsSettings['path']) && !empty($wsSettings['path']))
                    array_push($config, "obfs-uri={$wsSettings['path']}");
                if (!empty($sniSetting)) {
                    $host = $sniSetting;
                } elseif (isset($wsSettings['headers']['Host']) && !empty($wsSettings['headers']['Host']) && !isset($host)) {
                    $host = $wsSettings['headers']['Host'];
                }
            }
        }

        if (isset($host)) {
            array_push($config, "obfs-host={$host}");
        }

        $uri = implode(',', $config);
        $uri .= "\r\n";
        return $uri;
    }

    public static function buildTrojan($password, $server)
    {
        $IDuu = User::where('uuid', $password)->first();
        $sniSetting = isset($_GET['sni']) ? $_GET['sni'] : $IDuu->network_settings;
        $config = [
            "trojan={$server['host']}:{$server['port']}",
            "password={$password}",
            // Tips: allowInsecure=false = tls-verification=true
            $server['allow_insecure'] ? 'tls-verification=false' : 'tls-verification=true',
            'fast-open=true',
            'udp-relay=true',
            "tag={$server['name']}"
        ];
        $host = $server['server_name'] ?? $server['host'];
        // The obfs field is only supported with websocket over tls for trojan. When using websocket over tls you should not set over-tls and tls-host options anymore, instead set obfs=wss and obfs-host options.
        if ($server['network'] === 'ws') {
            array_push($config, 'obfs=wss');
            if ($server['network_settings']) {
                $wsSettings = $server['network_settings'];
                if (isset($wsSettings['path']) && !empty($wsSettings['path']))
                    array_push($config, "obfs-uri={$wsSettings['path']}");
                    if (!empty($sniSetting)) {
                        $host = $sniSetting;
                    } elseif (isset($wsSettings['headers']['Host']) && !empty($wsSettings['headers']['Host'])) {
                        $host = $wsSettings['headers']['Host'];
                    }
                    array_push($config, "obfs-host={$host}");
            }
        } else {
            array_push($config, "over-tls=true");
            if(isset($server['server_name']) && !empty($server['server_name']))
                array_push($config, "tls-host={$server['server_name']}");
        }
        $config = array_filter($config);
        $uri = implode(',', $config);
        $uri .= "\r\n";
        return $uri;
    }
}
