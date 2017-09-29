# Laravel DDns
A DDns tool for Laravel.    
利用 DNSPod 的 API 实现 DDNS。    


## 安装
1. 安装扩展包：
```bash
composer require seekerliu/laravel-ddns:dev-master
```
2. 注册 DNSPod 账户，并获取 ID 及 TOKEN，方法请参考: https://support.dnspod.cn/Kb/showarticle/tsid/227/

## 配置

> Laravel 5.5 中有 `Package Discover` 功能，故无需进行第 1 步。如未起作用，可手动执行 `php artisan package:discover` 命令。

1. 在 `config/app.php` 中添加此行:
```php
  'providers' => [
      //...
      
      Seekerliu\DynamicDns\ServiceProvider::class,
  ],
```

2. 配置 `.env` 文件：
```
# 你的 DNSPOD ID，必填
DDNS_DNSPOD_ID=
# 你的 DNSPOD TOKEN，必填
DDNS_DNSPOD_TOKEN=
# 你在 DNSPOD 解析的根域名，例如: seekerliu.com，必填
DDNS_DOMAIN=
# 你在 DNSPOD 解析的二级域名，例如：blog，必填
DDNS_SUB_DOMAIN=

# 访问 DNSPOD API 需要的 UA，不用改
DDNS_DNSPOD_UA="Laravel DDNS Client/0.0.1 (seekerliu@vip.qq.com)"
# DNSPOD API URI，不用改
DDNS_DNSPOD_URI=https://dnsapi.cn/
# 获取 json 格式的公网 ip，可以换成自己的
DDNS_GET_IP_URI=https://seekerliu.com/getip.php
# 是否开启日志
DDNS_ENABLE_LOG=true
```