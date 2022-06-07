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
		xhr.open("PUT", "../../index.php/apps/uppush/keepalive/", true);
		xhr.setRequestHeader('Content-type','application/json; charset=utf-8');
		xhr.send(json);
	}
}

document.addEventListener('DOMContentLoaded', function(){
	document.getElementById("setKeepaliveBtn").addEventListener("click", setKeepalive);
});
