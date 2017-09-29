# Laravel DDns
A DDns tool for Laravel.    
利用 DNSPod 的 API 实现 DDNS。    


## 安装
1. 安装扩展包：
```bash
composer require seekerliu/laravel-ddns:dev-master
```
2. 注册 DNSPod 账户，并获取 ID 及 TOKEN，方法请参考: https://support.dnspod.cn/Kb/showarticle/tsid/227/

3. 你的服务器上需要需要开启 Laravel 调度计划：
Linux:
```bash
$ crontab -e
//添加下列一行：
* * * * * /path-to-php/php /path-to-your-project/artisan schedule:run >> /dev/null 2>&1
```
MacOS 可以在 Terminal 中使用下面命令临时代替，`Ctrl + C` 可终止：
```bash
while true; do php artisan schedule:run; sleep 60; done
```
4. 默认每分钟同步一次

## 配置

> Laravel 5.5 中有 `Package Discover` 功能，故无需进行第 1 步。如未起作用，可手动执行 `php artisan package:discover` 命令。

1. 在 `config/app.php` 中添加此行:
```php
  'providers' => [
      //...
      
      Seekerliu\DynamicDns\ServiceProvider::class,
  ],
```

2. 将下面的内容放到你的 `.env` 文件中：
```
DDNS_DNSPOD_ID=
DDNS_DNSPOD_TOKEN=
DDNS_DOMAIN=
DDNS_SUB_DOMAIN=

DDNS_DNSPOD_UA="Laravel DDNS Client/0.0.1 (seekerliu@vip.qq.com)"
DDNS_DNSPOD_URI=https://dnsapi.cn/
DDNS_GET_IP_URI=https://seekerliu.com/getip.php
DDNS_ENABLE_LOG=true
```

3. 配置 `.env` 文件：
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
# 获取 json 格式的公网 ip，可以换成自己的，源码在 getip.php 中
DDNS_GET_IP_URI=https://seekerliu.com/getip.php
# 是否开启日志
DDNS_ENABLE_LOG=true
```