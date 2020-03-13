import Socket = NodeJS.Socket;
import {SocketConnectOpts} from "net";
import {SocketOptions} from "dgram";

const express = require('express')
import http, { Server as HTTPServer } from 'http'
import app, { Application as ExpressApp } from 'express'
require('dotenv').config()
const port: string = process.env.PORT || '9009'
app.set('port', port)
const server = http.createServer(app);
server.listen(port);
import socketIo, { Server as SocketIOServer }  from 'socket.io'
const io = socketIo(server)
io.attach(server)

let connections: string[] = []
const MAX_CONNECTIONS = 2

/**
 * const { RTCPeerConnection, RTCPEerDescription } = window
 * async function callUser (socketId) {
 *     const offer = await peerConnection.createOffer()
 *     await peerConnection.setLocalDescription(new RTCPeerDescription(offer))
 *     socket.emit('call user', {offer, socketId})
 * }
 * let myId = ''
 * socket.on('joined', id => myId = id)
 * socket.on('user joined', callUser)
 * socket.on('call made', ({offer, theirid }) {
 *     await peerConnection.setRemoteDescription(new RTCSessionDescription(offer))
 *     const answer = await peerConnection.createAnswer()
 *     await peerConnection.setLocalDescription(new RTCSessiondESCRIPTION(answer))
 *     socket.emit('answer call', { answer, id: theirId})
 * }
 * socket.on('answer made', ({answer, socketid}) => {
 *     await peerConnection.setRemoveDescription(new RTCSessionDescription(data.answer))
 *     if (!isAlreadyCalling) {
 *         callUser(data.socketId)
 *         isAlreadycallig = true
 *     }
 * }
 *
 * ... .getusermedia, (stream => stream.getTracks().foreach(track => peerconn.addtrack(track, stream
 *
 * peerconnection.ontrack = function ({streams: [stream]} {
 *     // display remnote video here
 * }
 */
class Socket {

  public static handle (io: SocketIOServer) {
    io.on('connection', socket => {
      Socket.handleConnection(socket)

      Socket.emitJoined(socket)
      Socket.broadcastEmitUserJoined(socket)

      socket.on('disconnect', () => {
        Socket.handleDisconnect(socket)
      })

      socket.on('call user', (data) => {
        Socket.handleCallUser(socket, data)
      })

      socket.on('answer call', (data: {answer: any, socketId: string}) => {
        Socket.handleAnswerCall(socket, data)
      })

    })
  }

  //@ts-ignore
  public static emitAnswerMade(socket, data: { answer: any, socketId: string}) {
    socket.to(data.socketId).emit('answer made', { socketId: socket.id, answer: data.answer})
  }

  //@ts-ignore
  public static handleAnswerCall(socket, data: { answer: any, socketId: string}) {
    Socket.emitAnswerMade(socket, data)
  }

  //@ts-ignore
  public static emitCallMade (socket, data: { offer: any, socketId: string}) {
    socket.to(data.socketId).emit('call made', {
      offer: data.offer,
      theirSocketId: data.socketId
    })
  }

  //@ts-ignore
  public static handleCallUser (socket, data: { offer: any, socketId: string}) {
    Socket.emitCallMade(socket, data)
  }

  //@ts-ignore
  public static broadcastEmitUserJoined (socket) {
    socket.broadcast.emit('user joined', socket.id)
  }

  //@ts-ignore
  public static emitJoined (socket) {
    socket.emit('joined', socket.id)
  }

  //@ts-ignore
  public static handleDisconnect (socket) {
    console.log('[handleDisconnection] - User disconnected from me')
    connections = connections.filter(id => id !== socket.id)
  }

  //@ts-ignore
  public static handleConnection (socket) {
    console.log('[handleConnection] - Connection has been made')
    if (connections.length === MAX_CONNECTIONS) {
      socket.disconnect()
    } else {
      connections.push(socket.id)
    }
  }

}

class Server {
  private readonly httpServer: HTTPServer
  private readonly  app: ExpressApp
  private readonly io: SocketIOServer
  private readonly port: string = port

  constructor () {
    this.app = express()
    this.httpServer = http.createServer(this.app)
    this.io = socketIo(this.httpServer)
    this.configure()
    this.listen()
    this.handleSocketConnection()
  }

  private configure () {
    this.app.set('port', this.port)
    this.io.attach(this.httpServer)
  }

  private handleSocketConnection () {
    Socket.handle(this.io)
  }

  private listen () {
    this.httpServer.listen(port)
  }
}

const server2 = new Server()

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