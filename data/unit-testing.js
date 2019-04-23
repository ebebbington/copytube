'use strict'
// ///////////////////////////////////////////////////////////////////////////////////////////
// Documentation
//
// Call this method like so:
//    assert().[function]([params])
//
// j
function assert () {
  console.log('Assert function started')
  //
  // Error message setup
  //
  const msg = {
    start: 'Assert failed: Expected ',
    end: ' but instead got '
  }
  //
  // Check data, type and length
  //
  function checkTypeAndLength (data, expectedType, expectedLength) {
    console.log('Reached check of type and length')
    if (!data) {
      throw Error(msg.start + 'data to be set' + msg.end + data)
    }
    if (typeof data !== expectedType) {
      throw Error(msg.start + expectedType + msg.end + typeof data)
    }
    if (expectedLength && data.length !== expectedLength) {
      throw Error(msg.start + 'a length of' + expectedLength + msg.end + data.length)
    }
    console.log('Passed check of type and length')
  }
  //
  // Check a string
  //
  function string (data, expectedType, expectedResult, expectedRegEx) {
    console.log('Reached check of string')
    checkTypeAndLength(data, expectedType)
    if (data !== expectedResult) {
      throw Error(msg.start + expectedResult + msg.end + data)
    }
    if (expectedRegEx && !data.find(expectedRegEx)) {
      throw Error(msg.start + expectedRegEx + msg.end + data)
    }
    console.log('Test Passed', {
      Results: {
        data: data,
        expectedType: expectedType,
        expectedResult: expectedResult,
        expectedRegEx: expectedRegEx
      }
    })
  }
  //
  // Check an object
  //
  function object (data, expectedType, expectedLength, expectedProps) {
    console.log('Reached check of object')
    checkTypeAndLength(data, expectedType)
    const objectLength = Object.keys(data).length
    if (objectLength !== expectedLength) {
      throw Error(msg.start + expectedLength + msg.end + objectLength)
    }
    //
    // Check property names and values
    //
    for (let i = 0; i < expectedProps.length; i++) {
      // Properties
      if (!data.hasOwnProperty(expectedProps[i])) {
        throw Error(msg.start + expectedProps[i] + msg.end + data)
      }
    }
    console.log('Test Passed', {
      Results: {
        data: data,
        expectedType: expectedType,
        expectedProps: expectedProps
      }
    })
  }
  //
  // Check an array
  //
  function array (data, expectedType, expectedLength, expectedItems) {
    console.log('Reached check of array')
    checkTypeAndLength(data, expectedType, expectedLength)
    if (!Array.isArray(data)) {
      throw Error(msg.start + 'array' + msg.end + typeof data)
    }
    for (let i = 0; i < data.length; i++) {
      if (data[i] !== expectedItems[i]) {
        throw Error(msg.start + expectedItems[i] + msg.end + data[i])
      }
    }
    console.log('Test Passed', {
      Results: {
        data: data,
        expectedType: expectedType,
        expectedLength: expectedLength,
        expectedItems: expectedItems
      }
    })
  }
  //
  // Check an object array
  //
  function objectArray (data, expectedType, expectedLength, expectedProps) {
    console.log('Reached check of object array')
    checkTypeAndLength(data, expectedType, expectedLength)
    if (!Array.isArray(data)) {
      throw Error(msg.start + 'array' + msg.end + typeof data)
    }
    for (let i = 0; i < expectedLength; i++) {
      if (!data[i].hasOwnProperty(expectedProps[i])) {
        throw Error(msg.start + expectedProps[i] + msg.end + data[i])
      }
    }
    console.log('Test Passed', {
      Results: {
        data: data,
        expectedType: expectedType,
        expectedLength: expectedLength,
        expectedProps: expectedProps
      }
    })
  }
  //
  // Return functions
  //
  return {
    string: string,
    object: object,
    array: array,
    objectArray: objectArray
  }
}
