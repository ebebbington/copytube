import Socket = NodeJS.Socket;
import {SocketConnectOpts} from "net";
import {SocketOptions} from "dgram";

const express = require('express')
const http = require('http')
const app = express()
require('dotenv').config()
const port: string = process.env.PORT || '9009'
app.set('port', port)
const server = http.createServer(app);
server.listen(port);
const socketIo = require('socket.io')
const io = socketIo(server)
io.attach(server)

interface User {
  username: string,
  id: string,
  room: string
}
//@ts-ignore
let users: [User] = []
const MAX_USERS = 2

function handleConnection (socket: any): void {
  console.log('[handleConnection] - Connection has been made')
  // @ts-ignore
  if (users.length === MAX_USERS) {
    console.log('[handleConnection] - Maximum users reached. Cutting off this new connection')
    socket.disconnect()
  }
}

function handleDisconnect (): void {
  console.log('[handleDisconnection] - User disconnected from me')
}

function handleUserLeft (id: string): void {
  console.log('[handleUserLeft] - User left with id of ' + id)
  users.forEach((user, index) => {
    if (user.id === id) {
      users.splice(index, 1)
    }
  })
}

/**
 * Sends the new user data to the other connection,
 * and sends the other users data to the newly connected user
 * @param socket
 * @param newUserData
 */
function emitUserJoined (socket: any, newUserData: User) {
  console.log('[emitUserJoined]')
  socket.broadcast.emit('user joined', newUserData)
  let otherUser = {}
  users.forEach((user, index) => {
    if (user.id !== newUserData.id) {
      // means this obj is the other user
      otherUser = user
    }
  })
  socket.emit('user joined', otherUser)
}

function handleUserJoined (socket: any, data: { id: string, username: string }) {
  console.log('[handleUserJoined] - User joined')
  const newUserData = {
    id: data.id,
    room: 'some room',
    username: data.username
  }
  users.push(newUserData)
  emitUserJoined(socket, newUserData)
}

io.on('connection', function (socket: any) {
  handleConnection(socket)

  socket.on('disconnect', function (data: any) {
    handleDisconnect()
  })

  /**
   * removes the user from the pool
   */
  socket.on('user left', function (data: {id: string}) {
    handleUserLeft(data.id)
  })

  /**
   * A user emits that they have joined the pool
   */
  socket.on('user joined', function (data: { id: string, username: string}) {
    handleUserJoined(socket, data)
  })

})

// todo :: add rooms