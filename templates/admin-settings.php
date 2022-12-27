<?php script("uppush", "admin"); ?>
<style>
table td, table th {
	padding: 5px;
	word-break: break-all;
}
</style>
<div id="uppush-admin" class="section">
<h2>UnifiedPush Provider Settings</h2>
<p>The objective of this service is to make installation and configuration as easy as possible, but there are certain requirements that must be met for the system to work correctly.</p>
<br><div>
<ol>
<li><b>The service requires Redis to be installed and configured for use by Nextcloud.</b></li>
<li>The maximum number of PHP processes must be set high enough. Each user connected for push messaging will need their own PHP process running, so you must set the process limits high enough to serve not only the needs of all push clients, but also other web clients. For example, if you expect a maximum of 200 push clients to be connected simultaneously and an additional 100 servers for web traffic, you should set your process limit to at least 300. The parameter to adjust in your PHP-FPM configuration is <code>pm.max_children</code>.</li>
<li><b>Buffering must be disabled.</b> Most web server stacks can be instructed to flush buffers by issuing a flush() in PHP, but this does not work when using PHP-FPM. This buffering can cause delayed pushed messages. To ensure that messages are delivered instantly, it is necessary to eliminate this additional buffering.</li>
<li><b>In order to reduce battery consumption, the web server proxy timeout should be set to 360 seconds or higher.</b> Connection to PHP is typically through fastCGI, which uses the proxy infrastructure of your web server. If you see clients frequently reconnecting, usually at 1 minute intervals, it means that the proxy is timing out before the keep-alive message is sent. <b>Once the proxy timeout is configured, you can set increase timeout of this application to 300 seconds.</b></li>
</ol>
<br>
<div><input type="text" id="keepalive" placeholder="Keep-alive interval" value="<?php echo filter_var ( $_['keepalive'], FILTER_SANITIZE_NUMBER_INT); ?>";>
<button class="button" id="setKeepaliveBtn">Set keep-alive</button>
</div>
</div>
</div>
