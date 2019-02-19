/* This script handles events */

/* global $, alert */
'use strict'

// Refresh web page
function refresh () {
  window.location.replace('http://localhost/copytube/index/copytube.php')
}

// check if user is logged in and redirect if needed
function getUsername (param) {
  $.ajax({
    type: 'GET',
    url: 'http://localhost/copytube/models/get-username.php',
    success: function (username) {
      // return username if needed
      // if user tries to access page but not logged in divert back to login
      if (username === 'false') {
        window.location.replace('http://localhost/copytube/login/login.html')
      } else {
        document.cookie = 'name=' + username + "'"
        if (param === 'welcome message') {
          $('#welcome').text(username + ', welcome to CopyTube')
        } else {
          return username
        }
      }
    },
    error: function () {
      alert('TESTTTTTT')
    }
  })
}

// Log Out function
function logOut () {
  $.ajax({
    type: 'POST',
    url: 'http://localhost/copytube/models/log-out.php',
    success: function (response) {
      alert(response)
      if (JSON.parse(response) === true) {
        window.location.replace('http://localhost/copytube/login/login.html')
      }
    }
  })
}

// Retrieve videos and comments from DB and export
function getVideosAndComments (videoTitle, maxLength) {
  if (videoTitle === '' || videoTitle > maxLength || videoTitle.trim().length === 0 || videoTitle === null || videoTitle === undefined) {
    alert('Enter correct information you lil rascal with a max length of: ' + maxLength)
  } else {
    // Get Videos
    $.ajax({
      type: 'GET',
      url: 'http://localhost/copytube/models/get_videos.php',
      success: function (response) {
        const videos = JSON.parse(response)
        let [ rabbitHoleVideos, rabbitHoleTitles, found ] = [ [], [], null ]
        // Find video if possible
        for (let i = 0, l = videos.length; i < l; i++) {
          if ((videos[ i ].title.toLowerCase() === videoTitle.toLowerCase()) || videos[ i ].title.toLowerCase().indexOf(videoTitle.toLowerCase()) > -1) {
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
            rabbitHoleVideos.push(videos[ i ])
            rabbitHoleTitles.push(videos[ i ].title)
          }
        }
        if (found == null) {
          alert('No video has been found with that title')
        } else {
          // Display Rabbit Hole Videos
          let [ a, b, rabbitHoles ] = [ 1, 0, $('.rabbit-holes') ]
          rabbitHoles.html('')
          rabbitHoleVideos.forEach(function (video, i) {
            // creating and displaying new video elements
            let videoHtml =
              '<video id=\'' + 'rabbit-hole-vid-' + a + '\' class=\'rabbit-hole-videos\' controls' +
              ' muted' + ' ' +
              'poster=\'' + rabbitHoleVideos[ b ].poster + '\'' +
              'title=\'' + rabbitHoleVideos[ b ].title + '\'' +
              'src=\'' + rabbitHoleVideos[ b ].src + '\'' +
              'width=\'' + rabbitHoleVideos[ b ].width + '\'' +
              'height=\'' + rabbitHoleVideos[ b ].height + '\'' +
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
          // GET Comments Request
          $.ajax({
            type: 'GET',
            url: 'http://localhost/copytube/models/get_comment.php',
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
      },
      error: function (error) {
        console.log('%cAJAX GET comments Failed: ' + error, 'color: red')
      }
    })
  }
}

// Save comments assuming input is validated todo :: check and remove code one validation is complete
function addComment () {
  const [ comment, maxLength, username ] = [ $('#comment-bar').val(), 400, $('#welcome-username').text() ]
  if (comment === '' || comment > maxLength || comment.trim().length === 0 || comment === null || comment === undefined) {
    alert('Enter correct information you lil rascal with a max length of: ' + maxLength)
    $('#comment-bar').val('')
  } else {
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
      url: 'http://localhost/copytube/models/save_comment.php',
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
    })
  }
}

$(document).ready(function () {
  // Comment character count
  $(document).on('keyup', '#comment-bar', function () {
    const commentLength = $('#comment-bar').val().length
    $('#comment-count').text(commentLength)
  })
  // On Click of Add Comment Button
  $('#comment-button').on('click', function () {
    // Run addComment function
    addComment()
  })
  // On click of the drodown content
  $(document).on('click', '.dropdown-titles', function () {
    getVideosAndComments($(this).text(), 80)
  })
  // On Click of A Rabbit Hole Video
  $(document).on('click', '.rabbit-hole-videos', function () {
    getVideosAndComments($(this).prop('title'), 80)
  })
  // On Click of Search Button
  $(document).on('click', '#search-button', function () {
    getVideosAndComments($('#search-bar').val(), 80)
  })
  // On click of log out button
  $(document).on('click', '#log-out', function () {
    logOut()
  })
  // Refresh page automatically to check cookies
  setInterval(function () {
    refresh()
  }, 600000)
})
