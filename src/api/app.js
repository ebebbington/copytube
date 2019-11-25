//
// General Packages
//
const cookieParser = require('cookie-parser')
const bodyParser = require('body-parser')
require('dotenv').config()

//
// Server Setup
//
const express = require('express')
const app = express()
const port = parseInt(process.env.PORT) || null
app.listen(port);
console.log('CopyTube RESTful API is ready and listening on: ' + port);

//
// Database Setup
//
const mongoose = require('mongoose')
const dbUrl = process.env.DB_URL || ''
const env = process.env.ENV || null
mongoose.connect(dbUrl, {useNewUrlParser: true, useUnifiedTopology: true})
  .then(() => {
    if (env === 'development') {
      //logger.info(`Connected to ${dbUrl}`)
      console.log(`Connected to ${dbUrl}`)
    }
  })
  .catch(err => {
    //logger.error(`Error connecting to database: ${err.message}`)
    console.error(`Error connecting to database: ${err.message}`)
  })

//
// Configurations
//
app.use(cookieParser())
app.use(bodyParser.urlencoded({ extended: false}))
app.use(bodyParser.json())