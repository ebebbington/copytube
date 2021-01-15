let url = "ws://";
if (window.location.hostname === "copytube_nginx")
    // laravel dusk testing
    url += "copytube_realtime";
else url += window.location.hostname;
url += ":9008/realtime";

let Realtime = new WebSocket(url);
Realtime.onopen = function (e) {
    console.log("Realtime connection has opened");
};
Realtime.onclose = function (event) {
    if (event.wasClean) {
        console.log("Realtime connection closed");
    } else {
        console.log("Realtime connection died");
    }
};
Realtime.onerror = function (error) {
    console.error("Realtime encountered an error");
};
Realtime.onmessage = function (event) {
    try {
        const message = JSON.parse(event.data);
        switch (message.channel) {
            case "realtime.comments.new":
                //@ts-ignore
                if (Realtime.handleNewVideoComment)
                    Realtime.handleNewVideoComment(message);
                break;
            case "realtime.users.delete":
                //@ts-ignore
                if (Realtime.handleUserDeleted)
                    Realtime.handleUserDeleted(message);
                break;
        }
    } catch (err) {}
};

export default Realtime;
