//const express = require('express')
import http, { Server as HTTPServer } from 'http'
import express, { Application as ExpressApp } from 'express'
const app = express()
require('dotenv').config()
const port: string = process.env.PORT || '9009'
//app.set('port', port)
//const server = http.createServer(app);
//server.listen(port);
import socketIo, { Server as SocketIOServer }  from 'socket.io'
import SocketIO from "socket.io";
//const io = socketIo(server)
//io.attach(server)

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

  private connections: string[] = []

  private readonly MAX_CONNECTIONS = 2

  private io: any

  private activeSockets: string[] = [];

  constructor(io: SocketIOServer) {
    this.io = io
  }

  public handle () {
    this.io.on('connection', (socket: any) => {

      socket.on('get-id', () => {
        socket.emit('get-id', socket.id)
      })

      const existingSocket = this.activeSockets.find(
          existingSocket => existingSocket === socket.id
      );
      if (!existingSocket) {
        this.activeSockets.push(socket.id);
        socket.emit('user-joined', {
          users: this.activeSockets.filter(
              existingSocket => existingSocket !== socket.id
          )
        });
        socket.broadcast.emit('user-joined', {
          users: [socket.id]
        });
      }
      socket.on("disconnect", () => {
        this.activeSockets = this.activeSockets.filter(
            existingSocket => existingSocket !== socket.id
        );
        socket.broadcast.emit("remove-user", {
          socketId: socket.id
        });
      });
      socket.on("call-user", (data: any) => {
        socket.to(data.to).emit("call-made", {
          offer: data.offer,
          socket: socket.id
        });
      });
      socket.on("make-answer", (data: any) => {
        socket.to(data.to).emit("answer-made", {
          socket: socket.id,
          answer: data.answer
        });
      });


      //this.handleConnection(socket)

      socket.on('user joined', () => {
        //this.handleUserJoined(socket)
      })

      socket.on('get id', () => {
        //this.handleGetId(socket)
      })

      socket.on('disconnect', () => {
        //this.handleDisconnect(socket)
      })

      socket.on('call user', (data: { offer: any, to: string}) => {
        //this.handleCallUser(socket, data)
      })

      socket.on('answer call', (data: {answer: any, to: string}) => {
        //this.handleAnswerCall(socket, data)
      })

    })
  }

  //@ts-ignore
  public emitGetId (socket) {
    console.log('[emitGetId] - emitting users socket id to them')
    socket.emit('get id', socket.id)
  }

  //@ts-ignore
  public handleGetId (socket) {
    console.log('[handleGetId] - calling emitGetId method')
    this.emitGetId(socket)
  }

  //@ts-ignore
  public emitAnswerMade(socket, data: { answer: any, to: string}) {
    console.log('[emitAnswerMade] - Data', data.answer, data.to)
    console.log('[emitAnswerMade] - Emitting socket id and answer to calling id')
    socket.to(data.to).emit('answer made', { socketId: socket.id, answer: data.answer})
  }

  //@ts-ignore
  public handleAnswerCall(socket, data: { answer: any, to: string}) {
    console.log('[handleAnswerCall] - Data', data.answer, data.to)
    console.log('[handleAnswerCall] - calling emitAnswerMade method')
    this.emitAnswerMade(socket, data)
  }

  //@ts-ignore
  public emitCallMade (socket, data: { offer: any, to: string}) {
    console.log('[emitCallMade] - Data', data.offer, data.to)
    console.log('[emitCallMade] - Emiting the offer and other users id to calling user')
    console.log('ID OF CALLED USER:' + data.to)
    socket.to(data.to).emit('call made', {
      offer: data.offer,
      id: data.to
    })
  }

  //@ts-ignore
  public handleCallUser (socket, data: { offer: any, to: string}) {
    console.log('[handleCallUser] - Data', data.offer, data.to)
    console.log('[handleCallUser] - Calling the emitCallMade method')
    this.emitCallMade(socket, data)
  }

  //@ts-ignore
  public handleUserJoined (socket) {
    console.log('[handleUserJoined] - Emitting the connections socket id')
    socket.broadcast.emit('user joined', socket.id)
  }

  //@ts-ignore
  public emitJoined (socket) {
    console.log('[emitJoined] - Emitting `joined` with id of ' + socket.id)
    socket.emit('joined', socket.id)
  }

  //@ts-ignore
  public handleDisconnect (socket) {
    console.log('[handleDisconnection] - User disconnected from me')
    this.connections = this.connections.filter(id => id !== socket.id)
  }

  //@ts-ignore
  public handleConnection (socket) {
    console.log('[handleConnection] - Connection has been made')
    if (this.connections.length === this.MAX_CONNECTIONS) {
      console.log('max num of connections reached')
      socket.disconnect()
    } else {
      this.connections.push(socket.id)
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
    const socket = new Socket(this.io)
    socket.handle()
  }

  private listen () {
    this.httpServer.listen(this.port, () => {
      console.log('Listening on ' + this.port)
    })
  }
}

const server = new Server()

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

// io.on('connection', function (socket: any) {
//   handleConnection(socket)
//
//   socket.on('disconnect', function (data: any) {
//     handleDisconnect()
//   })
//
//   /**
//    * removes the user from the pool
//    */
//   socket.on('user left', function (data: {id: string}) {
//     handleUserLeft(data.id)
//   })
//
//   /**
//    * A user emits that they have joined the pool
//    */
//   socket.on('user joined', function (data: { id: string, username: string}) {
//     handleUserJoined(socket, data)
//   })
//
// })

// todo :: add rooms