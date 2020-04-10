"use strict";
var __importDefault = (this && this.__importDefault) || function (mod) {
    return (mod && mod.__esModule) ? mod : { "default": mod };
};
Object.defineProperty(exports, "__esModule", { value: true });
console.log('hi');
var express = require('express');
var http = require('http');
var app = express();
var socket_1 = __importDefault(require(".././socket"));
require('dotenv').config();
var port = process.env.NODE_PORT || 9009;
var socket_io_1 = __importDefault(require("socket.io"));
app.set('port', port);
var server = http.createServer(app);
var io = socket_io_1.default(server);
server.listen(port);
server.on('error', onError);
io.attach(server);
var socket = new socket_1.default(io);
socket.handle();
function onError(error) {
    console.log('on error');
    if (error.syscall !== 'listen') {
        throw error;
    }
    var bind = typeof this.port === 'string'
        ? 'Pipe ' + this.port
        : 'Port ' + this.port;
    switch (error.code) {
        case 'EACCES':
            console.error(bind + ' requires elevated privileges');
            process.exit(1);
            break;
        case 'EADDRINUSE':
            console.error(bind + ' already in use');
            process.exit(1);
            break;
        default:
            throw error;
    }
}
server.on('listening', function () {
    var addr = server.address();
    var bind = typeof addr === 'string'
        ? 'pipe ' + addr
        : 'port ' + addr.port;
    console.log('Listening on ' + bind);
});
module.exports = server;
