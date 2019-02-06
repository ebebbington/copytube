/* globals $ */

/* Created by Edward Bebbington
* On 04/02/2019
 * on: 04/02/2019
 * for: keeping code that is purely for my leaning here seperate from the real code base */

// All code here has no impact on copytube and is merely for outputting data in the console

// After researching what a RMP is i realised Adam was using the 'Revealing Module Pattern', credit goes to you Adam as i used your RMP template :) which shall be below:
const supportingFunctions = function () {
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

  // Run all functions above
  function runAll () {
    console.log('Supporting Functions [Part 2/3] - Run All Functions')
    fibonaccisSequence()
    apiRequest()
    myPromise()
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
supportingFunctions().runAll()
