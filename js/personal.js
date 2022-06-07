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
    alert(1)
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
