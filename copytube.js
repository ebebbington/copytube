// Globals
/* global alert, prompt, $ */
'use strict'
let username

// todo :: come back to adams function
// Adams help in showing how to make a gawj function - look into at a later date as it might become useful
/* let videos = (function () {
  let Videos = []
  function initialise (Videos) {
    doesSomething(Videos)
  }
  function doesSomething (Videos) {
    // AJAX call
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

// todo :: Promise - replace ajax requests with below code to call
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
  // Ensure username is correct
  // todo :: add regrex validation
  (function () {
    let [complete, username] = [false, '']
    const errorMsg = 'Please enter an appropriate username between 0 and 81 characters long'
    const askUsernameMsg = 'Enter your username. \nUsername must be: \n- Between 0-81 characters \n- Contain only numbers and letters'
    while (complete !== true) {
      username = encodeURI(prompt(askUsernameMsg))
      // Taken out as not needed but cause be useful (split, replace)
      /* const count = username.split('%20')
      let i = 0
      while (i !== count.length) {
        i++
        username = username.replace('%20', ' ')
      } */
      username.length > 80 || username === 'null' || username.trim().length === 0 ? alert(errorMsg) : complete = true
    }
    const welcomeMessage = 'Hello ' + username + ', and welcome to CopyTube'
    $('#welcome').text(welcomeMessage)
  })()

  // Comment character count
  $(document).on('keyup', '#comment-bar', function () {
    const commentLength = $('#comment-bar').val().length // todo :: i realise i can add 'length' to the end, this means i dont need a counter - check the script for this and use the new method
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
    if (comment.length > maxLength || comment.trim().length === 0) {
      alert('Please input a comment and have it be less than 401 characters long')
      $('#comment-bar').val('')
      $('#comment-count').text('0')
    } else {
      // Create comment date
      let today = new Date()
      const [dd, mm, yyyy] = [today.getDate(), (today.getMonth() + 1), today.getFullYear()] // Month was 1 behind
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

  // On Click of Rabbit Hole Video
  $(document).on('click', '.rabbit-hole-vid', function () {
    // Get Videos from database
    const clickedVidTitle = $(this).prop('title')
    $.ajax({
      type: 'GET',
      url: 'models/get_videos.php',
      data: {
        videoTitle: clickedVidTitle
      },
      // On Success
      success: function (response) {
        console.log('%cAJAX GET Videos Request Completed', 'color: green')
        // parsing the string from the ajax request into an object so it can be used
        const videos = JSON.parse(response)
        // Locate clicked video
        let [rabbitHoleVids, found] = [[], null]
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
        let [rabbitHoles, a] = [$('.rabbit-holes'), 1]
        rabbitHoles.html('')
        rabbitHoleVids.forEach(function (video, i) {
          let videoHtml =
            '<video id=\'' + 'rabbit-hole-vid-' + a + '\' class=\'rabbit-hole-vid\' controls' +
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
      },
      // On Failure
      error: function (err) {
        console.log('%cAJAX GET Videos Request Failed' + err, 'color: red')
      }
    })

    // Retrieve related comments
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
        const obj = JSON.parse(response)
        // clear all comments
        $('#user-comments, #db-comments').empty()
        // for loop to diSplay new comments based on clicked video
        for (let i = 0; i < obj.length; i++) {
          $('#db-comments').prepend('<br>' + 'Username: ' + obj[ i ].author + '<br>' + 'Date: ' + obj[ i ].dateposted + '<br>' + 'Comment: ' + obj[ i ].comment + '<br>')
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
    let [input, i] = [encodeURI($('#search-bar').val()), 0]
    const count = input.split('%20')
    while (i !== count.length) {
      i++
      input = input.replace('%20', ' ')
    }
    if (input === '' || input === ' ' || input.trim().length === 0) {
      alert('Please input a video title.')
      $('#search-bar').val('')
    } else {
      // Retrieve videos
      let searchedVidTitle = $('#search-bar').val()
      $.ajax({
        type: 'GET',
        url: 'models/get_videos.php',
        data: {
          videoTitle: searchedVidTitle
        },
        // On Success
        success: function (response) {
          console.log('%cAJAX GET Videos Request Completed', 'color: green')
          let [complete, found, rabbitHoleVids, rabbitHoleTitles] = [false, null, [], []]
          // parsing the string from the ajax request into an object
          const videos = JSON.parse(response)
          // Looking For Videos
          // Getting main video and rabbit hole data
          for (let i = 0, l = videos.length; i < l; i++) {
            if ((videos[ i ].title.toLowerCase() === input.toLowerCase()) || videos[ i ].title.toLowerCase().indexOf(input.toLowerCase()) > -1) {
              found = videos[ i ]
              $('#main-video').prop({
                'title': found.title,
                'src': found.src,
                'poster': found.poster,
                'description': found.description
              })
              $('#main-video-title').text(found.title)
              $('#main-video-description').text(found.description)
              searchedVidTitle = found.title
            } else {
              // Pushes object to variable
              rabbitHoleVids.push(videos[ i ])
              rabbitHoleTitles.push(videos[ i ].title)
            }
          }
          complete = true
          if (found === null) {
            alert('No video has been found with that title')
          } else {
            // Displaying Rabbit Hole Videos
            if (complete === true) {
              let [ a, b, rabbitHoles ] = [ 1, 0, $('.rabbit-holes') ]
              rabbitHoles.html('')
              rabbitHoleVids.forEach(function (video, i) {
                // creating and displaying new video elements
                let videoHtml =
                  '<video id=\'' + 'rabbit-hole-vid-' + a + '\' class=\'rabbit-hole-vid\' controls' +
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
                const obj = JSON.parse(response)
                // clear all comments
                $('#user-comments, #db-comments').empty()
                // for loop to diSplay new comments based on clicked video
                for (let i = 0; i < obj.length; i++) {
                  $('#db-comments').prepend('<br>' + 'Username: ' + obj[ i ].author + '<br>' + 'Date: ' + obj[ i ].dateposted + '<br>' + 'Comment: ' + obj[ i ].comment + '<br>')
                }
              },
              // On Failure
              error: function (err) {
                console.log('%cAJAX GET Comments Relative to Video Request Failed' + err, 'color: red')
              }
            })
            $('#search-bar').val('')
          }
        },
        // On Failure
        error: function (err) {
          console.log('%cAJAX GET Videos Request Failed' + err, 'color: red')
        }
      })
    }
  })
})
