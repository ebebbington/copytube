



const express = require('express')
const http = require('http')
const app = express()
require('dotenv').config()
const port = process.env.PORT || 9009
app.set('port', port)
//create http server
const server = http.createServer(app);
server.listen(port);
// Attach socket io we assigned in app.ts to the server (handling is handled inside the respective route)
const socketIo = require('socket.io')
const io = socketIo(server)
io.attach(server)





io.on('connection', function (socket) {
  console.log('io connection has been made to me')
  socket.on('disconnect', function (data) {
    console.log('user disconencted from me')
  })
  socket.on('user joined', function (data) {
    console.log('user has joined with the following data:')
    console.log(data)
    var data = {
      id: data.id, room: 'some room', username: data.username
    }
    console.log('going to broadcast to user joined with the following:')
    console.log(data)
    socket.broadcast.emit('user joined', data)
  })
})