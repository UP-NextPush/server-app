<?php script("uppush", "personal"); ?>
<style>
table td, table th {
	padding: 5px;
	word-break: break-all;
}
</style>
<div id="uppush-inst" class="section">
<h2>UnifiedPush Provider</h2>
<br>
<p>This service, along with the Distributor application, allows messages to be delivered to application supporting UnifiedPush. Like Firebase Cloud Messaging works with Google Play Service.</p>
<br>
<p>The Distributor application source code and issue tracker can be found here;<br>
<a style="color: blue; text-decoration: underline;" href="https://github.com/UP-NextPush/android">https://github.com/UP-NextPush/android</a></p>
<br>
<br>
<a href="https://github.com/UP-NextPush/android/releases"><button class="button">Install from Github Release</button></a>
<br>
</div>
<div id="uppush-auth" class="section">
<h2>Registered Devices</h2>
<p class="settings-hint">List of registered devices and applications.</p>
    <ul>
<?php
$devices = $_['devices'];
foreach($devices as $device) {
    $deviceName = filter_var($device['name'],FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $deviceDate = filter_var($device['date'],FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $deviceToken = filter_var($device['token'],FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    echo "
        <li id='li-".$deviceToken."'>".$deviceName." (".$deviceDate.") <button id='toggle-".$deviceToken."' class='toggle-device'>+</button> <button id='delete-".$deviceToken."' class='delete-device'>Delete</button></li>
        <table cellpadding=3 id='table-".$deviceToken."' hidden>
            <thead>
                <tr>
                    <th></th>
                    <th><b>Application</b></th>
                    <th><b>Date</b></th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
    ";
    $apps = $device['apps'];
    foreach($apps as $app) {
        $appName = filter_var($app['name'],FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $appDate = filter_var($app['date'],FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $appToken = filter_var($app['token'],FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        echo "
                <tr id='tr-".$appToken."'>
                    <th></th>
                    <th>".$appName."</th>
                    <th>".$appDate."</th>
                    <th><button id='delete-".$appToken."' class='delete-app'>Delete</button></th>
                </tr>
        ";
    }
    echo "
                <tr><th> </th></tr>
            </tbody>
        </table>
";
}
?>
    </ul>
