<?php
namespace Seekerliu\DynamicDns;

use GuzzleHttp\Client;

class Dns
{
    private $ip;
    private $options;
    private $baseUri;
    private $getIpUri;
    private $domain;
    private $subDomain;
    private $enableLog;

    public function __construct()
    {
        //设置DNSPOD需要的User-Agent,login_token,format参数
        $this->options['headers']['User-Agent'] = env('DDNS_DNSPOD_UA');
        $this->options['form_params'] = [
            'login_token' => join(',', [env('DDNS_DNSPOD_ID') , env('DDNS_DNSPOD_TOKEN')]),
            'format' => 'json',
        ];

        $this->baseUri = env('DDNS_DNSPOD_URI');  // DNSPOD api
        $this->domain = env('DDNS_DOMAIN');   // 域名
        $this->subDomain = env('DDNS_SUB_DOMAIN');    // 二级域名

        $this->getIpUri = env('DDNS_GET_IP_URI');   // 获取本机公网 ip 的 API
        $this->enableLog = env('DDNS_ENABLE_LOG', true);    // 是否开启日志
    }

    /**
     * 执行
     */
    public function run()
    {
        //检查IP是否有变化, 如果有变化则更新DNS
        if($ip = $this->checkIpUpdate()) {
            $this->updateDnsRecord($ip);
        }
    }

    /**
     * 获取本地的公网IP
     */
    public function getCurrentIp()
    {
        $client = new Client(['base_uri' => $this->getIpUri]);
        $response = $client->request('GET');
        $ip = json_decode($response->getBody())->ip;
        return $ip;
    }

    /**
     * 更新record记录
     * @param $ip
     */
    public function updateDnsRecord($ip)
    {
        $record = $this->getRecord($this->domain, $this->subDomain);

        $options = $this->options;
        $options['form_params']['domain'] = $this->domain;  //域名
        $options['form_params']['sub_domain'] = $this->subDomain;   //子域名
        $options['form_params']['record_id'] = $record->id; //记录id
        $options['form_params']['record_line_id'] = $record->line_id;   //记录线路id
        $options['form_params']['value'] = $ip; //IP

        //更新DNS
        $response = $this->http('POST', 'Record.Ddns', $options);

        //更新缓存
        if($response->status->code == 1) {
            $this->fetchAndCacheRecordList($this->domain);

            //记录日志
            if($this->enableLog) {
                \Log::info('已更新IP为:', [$response->record->value]);
            }
        }

    }

    /**
     * 根据域名,子域名获取子域名对应的record_id
     * @param $domain
     * @param $subDomain
     * @return mixed
     */
    public function getRecord($domain, $subDomain) {
        $recordList = $this->getRecordList($domain);

        foreach($recordList as $record) {
            if($record->name==$subDomain) {
                return $record;
            }
        }
    }

    /**
     * 获取域名的解析列表, 并返回名子域名的解析记录
     * @param $domain
     * @return false|mixed
     */
    public function getRecordList($domain)
    {
        if(!\Cache::has('recordList')) {
            $this->fetchAndCacheRecordList($domain);
        }

        return \Cache::get('recordList');
    }


    /**
     * 远程获取域名的解析列表并更新缓存
     * @param $domain
     */
    public function fetchAndCacheRecordList($domain)
    {
        $options = $this->options;
        $options['form_params']['domain'] = $domain;

        $recordList = $this->http('POST', 'Record.List', $options);
        if(isset($recordList->records)) {
            \Cache::put('recordList', $recordList->records, 3600);
            if($this->enableLog) {
                \Log::info('已缓存域名解析列表!');
            }
        }
    }

    /**
     * 检查IP是否产生变化
     */
    public function checkIpUpdate()
    {
        $lastIp = $this->getRecord($this->domain, $this->subDomain)->value;
        $currentIp = $this->getCurrentIp();
        if(!empty($currentIp) && !empty($lastIp) && $lastIp != $currentIp) {
            return $currentIp;
        }

        return false;
    }

    /**
     * 封装HTTP请求, 返回json_decode之后的数组
     * @param string $method
     * @param $uri
     * @param $options
     * @return mixed
     */
    public function http($method = 'POST', $uri, $options)
    {
        $client = new Client(['base_uri' => $this->baseUri]);
        $recordList = $client->request($method, $uri, $options);
        return json_decode($recordList->getBody());
    }
}