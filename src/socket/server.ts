import http, { Server as HTTPServer } from 'http'
import express, { Application as ExpressApp } from 'express'
require('dotenv').config()
const port: string = process.env.PORT || '9009'
import socketIo, { Server as SocketIOServer }  from 'socket.io'

class Socket {

  private readonly MAX_CONNECTIONS = 2

  private io: any

  private activeSockets: string[] = [];

  constructor(io: SocketIOServer) {
    this.io = io
  }

  public handle () {
    this.io.on('connection', (socket: any) => {

      if (this.activeSockets.length === this.MAX_CONNECTIONS) {
        socket.disconnect()
      }

      // Get id
      socket.on('get-id', () => {
        socket.emit('get-id', socket.id)
      })

      // Display users
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

      // Send message that the user left
      socket.on("disconnect", () => {
        this.activeSockets = this.activeSockets.filter(
            existingSocket => existingSocket !== socket.id
        );
        socket.broadcast.emit("remove-user", {
          socketId: socket.id
        });
      });

      // Make a call request
      socket.on("call-user", (data: any) => {
        socket.to(data.to).emit("call-made", {
          offer: data.offer,
          socket: socket.id
        });
      });

      // Answer the call request
      socket.on("make-answer", (data: any) => {
        socket.to(data.to).emit("answer-made", {
          socket: socket.id,
          answer: data.answer
        });
      });

    })
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