import http, { Server as HTTPServer } from 'http'
import express, { Application as ExpressApp } from 'express'
require('dotenv').config()
const port: string = process.env.PORT || '9009'
import socketIo, { Server as SocketIOServer }  from 'socket.io'

class Socket {

  private readonly MAX_CONNECTIONS = 2

  private io: any

  private activeSockets: string[] = [];

  private rooms: Array<{name: string, users: string[]}>

  private i = 1

  constructor(io: SocketIOServer) {
    this.io = io
    this.rooms = []
    this.tidyUp()
  }

  private tidyUp () {
    setInterval(() => {
      this.rooms.forEach((room, i) => {
        if (room.users.length === 0) {
          console.log('Found an empty room while tidying up:', room)
          this.rooms.splice(i, 1)
        }
      })
    }, 50000)
  }

  private findRoomWithUserIn (socketId: string): string {
    const room = this.rooms.filter(room => room.users.includes(socketId))
    if (room.length)
      return room[0].name || ''
    else
      return ''
  }

  private removeUserFromRoom (socket: any) {
    this.rooms.forEach((room, i) => {
      const userIndex = room.users.indexOf(socket.id)
      if (userIndex !== -1) {
        this.rooms[i].users.splice(userIndex, 1)
      }
    })
    socket.leave(socket.id)
  }

  private joinRoom (socket: any) {
    // if user is already in room then dont join one
    const existingRoom = this.rooms.filter(room => room.users.includes(socket.id))
    if (existingRoom && existingRoom.length) {
      return false
    }

    // Join room
    const freeRoom = this.rooms.filter(room => room.users.length < this.MAX_CONNECTIONS)
    if (freeRoom.length) {
      console.log('There is an existing room to join')
      socket.join(freeRoom[0].name)
      freeRoom[0].users.push(socket.id)
    } else {
      console.log('Going to create a new room as none were free')
      // create one and join it
      const newRoom = {
        name: this.generateRoomName(),
        users: [socket.id]
      }
      this.rooms.push(newRoom)
      socket.join(newRoom.name)
    }
  }

  private generateRoomName (): string {
    return Math.random().toString(36).substring(7) + Math.random().toString(36).substring(7);
  }

  public handle () {
    this.io.on('connection', (socket: any) => {

      this.joinRoom(socket)

      console.log('start of loop')
      Object.keys(this.io.sockets.adapter.rooms).forEach(prop => {
        console.log(prop)
        console.log(this.io.sockets.adapter.rooms[prop])
        console.log(this.io.sockets.adapter.rooms[prop].sockets[socket.id])
      })
      console.log('end of loop')

      // Display users
      const usersRoom = this.rooms.filter(room => room.users.includes(socket.id))[0]
      console.log('the users room:', usersRoom)
      if (usersRoom) {
        // send to other clients someone has joined
        socket.broadcast.to(usersRoom.name).emit('user-joined', {
          users: [socket.id]
        })
        // send to connected client the other user if there is one
        const otherUsersId = usersRoom.users.filter(id => id !== socket.id)[0]
        if (otherUsersId) {
          console.log('sending the id of ' + otherUsersId + ' to ' + socket.id)
          console.log('here are our own rooms', this.rooms)
          console.log('here are the rooms in socket:', this.io.sockets.adapter.rooms)
          setTimeout(() => {
            socket.to(usersRoom.name).emit('user-joined', {
              users: [otherUsersId]
            })
          }, 4000)
        }
      }
      // Then if the user has joined and a user is already in their room


      // const existingSocket = this.activeSockets.find(
      //     existingSocket => existingSocket === socket.id
      // );
      // if (!existingSocket) {
      //   this.activeSockets.push(socket.id);
      //   socket.emit('user-joined', {
      //     users: this.activeSockets.filter(
      //         existingSocket => existingSocket !== socket.id
      //     )
      //   });
      //   socket.broadcast.emit('user-joined', {
      //     users: [socket.id]
      //   });
      // }

      // Get id
      socket.on('get-id', () => {
        socket.emit('get-id', socket.id)
      })

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

      // disconnect
      socket.on('disconnect', (data: any) => {
        this.removeUserFromRoom(socket)
      })

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