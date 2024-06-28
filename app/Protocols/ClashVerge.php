<?php

namespace App\Protocols;

use App\Utils\Helper;
use Symfony\Component\Yaml\Yaml;

class ClashVerge
{
    public $flag = 'verge';
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
        $appName = config('v2board.app_name', 'V2Board');
        header("subscription-userinfo: upload={$user['u']}; download={$user['d']}; total={$user['transfer_enable']}; expire={$user['expired_at']}");
        header('profile-update-interval: 24');
        header("content-disposition:attachment;filename*=UTF-8''".rawurlencode($appName));
        $defaultConfig = base_path() . '/resources/rules/default.clash.yaml';
        $customConfig = base_path() . '/resources/rules/custom.clash.yaml';
        if (\File::exists($customConfig)) {
            $config = Yaml::parseFile($customConfig);
        } else {
            $config = Yaml::parseFile($defaultConfig);
        }
        $proxy = [];
        $proxies = [];

        foreach ($servers as $item) {
            if ($item['type'] === 'shadowsocks') {
                array_push($proxy, self::buildShadowsocks($user['uuid'], $item));
                array_push($proxies, $item['name']);
            }
            if ($item['type'] === 'vmess') {
                array_push($proxy, self::buildVmess($user['uuid'], $item));
                array_push($proxies, $item['name']);
            }
            if ($item['type'] === 'vless') {
                array_push($proxy, self::buildVless($user['uuid'], $item));
                array_push($proxies, $item['name']);
            }
            if ($item['type'] === 'trojan') {
                array_push($proxy, self::buildTrojan($user['uuid'], $item));
                array_push($proxies, $item['name']);
            }
            if ($item['type'] === 'hysteria') {
                array_push($proxy, self::buildHysteria($user['uuid'], $item));
                array_push($proxies, $item['name']);
            }
        }

        $config['proxies'] = array_merge($config['proxies'] ? $config['proxies'] : [], $proxy);
        foreach ($config['proxy-groups'] as $k => $v) {
            if (!is_array($config['proxy-groups'][$k]['proxies'])) $config['proxy-groups'][$k]['proxies'] = [];
            $isFilter = false;
            foreach ($config['proxy-groups'][$k]['proxies'] as $src) {
                foreach ($proxies as $dst) {
                    if (!$this->isRegex($src)) continue;
                    $isFilter = true;
                    $config['proxy-groups'][$k]['proxies'] = array_values(array_diff($config['proxy-groups'][$k]['proxies'], [$src]));
                    if ($this->isMatch($src, $dst)) {
                        array_push($config['proxy-groups'][$k]['proxies'], $dst);
                    }
                }
                if ($isFilter) continue;
            }
            if ($isFilter) continue;
            $config['proxy-groups'][$k]['proxies'] = array_merge($config['proxy-groups'][$k]['proxies'], $proxies);
        }
        $config['proxy-groups'] = array_filter($config['proxy-groups'], function($group) {
            return $group['proxies'];
        });
        $config['proxy-groups'] = array_values($config['proxy-groups']);
        // Force the current subscription domain to be a direct rule
        $subsDomain = $_SERVER['HTTP_HOST'];
        if ($subsDomain) {
            array_unshift($config['rules'], "DOMAIN,{$subsDomain},DIRECT");
        }

        $yaml = Yaml::dump($config, 2, 4, Yaml::DUMP_EMPTY_ARRAY_AS_SEQUENCE);
        $yaml = str_replace('$app_name', config('v2board.app_name', 'V2Board'), $yaml);
        return $yaml;
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
        $array = [];
        $array['name'] = $server['name'];
        $array['type'] = 'ss';
        $array['server'] = $server['host'];
        $array['port'] = $server['port'];
        $array['cipher'] = $server['cipher'];
        $array['password'] = $password;
        $array['udp'] = true;
        return $array;
    }

    public static function buildVmess($uuid, $server)
    {
        $array = [];
        $array['name'] = $server['name'];
        $array['type'] = 'vmess';
        $array['server'] = $server['host'];
        $array['port'] = $server['port'];
        $array['uuid'] = $uuid;
        $array['alterId'] = 0;
        $array['cipher'] = 'auto';
        $array['udp'] = true;

        if ($server['tls']) {
            $array['tls'] = true;
            if ($server['tlsSettings']) {
                $tlsSettings = $server['tlsSettings'];
                if (isset($tlsSettings['allowInsecure']) && !empty($tlsSettings['allowInsecure']))
                    $array['skip-cert-verify'] = ($tlsSettings['allowInsecure'] ? true : false);
                if (isset($tlsSettings['serverName']) && !empty($tlsSettings['serverName']))
                    $array['servername'] = $tlsSettings['serverName'];
            }
        }
        if ($server['network'] === 'tcp') {
            $tcpSettings = $server['networkSettings'];
            if (isset($tcpSettings['header']['type']) && $tcpSettings['header']['type'] == 'http') {
                $array['network'] = $tcpSettings['header']['type'];
                if (isset($tcpSettings['header']['request']['headers']['Host'])) $array['http-opts']['headers']['Host'] = $tcpSettings['header']['request']['headers']['Host'];
                if (isset($tcpSettings['header']['request']['path'])) $array['http-opts']['path'] = $tcpSettings['header']['request']['path'];
            }
        }
        if ($server['network'] === 'ws') {
            $array['network'] = 'ws';
            if ($server['networkSettings']) {
                $wsSettings = $server['networkSettings'];
                $array['ws-opts'] = [];
                if (isset($wsSettings['path']) && !empty($wsSettings['path']))
                    $array['ws-opts']['path'] = $wsSettings['path'];
                if (isset($wsSettings['headers']['Host']) && !empty($wsSettings['headers']['Host']))
                    $array['ws-opts']['headers'] = ['Host' => $wsSettings['headers']['Host']];

            }
        }
        if ($server['network'] === 'grpc') {
            $array['network'] = 'grpc';
            if ($server['networkSettings']) {
                $grpcSettings = $server['networkSettings'];
                $array['grpc-opts'] = [];
                if (isset($grpcSettings['serviceName'])) $array['grpc-opts']['grpc-service-name'] = $grpcSettings['serviceName'];
            }
        }

        return $array;
    }

    public static function buildVless($uuid, $server)
    {
        $array = [];
        $array['name'] = $server['name'];
        $array['type'] = 'vless';
        $array['server'] = $server['host'];
        $array['port'] = $server['port'];
        $array['uuid'] = $uuid;
        $array['udp'] = true;
        
        if ($server['tls']) {
            $array['tls'] = true;
            $array['skip-cert-verify'] = isset($server['tls_settings']['allow_insecure']) && $server['tls_settings']['allow_insecure'] == 1 ? true : false;
            $array['flow'] = !empty($server['flow']) ? $server['flow']: "";
            $array['client-fingerprint'] = !empty($server['tls_settings']['fingerprint']) ? $server['tls_settings']['fingerprint'] : 'chrome';
            if ($server['tls_settings']) {
                $tlsSettings = $server['tls_settings'];
                if (isset($tlsSettings['server_name']) && !empty($tlsSettings['server_name']))
                   $array['servername'] = $tlsSettings['server_name'];
                if ($server['tls'] == 2) {
                   $array['reality-opts'] = [];
                   $array['reality-opts']['public-key'] = $tlsSettings['public_key'];
                   $array['reality-opts']['short-id'] = $tlsSettings['short_id'];
                }
            }
        }

        if ($server['network'] === 'tcp') {
            $tcpSettings = $server['network_settings'];
            if (isset($tcpSettings['header']['type']) && $tcpSettings['header']['type'] == 'http') {
                $array['network'] = $tcpSettings['header']['type'];
                if (isset($tcpSettings['header']['request']['headers']['Host'])) $array['http-opts']['headers']['Host'] = $tcpSettings['header']['request']['headers']['Host'];
                if (isset($tcpSettings['header']['request']['path'])) $array['http-opts']['path'] = $tcpSettings['header']['request']['path'];
            }
        }

        if ($server['network'] === 'ws') {
            $array['network'] = 'ws';
            if ($server['network_settings']) {
                $wsSettings = $server['network_settings'];
                $array['ws-opts'] = [];
                if (isset($wsSettings['path']) && !empty($wsSettings['path']))
                    $array['ws-opts']['path'] = $wsSettings['path'];
                if (isset($wsSettings['headers']['Host']) && !empty($wsSettings['headers']['Host']))
                    $array['ws-opts']['headers'] = ['Host' => $wsSettings['headers']['Host']];
            }
        }
        if ($server['network'] === 'grpc') {
            $array['network'] = 'grpc';
            if ($server['network_settings']) {
                $grpcSettings = $server['network_settings'];
                $array['grpc-opts'] = [];
                if (isset($grpcSettings['serviceName'])) $array['grpc-opts']['grpc-service-name'] = $grpcSettings['serviceName'];
            }
        }
        if ($server['network'] === 'h2') {
            $array['network'] = 'h2';
            if ($server['network_settings']) {
                $h2Settings = $server['network_settings'];
                $array['h2-opts'] = [];
                if (isset($h2Settings['host'])) $array['h2-opts']['host'] = array($h2Settings['host']);
                if (isset($h2Settings['path'])) $array['h2-opts']['path'] = $h2Settings['path'];
            }
        }
        return $array;
    }

    public static function buildTrojan($password, $server)
    {
        $array = [];
        $array['name'] = $server['name'];
        $array['type'] = 'trojan';
        $array['server'] = $server['host'];
        $array['port'] = $server['port'];
        $array['password'] = $password;
        $array['udp'] = true;
        if(isset($server['network']) && in_array($server['network'], ["grpc", "ws"])){
            $array['network'] = $server['network'];
            // grpc配置
            if($server['network'] === "grpc" && isset($server['network_settings']['serviceName'])) {
                $array['grpc-opts']['grpc-service-name'] = $server['network_settings']['serviceName'];
            }
            // ws配置
            if($server['network'] === "ws") {
                if(isset($server['network_settings']['path'])) {
                    $array['ws-opts']['path'] = $server['network_settings']['path'];
                }
                if(isset($server['network_settings']['headers']['Host'])){
                    $array['ws-opts']['headers']['Host'] = $server['network_settings']['headers']['Host'];
                }
            }
        };
        if (!empty($server['server_name'])) $array['sni'] = $server['server_name'];
        if (!empty($server['allow_insecure'])) $array['skip-cert-verify'] = ($server['allow_insecure'] ? true : false);
        return $array;
    }

    public static function buildHysteria($password, $server)
    {
        $array = [];
        $array['name'] = $server['name'];
        $array['server'] = $server['host'];
        $array['port'] = $server['port'];
        $array['udp'] = true;
        $array['skip-cert-verify'] = $server['insecure'] == 1 ? true : false;
    
        if (isset($server['server_name'])) $array['sni'] = $server['server_name'];

        if ($server['version'] === 2) {
            $array['type'] = 'hysteria2';
            $array['password'] = $password;
            if (isset($server['obfs'])){
                $array['obfs'] = $server['obfs'];
                $array['obfs-password'] = $server['obfs_password'];
            }
        } else {
            $array['type'] = 'hysteria';
            $array['auth_str'] = $password;
            if (isset($server['obfs']) && isset($server['obfs_password'])){
                $array['obfs'] = $server['obfs_password'];
            }
            //Todo:完善客户端上下行
            $array['up'] = $server['down_mbps'];
            $array['down'] = $server['up_mbps'];
            $array['protocol'] = 'udp';
        }

        return $array;
    }

    private function isMatch($exp, $str)
    {
        return @preg_match($exp, $str);
    }

    private function isRegex($exp)
    {
        return @preg_match($exp, null) !== false;
    }
}
