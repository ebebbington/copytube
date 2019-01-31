// Globals
/* global alert, prompt, $ */
'use strict'
let username = ''

// Adams help in showing how to make a function be snazzy
/* let videos = (function () {
  let Videos = []
  function initialise (Videos) {
    doesSomething(Videos)
  }
  function doesSomething (Videos) {
    // AJAX call (emulating it below)
    Videos = ['test', 'test']
    return Videos
  }
  return {
    initme: initialise(),
    getVideos: Videos
  }
})()
const vids = videos.initme()
console.log(vids)
videos.getVideos
console.log(videoCall) */
// After researching i realised he was using the 'Revealing Module Pattern', which shall be below:
let revealingModulePattern = (function () {
  let firstName = 'test'
  let lastName = 'testing'

  function firstNameFunction () {
    console.log('revealingModulePattern.firstNameFunction.first name: ' + firstName)
  }

  function lastNameFunction () {
    console.log('revealingModulePattern.lastNameFunction.last name: ' + lastName)
  }

  function viewFullNameFunction () {
    firstNameFunction()
    lastNameFunction()
  }

  return {
    first: firstNameFunction,
    last: lastNameFunction,
    view: viewFullNameFunction
  }
})()
revealingModulePattern.view()

// Fibonacci's Sequence - uses SLICE
function fibonaccisSequence () {
  // create variables
  let fibArray = [0, 1]
  let maxLength = 20
  console.log('Calculating Fibonaccis Sequence using baseline of [' + fibArray + '] with a max length of ' + maxLength)
  // calculate the sequence based on max length
  while (fibArray.length !== maxLength) {
    let lastTwoValues = fibArray.slice(-2) // this extracts the last 2 values of the array, reference: https://stackoverflow.com/questions/43430006/get-last-2-elements-of-an-array-in-a-selector-redux
    let n1 = lastTwoValues[0]
    let n2 = lastTwoValues[1]
    let n = n1 + n2
    fibArray.push(n)
  }
  console.log('Result: ' + fibArray)
}
fibonaccisSequence()

// Set up promise to get videos for later use
const getVideos = new Promise(function (resolve, reject) {
  $.ajax({
    type: 'GET',
    url: 'models/get_videos.php',
    success: function (response) {
      let videos = JSON.parse(response)
      resolve(videos) // when resolved it has an object, so this way i am just assigning the vids object to the resolve
    },
    error: function (err) {
      console.log('%cAJAX GET videos Request Failed: ' + err, 'color: red')
      reject(err)
    }
  })
})

$(document).ready(function () {
  // Ensure username is correct - SELF EXECUTING FUNCTION
  (function () {
    let [ complete, username ] = [ false, '' ]
    const errorMsg = 'Please enter an appropriate username between 0 and 81 characters long'
    while (complete !== true) {
      username = encodeURIComponent(prompt('Enter Temporary Username'))
      username.length > 80 || username === 'null' || username.trim().length === 0 ? alert(errorMsg) : complete = true
    }
    const welcomeMessage = 'Hello ' + username + ', and welcome to CopyTube'
    $('#welcome-message').text(welcomeMessage)
  })()

  // Comment character count
  $(document).on('keyup', '#comment-bar', function () {
    const commentLength = $('#comment-bar').val().length
    $('#comment-count').text(commentLength)
  })

  // todo :: Removing Drop-down for Search in Prep for Auto-complete
  /* var drop_down = true;
  $(document).on('keyup', '#search-bar',function(){
      disable drop-down elements
      drop_down = false;
      if (drop_down == false){
          $('.dropdown-content').prop('textContent', "");
      }
  }) */

  // On Click of Add Comment Button
  $('#comment-button').on('click', function () {
    // Ensure comment is correct
    let comment = encodeURI($('#comment-bar').val())
    const maxLength = 400
    const count = comment.split('%20')
    let i = 0
    while (i !== count.length) {
      i++
      comment = comment.replace('%20', '')
    }
    if (comment.length > maxLength || comment.trim().length === 0) {
      alert('Please input a comment and have it be less than 401 characters long')
      $('#comment-bar').val('')
      $('#comment-count').text('0')
    } else {
      // Create comment date
      let today = new Date()
      const [ dd, mm, yyyy ] = [ today.getDate(), (today.getMonth() + 1), today.getFullYear() ] // Month was 1 behind
      today = yyyy + '-' + mm + '-' + dd
      // Concatenate full comment
      const actualComment = '<br>' + '<br>' + 'Username: ' + username + '<br>' + 'Date: ' + today + '<br>' + 'Comment: ' + comment + '<br>' + '<br>'
      $('#user-comments').prepend(actualComment)
      $('#comment-bar').val('')
      $('#comment-count').text('0')
      // Save comment to database
      const mainVidTitle = $('#main-video-title').text()
      $.ajax({
        type: 'POST',
        url: 'models/save_comment.php',
        data: {
          author: username,
          comment: comment,
          datePosted: today,
          videoTitle: mainVidTitle
        },
        success: function () {
          console.log('%cAJAX POST Comment Request Completed', 'color: green')
        },
        error: function (err) {
          console.log('%cAJAX POST Comment Request Failed: ' + err, 'color: red')
        }
      }) // I can use "var_dump($_[typename])" to get props in network response which i an then do "var_dump($_POST[author])" to get value of this property
    }
  })
  // On click of the drodown content
  $(document).on('click', '.dropdown-titles', function () {
    let clickedTitle = $(this).text()
    $('#search-bar').val(clickedTitle)
    $('.rabbit-hole-vids').click()
  })

  // On Click of A Rabbit Hole Video
  $(document).on('click', '.rabbit-hole-vids', function () {
    // Get Videos from promise
    let clickedVidTitle = $(this).prop('title')
    getVideos
    // Data: title, src, description, height, width, poster
      .then(function (videos) {
        console.log('Resolved proceeding: ' + videos)
        // Locate main vid and store excess videos
        let [ rabbitHoleVids, found ] = [ [], null ]
        for (let i = 0, l = videos.length; i < l; i++) {
          if (clickedVidTitle === videos[ i ].title) {
            found = videos[ i ]
            $('#main-video').prop({ 'title': found.title, 'src': found.src, 'poster': found.poster })
            $('#main-video-title').text(found.title)
            $('#main-video-description').text(found.description)
          } else {
            rabbitHoleVids.push(videos[ i ])
          }
        }
        // Change html elements to reflect found video
        let [ rabbitHoles, a ] = [ $('.rabbit-holes'), 1 ]
        rabbitHoles.html('')
        rabbitHoleVids.forEach(function (video, i) {
          let videoHtml =
            '<video id=\'' + 'rabbit-hole-vids-' + a + '\' class=\'rabbit-hole-vids\' controls' +
            ' muted' + ' ' +
            'poster=\'' + rabbitHoleVids[ i ].poster + '\'' +
            'title=\'' + rabbitHoleVids[ i ].title + '\'' +
            'src=\'' + rabbitHoleVids[ i ].src + '\'' +
            'width=\'' + rabbitHoleVids[ i ].width + '\'' +
            'height=\'' + rabbitHoleVids[ i ].height + '\'' +
            'Sorry, your browser doesn/\'t support embedded videos.' +
            ' </video>'
          rabbitHoles.append(videoHtml)
          let titleHtml =
            '<p id=rabbit-hole-vid-' + a + '-title class=rabbit-hole-titles>' + rabbitHoleVids[ i ].title + '</p>'
          rabbitHoles.append(titleHtml)
          a++
        })
      })
      .catch(function (videos) {
        console.log('rejected: ' + videos)
      })
    // Retrieve related comments using ajax
    $.ajax({
      type: 'GET',
      url: 'models/get_comment.php',
      data: {
        videoTitle: $(this).prop('title')
      },
      // On Success
      success: function (response) {
        console.log('%cAJAX GET Comments Relative to Video Request Completed', 'color: green')
        // parsing the string from the ajax request into an object
        const comments = JSON.parse(response)
        // clear all comments
        $('#user-comments, #db-comments').empty()
        // for loop to diSplay new comments based on clicked video
        for (let i = 0; i < comments.length; i++) {
          $('#db-comments').prepend('<br>' + 'Username: ' + comments[ i ].author + '<br>' + 'Date: ' + comments[ i ].dateposted + '<br>' + 'Comment: ' + comments[ i ].comment + '<br>')
        }
      },
      // On Failure
      error: function (err) {
        console.log('%cAJAX GET Comments Relative to Video Request Failed' + err, 'color: red')
      }
    })
  })

  // On Click of Search Button
  $(document).on('click', '#search-button', function () {
    // Validate input
    let [ input, i ] = [ encodeURI($('#search-bar').val()), 0 ]
    const count = input.split('%20')
    while (i !== count.length) {
      i++
      input = input.replace('%20', ' ')
    }
    if (input === '' || input.trim().length === 0) {
      alert('Please input a video title.')
      $('#search-bar').val('')
    } else {
      // Retrieve videos and check
      let searchedVidTitle = $('#search-bar').val()
      getVideos
        .then(function (videos) {
          console.log('resolved: ' + videos)
          let [ complete, found, rabbitHoleVids, rabbitHoleTitles ] = [ false, null, [], [] ]
          for (let i = 0, l = videos.length; i < l; i++) {
            if ((videos[ i ].title.toLowerCase() === searchedVidTitle.toLowerCase()) || videos[ i ].title.toLowerCase().indexOf(searchedVidTitle.toLowerCase()) > -1) {
              found = videos[ i ]
              $('#main-video').prop({
                'title': found.title,
                'src': found.src,
                'poster': found.poster,
                'description': found.description
              })
              $('#main-video-title').text(found.title)
              $('#main-video-description').text(found.description)
            } else {
              // Pushes object to variable
              rabbitHoleVids.push(videos[ i ])
              rabbitHoleTitles.push(videos[ i ].title)
            }
          }
          complete = true
          if (found == null) {
            alert('No video has been found with that title')
          } else {
            // Displaying Rabbit Hole Videos
            if (complete === true) {
              let [ a, b, rabbitHoles ] = [ 1, 0, $('.rabbit-holes') ]
              rabbitHoles.html('')
              rabbitHoleVids.forEach(function (video, i) {
                // creating and displaying new video elements
                let videoHtml =
                  '<video id=\'' + 'rabbit-hole-vid-' + a + '\' class=\'rabbit-hole-vids\' controls' +
                  ' muted' + ' ' +
                  'poster=\'' + rabbitHoleVids[ b ].poster + '\'' +
                  'title=\'' + rabbitHoleVids[ b ].title + '\'' +
                  'src=\'' + rabbitHoleVids[ b ].src + '\'' +
                  'width=\'' + rabbitHoleVids[ b ].width + '\'' +
                  'height=\'' + rabbitHoleVids[ b ].height + '\'' +
                  'Sorry, your browser doesn/\'t support embedded videos.' +
                  ' </video>'
                rabbitHoles.append(videoHtml)
                // creating and displaying new rabbit hole title elements
                let titleHtml =
                  '<p id=rabbit-hole-vid-' + a + '-title class=rabbit-hole-titles></p>'
                rabbitHoles.append(titleHtml)
                a++
                b++
              })
              // setting content for rabbit holes using unused array titles
              $('#rabbit-hole-vid-1-title').text(rabbitHoleTitles[ 0 ])
              $('#rabbit-hole-vid-2-title').text(rabbitHoleTitles[ 1 ])
            } else {
              alert('No video with the title of ' + input + ' has been found.')
            }
            // GET Comments Request
            $.ajax({
              type: 'GET',
              url: 'models/get_comment.php',
              data: {
                videoTitle: found.title
              },
              // On Success
              success: function (response) {
                console.log('%cAJAX GET Comments Relative to Video Request Completed', 'color: green')
                // parsing the string from the ajax request into an object
                const comments = JSON.parse(response)
                // clear all comments
                $('#user-comments, #db-comments').empty()
                // for loop to diSplay new comments based on clicked video
                for (let i = 0; i < comments.length; i++) {
                  $('#db-comments').prepend('<br>' + 'Username: ' + comments[ i ].author + '<br>' + 'Date: ' + comments[ i ].dateposted + '<br>' + 'Comment: ' + comments[ i ].comment + '<br>')
                }
              },
              // On Failure
              error: function (err) {
                console.log('%cAJAX GET Comments Relative to Video Request Failed' + err, 'color: red')
              }
            })
            $('#search-bar').val('')
          }
        })
        .catch(function (videos) {
          console.log('rejected: ' + videos)
        })
    }
  })
})
