import Echo from "laravel-echo";
import Pusher from "pusher-js";

window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: "pusher",
    key: "5af8b56c9ba705ddfb37",
    cluster: "ap1",
    forceTLS: true,
});
