import http, { Server as HTTPServer } from 'http'
import express, { Application as ExpressApp } from 'express'
require('dotenv').config()
const port: string = process.env.PORT || '9009'
import socketIo, { Server as SocketIOServer } from 'socket.io'
import SocketIO from "socket.io"

interface Room {
  name: string,
  users: string[]
}

/**
 * @class Socket
 *
 * @property  {MAX_CONNECTIONS}
 * @property  {io}
 * @property  {rooms}
 *
 * @method    getJoinedRoom           {@link Socket#getJoinedRoom}
 * @method    generateRoomName        {@link Socket#generateRoomName}
 * @method    getOtherUsersIdByRoom   {@link Socket#getOtherUsersIdByRoom}
 * @method    joinRoom                {@link Socket#joinRoom}
 * @method    handle                  {@link Socket#handle}
 */
class Socket {

  /**
   * @var {number} Maximum number of connections for a room
   */
  private readonly MAX_CONNECTIONS = 2

  /**
   * @var {SocketIOServer} The SocketIO object to handle everything
   */
  private io: SocketIOServer

  /**
   * @var {[Room]} List of rooms that are currently in use by users
   */
  private rooms: Array<Room> = []

  /**
   * @param {SocketIOServer} io
   */
  constructor(io: SocketIOServer) {
    this.io = io
  }

  /**
   * @description
   * Get the room the user is in by their socket id
   *
   * @example
   * const joinedRoom = this.getJoinedRoom(socket.id)
   *
   * @param {string} socketId socket.id
   *
   * @return {Room|undefined} The room object or undefined if they haven't joined a room
   */
  private getJoinedRoom (socketId: string): Room|undefined {
    const joinedRoom = this.rooms.filter(room => room.users.includes(socketId))
    if (joinedRoom.length)
      return joinedRoom[0]
    return undefined
  }

  /**
   * @description
   * Our helper function for creating the names for our rooms
   *
   * @example
   * const newRoomName = this.generateRoomName()
   * socket.join(newRoomName)
   *
   * @return {string} A randomised 14 character string
   */
  private generateRoomName (): string {
    return Math.random().toString(36).substring(7) + Math.random().toString(36).substring(7);
  }

  /**
   * @description
   * Get the calling (current) users room by the socket id, and get the other user in that room
   *
   * @example
   * const otherUsersId = this.getOtherUsersIdByRoom(socket)
   *
   * @param {SocketIO.Socket} socket The callback data from the io connection
   *
   * @return {string} The other users id or undefined if couldn't find one
   */
  private getOtherUsersIdByRoom (socket: SocketIO.Socket): string {
    const joinedRoom = this.getJoinedRoom(socket.id)
    if (!joinedRoom)
      return ''
    const userId: string[] = joinedRoom.users.filter(id => id !== socket.id)
    if (userId.length)
      return userId[0]
    else
      return ''
  }

  /**
   * @description
   * Join a room using the calling sockets id. First find a spare room, else create a new one.
   * Updates the rooms property as well
   *
   * @example
   * this.joinRoom(socket)
   *
   * @param {SocketIO.Socket} socket Passed back data from the io.on connection callback
   *
   * @return {void}
   */
  private joinRoom (socket: SocketIO.Socket): void {
    // find a spare room
    const foundSpareRoom = this.rooms.filter((room, i) => {
      if (room.users.length < this.MAX_CONNECTIONS) {
        socket.join(room.name)
        this.rooms[i].users.push(socket.id)
        return true
      }
    })
    if (!foundSpareRoom.length) {
      // create one instead
      const newRoomName = this.generateRoomName()
      socket.join(newRoomName)
      this.rooms.push({name: newRoomName, users: [socket.id]})
    }
  }

  private removeUserFromRoom (socketId: string) {

  }

  /**
   * @description
   * The entry point for handling all events and connections
   *
   * @return {void}
   */
  public handle () {
    this.io.on('connection', (socket: SocketIO.Socket) => {

      this.joinRoom(socket)

      // Display other user in room
      const joinedRoom = this.getJoinedRoom(socket.id)
      if (joinedRoom) {
        const otherUsersId = this.getOtherUsersIdByRoom(socket)
        // send to other clients someone has joined
        socket.to(otherUsersId).emit('user-joined', {
          users: [socket.id]
        })
        // and when a user connects whilst there is already another user in the room, send the other users id
        socket.to(socket.id).emit('user-joined', {
          users: [otherUsersId]
        })
      }

      // Get id
      socket.on('get-id', () => {
        socket.emit('get-id', socket.id)
      })

      // Update rooms
      socket.on("disconnect", () => {
        const otherUsersId = this.getOtherUsersIdByRoom(socket)
        // Remove the user from the room
        this.rooms.forEach((room, i) => {
          if (room.users.includes(socket.id)) {
            this.rooms[i].users = room.users.filter(user => user !== socket.id)
          }
        })
        // Send message this user has left
        socket.to(otherUsersId).emit("remove-user", {
          socketId: socket.id
        });
        // Completely remove the user
        socket.leave(socket.id)
        // Remove the room if empty
        this.rooms.forEach((room, i) => {
          if (room.users.length === 0) {
            this.rooms.splice(i, 1)
          }
        })
      });

      // Make a call request
      socket.on("call-user", (data: { to: string, offer: RTCOfferOptions}) => {
        socket.to(data.to).emit("call-made", {
          offer: data.offer,
          socket: socket.id
        });
      });

      // Answer the call request
      socket.on("make-answer", (data: { to: string, answer: RTCAnswerOptions}) => {
        socket.to(data.to).emit("answer-made", {
          socket: socket.id,
          answer: data.answer
        });
      });

    })
  }
}

/**
 * @class Server
 *
 * @property  {httpServer}
 * @property  {app}
 * @property  {io}
 * @property  {port}
 *
 * @method    constructor             {@link Server#constructor}
 * @method    configure               {@link Socket#getJoinedRoom}
 * @method    handleSocketConnection  {@link Socket#handleSocketConnection}
 * @method    listen                  {@link Socket#listen}
 */
class Server {

  /**
   * @var {HTTPServer} Basix HTTP Server to start our application
   */
  private readonly httpServer: HTTPServer

  /**
   * @var {ExpressApp} Used to be passed into the HTTP server for easier setup
   */
  private readonly  app: ExpressApp

  /**
   * @var {SocketIOServer} The SocketIO connection (io)
   */
  private readonly io: SocketIOServer

  /**
   * @var {string} Port For the HTTP server to listen on
   */
  private readonly port: string = port

  /**
   * Usual setup and configuration
   */
  constructor () {
    this.app = express()
    this.httpServer = http.createServer(this.app)
    this.io = socketIo(this.httpServer)
    this.configure()
    this.listen()
    this.handleSocketConnection()
  }

  /**
   * @method configure
   *
   * @description
   * Configure the server
   */
  private configure () {
    this.app.set('port', this.port)
    this.io.attach(this.httpServer)
  }

  /**
   * @method handleSocketConnection
   *
   * @description
   * Create an instance of the Socket server and pass the SocketIO object to the Socket server to handle everything
   */
  private handleSocketConnection () {
    const socket = new Socket(this.io)
    socket.handle()
  }

  /**
   * @method listen
   *
   * @description
   * Start the server
   */
  private listen () {
    this.httpServer.listen(this.port, () => {
      console.log('Listening on ' + this.port)
    })
  }
}

const server = new Server()