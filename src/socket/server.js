



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

let users = []

io.on('connection', function (socket) {
  console.log('io connection has been made to me')

  socket.on('disconnect', function (data) {
    console.log('user disconencted from me')
  })

  socket.on('user left', function (data) {
    console.log('user left')
    users.forEach((user, index) => {
      if (user.id === data.id) {
        users.splice(index, 1)
      }
    })
    console.log('removed user from array, here is what it is now:')
    console.log(users)
  })

  /**
   * A user emits that they have joined the pool
   */
  socket.on('user joined', function (data) {
    console.log('user has joined with the following data:')
    console.log(data)
    var data = {
      id: data.id, room: 'some room', username: data.username
    }
    users.push(data)
    console.log('Added a user to the list, heres the total users:')
    console.log(users)
    console.log('going to broadcast to user joined with the following:')
    console.log(data)
    // send data of connected user to other users
    socket.broadcast.emit('user joined', data)
    // send data of other connected user to user that sent event
    let otherUser = {}
    users.forEach((user, index) => {
      if (user.id !== data.id) {
        // means this obj is the other user
        otherUser = user
      }
    })
    socket.emit('user joined', otherUser)
  })
})

// todo :: add rooms