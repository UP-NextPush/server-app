<style>
table td, table th {
	padding: 5px;
	word-break: break-all;
}
</style>
<script nonce="<?php p(\OC::$server->getContentSecurityPolicyNonceManager()->getNonce()) ?>">
   function expand(id) {
        deviceId = id.split("toggle-")[1]
        document.getElementById("toggle-" + deviceId).innerHTML = "-";
        document.getElementById("toggle-" + deviceId).onclick = function() { shrink(id); };
        document.getElementById("table-" +  deviceId).removeAttribute("hidden")
    }
    function shrink(id) {
        deviceId = id.split("toggle-")[1]
        document.getElementById("toggle-" + deviceId).innerHTML = "+";
        document.getElementById("toggle-" + deviceId).onclick = function() { expand(id); };
        document.getElementById("table-" +  deviceId).setAttribute("hidden","1")
    }
    function deleteDevice(id) {
        deviceId = id.split("delete-")[1]
        if (confirm("Confirm to delete the device.")) {
            var xhr = new XMLHttpRequest();
            xhr.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                        console.log(deviceId + " deleted");
                        document.getElementById("li-"+deviceId).remove();
                        document.getElementById("table-"+deviceId).remove();
                }
            };
            xhr.open("DELETE", "../../index.php/apps/uppush/device/" + deviceId, true);
            xhr.send();
        }
    }
    function deleteApp(id) {
        appId = id.split("delete-")[1]
        var xhr = new XMLHttpRequest();
        xhr.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                    console.log(appId + " deleted");
                    document.getElementById("tr-"+appId).remove();
            }
        };
        xhr.open("DELETE", "../../index.php/apps/uppush/app/" + appId, true);
        xhr.send();
    }
    document.addEventListener('DOMContentLoaded', function(){
        var toggleDeviceButtons = document.getElementsByClassName("toggle-device");
        for (let i = 0; i < toggleDeviceButtons.length; i++) {
            shrink(toggleDeviceButtons[i].id);
        }
        var deleteDeviceButtons = document.getElementsByClassName("delete-device");
        for (let i = 0; i < deleteDeviceButtons.length; i++) {
            document.getElementById(deleteDeviceButtons[i].id).onclick = function() { deleteDevice(deleteDeviceButtons[i].id) };
        }
        var deleteAppButtons = document.getElementsByClassName("delete-app");
        for (let i = 0; i < deleteAppButtons.length; i++) {
            document.getElementById(deleteAppButtons[i].id).onclick = function() { deleteApp(deleteAppButtons[i].id) };
        }
    });
</script>
<div id="uppush-inst" class="section">
<h2>UnifiedPush Provider</h2>
<br>
<p>This service, along with the Distributor application, allows messages to be delivered to application supporting UnifiedPush. Like Firebase Cloud Messaging works with Google Play Service.</p>
<br>
<p>The Distributor application source code and issue tracker can be found here;<br>
<a style="color: blue; text-decoration: underline;" href="#soon">#soon</a></p>
<br>
<br>
<a href="#soon" download="sooon"><button class="button">Install from Github Release</button></a>
<br>
</div>
<div id="uppush-auth" class="section">
<h2>Registered Devices</h2>
<p class="settings-hint">List of registered devices and applications.</p>
    <ul>
<?php
$devices = $_['devices'];
foreach($devices as $device) {
    $deviceName = filter_var($device['name'],FILTER_SANITIZE_STRING);
    $deviceDate = filter_var($device['date'],FILTER_SANITIZE_STRING);
    $deviceToken = filter_var($device['token'],FILTER_SANITIZE_STRING);
    echo "
        <li id='li-".$deviceToken."'>".$deviceName." (".$deviceDate.") <button id='toggle-".$deviceToken."' class='toggle-device'> </button> <button id='delete-".$deviceToken."' class='delete-device'>Delete</button></li>
        <table cellpadding=3 id='table-".$deviceToken."'>
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
        $appName = filter_var($app['name'],FILTER_SANITIZE_STRING);
        $appDate = filter_var($app['date'],FILTER_SANITIZE_STRING);
        $appToken = filter_var($app['token'],FILTER_SANITIZE_STRING);
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
