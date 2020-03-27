

// const server = new Drash.Http.Server({
//     address: `${config().HOST}:${config().PORT}`,
//     // Logger for `this.server.logger.*()`
//     logger: new Drash.CoreLoggers.ConsoleLogger({
//         enabled: true,
//         level: "all",
//         tag_string: "{datetime} | {level} |",
//         tag_string_fns: {
//             datetime() {
//                 return new Date().toISOString().replace("T", " ");
//             }
//         }
//     }),
//     directory: ".",
//     resources: [CommentsReosurce]
// });
// server.run();

import SocketServer from "./server.ts";
const io = new SocketServer();
io.on('connection', () => {
    console.log('A user connected.');
});
io.on('chatroom1', function (incomingMessage: any) {
    io.to('chatroom1', incomingMessage);
});
io.on('disconnect', () => {
    console.log('A user disconnected.');
});
