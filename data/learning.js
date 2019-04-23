/* globals $ */

let outputArray = []

/* Created by Edward Bebbington
* On 04/02/2019
 * on: 04/02/2019
 * for: keeping code that is purely for my leaning here seperate from the real code base */

// All code here has no impact on copytube and is merely for outputting data in the console

// After researching what a RMP is i realised Adam was using the 'Revealing Module Pattern', credit goes to you Adam as i used your RMP template :) which shall be below:
const revealingModulePattern = function () {
  outputArray.push('Supporting Functions [Part 1/3] - Start')
  // Calculate Fibonacci's Sequence
  function fibonaccisSequence () {
    outputArray.push("Fibonacci's Sequence [Part 1/3] - Start")
    // create variables
    let [ fibArray, maxLength ] = [ [ 0, 1 ], 20 ]
    outputArray.push("Fibonacci's Sequence [Part 2/3] - Calculating - Baseline: [" + fibArray + '] - Max Length: ' + maxLength)
    // calculate the sequence based on max length
    while (fibArray.length !== maxLength) {
      let lastTwoValues = fibArray.slice(-2) // this extracts the last 2 values of the array, reference: https://stackoverflow.com/questions/43430006/get-last-2-elements-of-an-array-in-a-selector-redux
      let [ n1, n2 ] = [ lastTwoValues[ 0 ], lastTwoValues[ 1 ] ]
      let n = n1 + n2
      fibArray.push(n)
    }
    outputArray.push("Fibonacci's Sequence [Part 3/3] - Result: " + fibArray)
  }
  // How to do a promise
  // Set up promise to get videos for later use - METHOD 2: I could just handle the data within a fucntion that uses an
  // ajax call e.g. pass in arguments (ar1, arg2) and handle these if (arg1 == 1) {}, theres so many different ways using example below:
  // const test = function (arg1, arg2) { $.ajax({ ajax call; success { if (arg1 === 1) { } if (arg2 === 2) {} }})}
  function myPromise () {
    outputArray.push('Promises [Part 1/3] - Start')
    const promise = new Promise(function (resolve, reject) {
      // Simulate an AJAX
      const isResolved = 'Resolved'
      setTimeout(resolve(isResolved), 3000)
      outputArray.push('Promises [Part 2/3] - Resolved')
    })
    promise
      .then(function (isResolved) {
        outputArray.push('Promises [Part 3/3] - Accessing data: ' + isResolved)
      })
      .catch(function (reject) {
        outputArray.push('%cPromises [Part 3/3] - Rejected: ' + reject, 'color: red')
      })
  }
  // Cookie function
  function cookies (arg1) {
    outputArray.push('Cookies [Part 1/2] - Start')
    const cookie = document.cookie = 'name=' + arg1
    outputArray.push(cookie)
    outputArray.push('Cookies [Part 2/2] - Finished')
  }
  // RNG
  function rng () {
    // find a specific number without finding duplicates
    outputArray.push('RNG [ Part 1/2] - Start')
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
    outputArray.push('RNG [Part 2/2] - Finished - Result: ' + random)
  }
  // Get current time
  function getTime () {
    outputArray.push('Get Current Time [Part 1/3] - Start')
    let time = new Date()
    let [ h, m ] = [ time.getHours(), time.getMinutes() ]
    if (m < '10') { // slight problem, any minute below 10 was displayed as 15:1 or 15:8, this fixes it by adding a zero before the minute
      m = '0' + m
    }
    time = h + ':' + m
    outputArray.push('Get Current Time [Part 2/3] - Result: ' + time)
    outputArray.push('Get Current Time [Part 3/3] - Finished')
  }
  // Run all functions above
  //
  // Classes
  //
  function myClasses () {
    outputArray.push('Classes [Part 1/2] - Run classes')
    class Vehicle {
      constructor (size) {
        // Children use all properties from parent constructor i.e this mist hold all the methods needed
        this.speed = 0
        this.size = size
      }
      moveForward (vehicle, speed) {
        this.speed = speed
        this.vehicle = vehicle
        outputArray.push('Classes Part[2/2] - ' + vehicle + ' class moves forward with ' + speed + ' speed')
      }
      stop (vehicle) {
        this.speed = 0
        outputArray.push(vehicle + ' stopped')
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
  //
  // Run all functions
  //
  function runAll () {
    outputArray.push('Supporting Functions [Part 2/3] - Run All Functions')
    fibonaccisSequence()
    myPromise()
    cookies('test name')
    rng()
    getTime()
    myClasses()
    assert('hello', 'string', null, 'hello')
    outputArray.push('Supporting Functions [Part 3/3] - Finished')
  }
  // Self Executing Anonymous Function
  (function () {
    outputArray.push('SEAF [Part 1/1] - Running')
  })()
  return {
    runAll: runAll
  }
}
revealingModulePattern().runAll()
const x = {
  'output': outputArray
}
console.log(x)
