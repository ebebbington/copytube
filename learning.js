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

  // Access to the API JSON Server - using a Try/Catch block
  function apiRequest () {
    // todo :: first check i can connect to the api to stop an error i cant catch
    try {
      console.log('API Request [Part 1/2] - Start Try block')
      $.ajax({
        type: 'GET', // also use POST, PUT, DELETE
        url: 'http://localhost:3000/posts',
        success: function (response) { // Instead of this block use "data: { id: 1, title: 'title' }" for POST
          console.log('API Request [Part 2/2] - Request completed: ' + response)
        }
      })
    } catch (e) {
      console.log('%cAPI Request [Part 2/2] - Caught an error: ' + e, 'color: red')
    }
  }

  // Run all functions above
  function runAll () {
    console.log('Supporting Functions [Part 2/3] - Run All Functions')
    fibonaccisSequence()
    apiRequest()
    console.log('Supporting Functions [Part 3/3] - Finished')
  }

  return {
    runAll: runAll
  }
}
supportingFunctions().runAll()
