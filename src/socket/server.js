"use strict";
require('dotenv').config()
const PORT = process.env.PORT
var webSocketServer = require('websocket').server;
var http = require('http');
// latest 100 messages
var history = [ ];
// list of currently connected clients (users)
var clients = [ ];


/**
 * HTTP server
 */
var server = http.createServer( function (request, response) { });
server.listen(PORT, function() {
  console.log((new Date()) + " Server is listening on port "
    + PORT);
});

/**
 * WebSocket server
 */
var wsServer = new webSocketServer({
  httpServer: server
});

/**
 * On someone connecting
 */
wsServer.on('request', function(request) {
  console.log((new Date()) + ' Connection from origin '
    + request.origin + '.');
  // accept connection - you should check 'request.origin' to
  // make sure that client is connecting from your website
  // (http://en.wikipedia.org/wiki/Same_origin_policy)
  var connection = request.accept(null, request.origin);
  // we need to know client index to remove them on 'close' event
  var index = clients.push(connection) - 1;
  var userName = false;
  var userColor = false;
  console.log((new Date()) + ' Connection accepted.');
  // send back chat history
  if (history.length > 0) {
    connection.sendUTF(
      JSON.stringify({ type: 'history', data: history} ));
  }
  // user sent some message
  connection.on('message', function(message) {
    console.log('got a message!: ' + message)
  });
  // user disconnected
  connection.on('close', function(connection) {
    console.log('user disconnected: ' + connection)
  });
});