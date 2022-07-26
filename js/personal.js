function expand(id) {
    let deviceId = id.split("toggle-")[1]
    document.getElementById("toggle-" + deviceId).innerHTML = "-"
    document.getElementById("toggle-" + deviceId).onclick = function() { shrink(id) }
    document.getElementById("table-" +  deviceId).removeAttribute("hidden")
}
function shrink(id) {
    let deviceId = id.split("toggle-")[1]
    document.getElementById("toggle-" + deviceId).innerHTML = "+"
    document.getElementById("toggle-" + deviceId).onclick = function() { expand(id) }
    document.getElementById("table-" +  deviceId).setAttribute("hidden","1")
}
function deleteDevice(id) {
    let deviceId = id.split("delete-")[1]
    if (confirm("Confirm to delete the device.")) {
        let xhr = new XMLHttpRequest()
        xhr.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                    console.log(deviceId + " deleted")
                    document.getElementById("li-"+deviceId).remove()
                    document.getElementById("table-"+deviceId).remove()
            }
        }
        xhr.open("DELETE", "../../apps/uppush/device/" + deviceId, true)
        xhr.send()
    }
}
function deleteApp(id) {
    let appId = id.split("delete-")[1]
    let xhr = new XMLHttpRequest()
    xhr.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
                console.log(appId + " deleted")
                document.getElementById("tr-"+appId).remove()
        }
    }
    xhr.open("DELETE", "../../apps/uppush/app/" + appId, true)
    xhr.send()
}
document.addEventListener('DOMContentLoaded', function(){
    document.getElementsByClassName("toggle-device").forEach((el) => {
        shrink(el.id)
    })
    document.getElementsByClassName("delete-device").forEach((el) => {
        el.onclick = function() {deleteDevice(el.id)}
    })
    document.getElementsByClassName("delete-app").forEach((el) => {
        el.onclick = function() {deleteApp(el.id)}
    })
})
