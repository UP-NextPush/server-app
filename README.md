# NextPush - Server App

UnifiedPush provider for Nextcloud - server application 

## Requirement

[Your Nextcloud instance needs to have a Redis server installed and listening for connections.](https://docs.nextcloud.com/server/latest/admin_manual/configuration_server/caching_configuration.html)

## Installation

1.) Clone the git repository to __<your_nextcloud_dir>/apps/uppush__ :

```
cd /<nextcloud_dir>/apps/
git clone https://github.com/UP-NextPush/server-app/ nextcloud/apps/uppush
```

2.) Configure your reverse proxy.  The reverse proxy needs to be configured as a personal matrix gateway, with long timeout, and without buffering.

### nginx

Add the following to the end of your Nextcloud nginx configuration, replacing `your.nextcloud.tld` with your instance:

```
...

    location /_matrix/push/v1/notify {
        proxy_pass http://<your.nextcloud.tld>/index.php/apps/uppush/gateway/matrix;
        proxy_connect_timeout   10m;
        proxy_send_timeout      10m;
        proxy_read_timeout      10m;
        proxy_buffering off;
    }
```

### apache

Add the following inside your `VirtualHost` block: 

```
...

        ProxyTimeout 600
        <Proxy "fcgi://localhost/" disablereuse=on flushpackets=on max=10>
        </Proxy>
        ProxyPass "/_matrix/push/v1/notify" http://127.0.0.1:5000/index.php/apps/uppush/gateway/matrix
```



## Credit

This application has been inspired by [Nextcloud Push Notifier](https://gitlab.com/Nextcloud-Push/direct-push-proxy-v2)
