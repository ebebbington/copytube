/* globals $ */

/* Created by Edward Bebbington
* On 04/02/2019
 * on: 04/02/2019
 * for: keeping code that is purely for my leaning here seperate from the real code base */

// All code here has no impact on copytube and is merely for outputting data in the console

// After researching what a RMP is i realised Adam was using the 'Revealing Module Pattern', credit goes to you Adam as i used your RMP template :) which shall be below:
const revealingModulePattern = function () {
  console.log('Supporting Functions [Part 1/3] - Start')
  // Calculate Fibonacci's Sequence
  function fibonaccisSequence () {
    console.log("Fibonacci's Sequence [Part 1/3] - Start")
    // create variables
    let [ fibArray, maxLength ] = [ [ 0, 1 ], 20 ]
    console.log("Fibonacci's Sequence [Part 2/3] - Calculating - Baseline: [" + fibArray + '] - Max Length: ' + maxLength)
    // calculate the sequence based on max length
    while (fibArray.length !== maxLength) {
      let lastTwoValues = fibArray.slice(-2) // this extracts the last 2 values of the array, reference: https://stackoverflow.com/questions/43430006/get-last-2-elements-of-an-array-in-a-selector-redux
      let [ n1, n2 ] = [ lastTwoValues[ 0 ], lastTwoValues[ 1 ] ]
      let n = n1 + n2
      fibArray.push(n)
    }
    console.log("Fibonacci's Sequence [Part 3/3] - Result: " + fibArray)
  }
  // Test access to the API JSON Server - using a Try/Catch block and AJAX call
  function apiRequest () {
    try {
      console.log('API Request [Part 1/2] - Start Try block')
      const apiUrl = 'http://localhost:3000'
      $.ajax({
        // types: GET, POST, PUT, DELETE
        url: apiUrl,
        success: function () { // Instead of this block use "data: { id: 1, title: 'title' }" for POST
          console.log('API Request [Part 2/2] - AJAX Request completed')
        },
        error: function () {
          console.log('%cAPI Request [Part 2/2] - AJAX Error at ' + apiUrl + ', API is most likely not running', 'color: red')
        }
      })
    } catch (e) {
      console.log('%cAPI Request [Part 2/2] - Caught an error: ' + e, 'color: red')
    }
  }
  // How to do a promise
  // Set up promise to get videos for later use - METHOD 2: I could just handle the data within a fucntion that uses an
  // ajax call e.g. pass in arguments (ar1, arg2) and handle these if (arg1 == 1) {}, theres so many different ways using example below:
  // const test = function (arg1, arg2) { $.ajax({ ajax call; success { if (arg1 === 1) { } if (arg2 === 2) {} }})}
  function myPromise () {
    console.log('Promises [Part 1/3] - Start')
    const promise = new Promise(function (resolve, reject) {
      // Simulate an AJAX
      const isResolved = 'Resolved'
      setTimeout(resolve(isResolved), 3000)
      console.log('Promises [Part 2/3] - Resolved')
    })
    promise
      .then(function (isResolved) {
        console.log('Promises [Part 3/3] - Accessing data: ' + isResolved)
      })
      .catch(function (reject) {
        console.log('%cPromises [Part 3/3] - Rejected: ' + reject, 'color: red')
      })
  }
  // Cookie function
  function cookies (arg1) {
    console.log('Cookies [Part 1/2] - Start')
    const cookie = document.cookie = 'name=' + arg1
    console.log(cookie)
    console.log('Cookies [Part 2/2] - Finished')
  }
  // RNG
  function rng () {
    // find a specific number without finding duplicates
    console.log('RNG [ Part 1/2] - Start')
    const [ chosenNumber, maxLength, minLength, rngArray ] = [ 101, 200, 1, [] ]
    let random = 0
    while (random !== chosenNumber) {
      // create rng and do checks
      random = Math.random() * (maxLength - minLength) + minLength
      // remove decimal places
      random = Math.round(random)
      if (rngArray.includes(random)) {
        random = Math.random() * (maxLength - minLength) + minLength
      } else {
        rngArray.push(random)
      }
    }
    console.log('RNG [Part 2/2] - Finished - Result: ' + random)
  }
  // Get current time
  function getTime () {
    console.log('Get Current Time [Part 1/3] - Start')
    let time = new Date()
    let [ h, m ] = [ time.getHours(), time.getMinutes() ]
    if (m < '10') { // slight problem, any minute below 10 was displayed as 15:1 or 15:8, this fixes it by adding a zero before the minute
      m = '0' + m
    }
    time = h + ':' + m
    console.log('Get Current Time [Part 2/3] - Result: ' + time)
    console.log('Get Current Time [Part 3/3] - Finished')
  }
  // Run all functions above
  //
  // Classes
  //
  function myClasses () {
    console.log('Classes [Part 1/2] - Run classes')
    class Vehicle {
      constructor (size) {
        // Children use all properties from parent constructor i.e this mist hold all the methods needed
        this.speed = 0
        this.size = size
      }
      moveForward (vehicle, speed) {
        this.speed = speed
        this.vehicle = vehicle
        console.log('Classes Part[2/2] - ' + vehicle + ' class moves forward with ' + speed + ' speed')
      }
      stop (vehicle) {
        this.speed = 0
        console.log(vehicle + ' stopped')
      }
    }
    class Motorbike extends Vehicle {
      moveForward (speed) {
        super.moveForward('Motorbike', speed)
      }
    }
    class Car extends Vehicle {
      moveForward (speed) {
        super.moveForward('Car', speed)
      }
    }
    let motorbike = new Motorbike()
    let car = new Car()
    motorbike.moveForward(10)
    motorbike.stop('motorbike')
    car.moveForward(5)
    car.stop('car')
  }
  function runAll () {
    console.log('Supporting Functions [Part 2/3] - Run All Functions')
    fibonaccisSequence()
    apiRequest()
    myPromise()
    cookies('test name')
    rng()
    getTime()
    myClasses()
    console.log('Supporting Functions [Part 3/3] - Finished')
  }
  // Self Executing Anonymous Function
  (function () {
    console.log('SEAF [Part 1/1] - Running')
  })()
  return {
    runAll: runAll
  }
}
revealingModulePattern().runAll()
