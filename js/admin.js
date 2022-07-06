function setKeepalive() {
	let keepalive = document.getElementById("keepalive").value
	if (keepalive.length > 0) {
		let xhr = new XMLHttpRequest()
		xhr.onreadystatechange = function() {
			if (this.readyState == 4 && this.status == 200) {
				alert("Keepalive changed to " + keepalive)
			}
		}
		let data = {}
		data.keepalive = keepalive
		let json = JSON.stringify(data)
		xhr.open("PUT", "../../index.php/apps/uppush/keepalive/", true)
		xhr.setRequestHeader('Content-type','application/json charset=utf-8')
		xhr.send(json)
	}
}

document.addEventListener('DOMContentLoaded', function() {
	document.getElementById("setKeepaliveBtn").addEventListener("click", setKeepalive)
})
