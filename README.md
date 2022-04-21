# NextPush - Server App
UnifiedPush provider for Nextcloud - server application 
## Requirement
[Your Nextcloud instance needs to have a Redis server installed and listening for connections.](https://docs.nextcloud.com/server/latest/admin_manual/configuration_server/caching_configuration.html)
## Installation
1. The app had to be installed to __nextcloud/apps/uppush__ :
```
git clone https://github.com/UP-NextPush/server-app/ nextcloud/apps/uppush
```
2. The reverse-proxy need to be configured for long timeout :

_Nginx_: - **What specific file does this configuration need to be added to?**
```
proxy_connect_timeout   10m;
proxy_send_timeout      10m;
proxy_read_timeout      10m;
```
_Apache_: - **What specific file does this configuration need to be added to?**
```
ProxyTimeout 600
```
3. The reverse-proxy need to be configured without buffering :

_Nginx_: - **What specific file does this configuration need to be added to?**
```
proxy_buffering off;
```
_Apache_ (php configuration): - **What specific file does this configuration need to be added to?**
```
<Proxy "fcgi://localhost/" disablereuse=on flushpackets=on max=10>
</Proxy>
```
## Gateways
The app can be used as a personal matrix gateway. It requires to pass requests to the path `/_matrix/push/v1/notify` to `/index.php/apps/uppush/gateway/matrix`

_Nginx_: - **What specific file does this configuration need to be added to?**
```
location /_matrix/push/v1/notify {
    proxy_pass http://127.0.0.1:5000/index.php/apps/uppush/gateway/matrix;
}
```
_Apache_: - **What specific file does this configuration need to be added to?**
```
ProxyPass "/_matrix/push/v1/notify" http://127.0.0.1:5000/index.php/apps/uppush/gateway/matrix
```
[Once completed head over and get the android app setup to begin testing.](https://github.com/UP-NextPush/android)
## Credit
This application has been inspired by [Nextcloud Push Notifier](https://gitlab.com/Nextcloud-Push/direct-push-proxy-v2)
