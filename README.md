# NextPush - Server App
UnifiedPush provider for Nextcloud - server application 

_This is still a beta version_

## Requirement

It require the nextcloud server to be installed with Redis.

## Installation

1. The app had to be installed to __nextcloud/apps/uppush__ :
```
git clone https://github.com/UP-NextPush/server-app/ nextcloud/apps/uppush
```
2. The reverse-proxy need to be configured for long timeout :

_Nginx_:
```
    proxy_connect_timeout   10m;
    proxy_send_timeout      10m;
    proxy_read_timeout      10m;
```
_Apache_:
```
    ProxyTimeout 600
```
3. The reverse-proxy need to be configured without buffering :
_Nginx_:
```
    proxy_buffering off;
```
_Apache_ (php configuration):
```
    <Proxy "fcgi://localhost/" disablereuse=on flushpackets=on max=10>
    </Proxy>
```

## Credit

This application has been inspired by [Nextcloud Push Notifier](https://gitlab.com/Nextcloud-Push/direct-push-proxy-v2)
