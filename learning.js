/* Created by Edward Bebbington
* On 04/02/2019
 * on: 04/02/2019
 * for: keeping code that is purely for my leaning here seperate from the real code base */

// All code here has no impact on copytube and is merely for outputting data in the console

// After researching what a RMP is i realised Adam was using the 'Revealing Module Pattern', credit goes to you Adam as i used your RMP template :) which shall be below:
const supportingFunctions = function () {
  console.log('Supporting Functions [Part 1/3] - Start')

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

  function runAll () {
    console.log('Supporting Functions [Part 2/3] - Run All Functions')
    fibonaccisSequence()
    console.log('Supporting Functions [Part 3/3] - Finished')
  }

  return {
    runAll: runAll
  }
}
supportingFunctions().runAll()
