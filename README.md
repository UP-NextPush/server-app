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

_Nginx_: - Server Block
```
proxy_connect_timeout   10m;
proxy_send_timeout      10m;
proxy_read_timeout      10m;
```

_Apache_: - VirtalHost Block
```
ProxyTimeout 600
```

3. The reverse-proxy need to be configured without buffering :

_Nginx_: - Server Block
```
proxy_buffering off;
```

_Apache_ (php configuration): - VirtalHost Block
```
<Proxy "fcgi://localhost/" disablereuse=on flushpackets=on max=10>
</Proxy>
```

## Gateways

The app can be used as a personal matrix gateway. It requires to pass requests to the path `/_matrix/push/v1/notify` to `/index.php/apps/uppush/gateway/matrix`.

_Nginx_: - Server Block
```
location /_matrix/push/v1/notify {
    proxy_pass http://127.0.0.1:5000/index.php/apps/uppush/gateway/matrix;
}
```

_Apache_: - VirtalHost Block
```
ProxyPass "/_matrix/push/v1/notify" http://127.0.0.1:5000/index.php/apps/uppush/gateway/matrix
```

## Credit

This application has been inspired by [Nextcloud Push Notifier](https://gitlab.com/Nextcloud-Push/direct-push-proxy-v2)
