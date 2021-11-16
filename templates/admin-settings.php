<style>
table td, table th {
	padding: 5px;
	word-break: break-all;
}
</style>
<script nonce="<?php p(\OC::$server->getContentSecurityPolicyNonceManager()->getNonce()) ?>">
document.addEventListener('DOMContentLoaded', function(){
	document.getElementById("setKeepaliveBtn").addEventListener("click", setKeepalive);
});
function setKeepalive(){
	console.log("setKeepalive run...");
	var keepalive = document.getElementById("keepalive").value
	if (keepalive.length > 0){
		var xhr = new XMLHttpRequest();
		xhr.onreadystatechange = function() {
			if (this.readyState == 4 && this.status == 200) {
    				console.log("Keepalive=" + keepalive);
			}
		};
		var data = {};
		data.keepalive = keepalive;
		var json = JSON.stringify(data);
		xhr.open("PUT", "../../index.php/apps/uppush/setkeepalive/", true);
		xhr.setRequestHeader('Content-type','application/json; charset=utf-8');
		xhr.send(json);
	}
}
</script>
<div id="uppush-admin" class="section">
<h2>UnifiedPush Provider Settings</h2>
<p class="settings-hint">Set up the service and solve common problems.</p>
<p>This has been set up with the objective of making installation and configuration as painless as possible, however, there are certain requirements that must be met in order for the system to perform correctly.</p>
<br><div>
<b>1)</b> This service required Redis to be installed and configured for use by Nextcloud. The database ID used will be 1 higher than the database ID used by Nextcloud.<br>
<b>2)</b> Maximum PHP processes must be set sufficiently high. Each user connected for push messaging will require their own PHP process running, therefore you must set the process limits sufficiently high in order to serve not only the needs of all push clients, but other web clients as well. If you anticipate a maximum of 200 push clients to be connected simultaneously and require an additional 100 servers for web traffic, then you should set your process limit to at least 300. The parameter to adjust in your php-fpm configuration is <b>pm.max_children</b>.<br>
<br><h2>If a proxy is in use, including access to PHP being by way of fastCGI...</h2>
<b>3)</b> Elimination of buffering. Most web server stacks can be instructed to flush buffers by issuing a flush() in php, however, this most notably does not work when using php-fpm. This buffering can result in pushed messages being delayed. In order for messages to be delivered instantaneously, it is necessary to eliminate this additional buffering. In Apache, this can be done by adding the below to your php configuration;<br>
<b>
&lt;Proxy "fcgi://localhost/" disablereuse=on flushpackets=on max=10&gt;<br>
&lt;/Proxy&gt;<br></b><br>
<b>4)</b> Web server proxy timeout should be set to at least 10 minutes. Connection to PHP will typically be by way of fastCGI, which uses the proxy infrastructure of your web server. If you observe clients frequently reconnecting, typically at 1 minute intervals, it means that the proxy is timing out before the keep-alive message is issued. Default keep-alive's are issued at 300 second (5 minute) intervals. In Apache, you can add <b>ProxyTimeout 600</b> to your Nextcloud virtual host.<br>
<br>If it isn't possible to set the proxy timeout to a sensible amount of time, an alternative is to reduce the time interval between keep-alive messages so that they are issued within the available timeout, such as 55 seconds. This may cause an increase in power consumption for the mobile device.
<div><input type="text" id="keepalive" placeholder="Keep-alive interval" value="<?php echo filter_var ( $_['keepalive'], FILTER_SANITIZE_NUMBER_INT); ?>";>
<button class="button" id="setKeepaliveBtn">Set keep-alive</button>
</div>
</div>
</div>
