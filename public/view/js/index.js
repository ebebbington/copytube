/* This script handles events */

/* global $, alert */
'use strict'

const getVideos = new Promise(function (resolve, reject) {
  $.ajax({
    type: 'POST',
    url: '../../classes/controllers/videos.php',
    data: {
      action: 'getAllVideos'
    },
    success: function (response) {
      const videos = JSON.parse(response)
      resolve(videos)
    },
    error: function (error) {
      console.log(error + '\n' + reject)
      reject(error)
    }
  })
})

const getComments = new Promise(function (resolve, reject) {
  $.ajax({
    type: 'POST',
    url: '../../classes/controllers/comments.php',
    data: {
      action: 'getComments'
    },
    success: function (response) {
      const comments = JSON.parse(response)
      resolve(comments)
    },
    error: function (error) {
      console.log(error + '\n' + reject)
      reject(error)
    }
  })
})

// Log Out function
function logOut () {
  $.ajax({
    type: 'POST',
    url: 'http://localhost/copytube/classes/controllers/user.php',
    data: {
      action: 'logout'
    },
    success: function () {
      window.location.replace('http://localhost/copytube/public/view/login.html')
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
      type: 'POST',
      url: 'http://localhost/copytube/controllers/videos-controller.php',
      data: {
        action: 'getVideos'
      },
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
  const [ comment, maxLength ] = [ $('#comment-bar').val(), 400 ]
  if (comment === '' || comment > maxLength || comment.trim().length === 0 || comment === null || comment === undefined) {
    alert('Enter correct information you lil rascal with a max length of: ' + maxLength)
    $('#comment-bar').val('')
  } else {
    let today = new Date()
    const [ dd, mm, yyyy ] = [ today.getDate(), (today.getMonth() + 1), today.getFullYear() ] // Month was 1 behind
    today = yyyy + '-' + mm + '-' + dd
    // Save comment to database
    const mainVidTitle = $('#main-video-title').text()
    $.ajax({
      type: 'POST',
      url: '../../classes/controllers/comments.php',
      data: {
        comment: comment,
        datePosted: today,
        videoTitle: mainVidTitle,
        action: 'addComment'
      },
      success: function (response) {
        console.log('%cAJAX POST Comment Request Completed', 'color: green')
        // Concatenate full comment
        const output = JSON.parse(response)
        if (output === false) {
          $('#comment-error').text('Unable to save comment')
          return false
        } else {
          const actualComment = '<br>' + '<br>' + 'Username: ' + output + '<br>' + 'Date: ' + today + '<br>' + 'Comment: ' + comment + '<br>' + '<br>'
          $('#user-comments').prepend(actualComment)
          $('#comment-bar').val('')
          $('#comment-count').text('0')
          return false
        }
      },
      error: function (err) {
        console.log('%cAJAX POST Comment Request Failed: ' + err, 'color: red')
      }
    })
  }
}

$(document).ready(function () {
  //
  // Display all DB data
  //
  getVideos
    .then(function (videos) {
      let count = 1
      for (let i = 0, l = videos.length; i < l; i++) {
        // Dropdown titles
        let title = "<a href='#' id='dropdown-title-'" + count + "class='dropdown-titles'>" + videos[ i ][ 'title' ] + '</a>'
        $('.dropdown-content').prepend(title)
        // Rabbit hole videos
        if (videos[0]['title'] !== 'Something More') {
          let video = "<video id='rabbit-hole-vid-'" + count + " class='rabbit-hole-videos' controls " +
            ' muted ' +
            ' poster=' + videos[i]['poster'] + ' title=' + videos[i]['title'] + ' src=' + videos[i]['src'] +
            'width=' + videos[i]['width'] + 'height=' + videos[i]['height'] + '></video>'
          let videoTitle = "<p id='rabbit-hole-vid-'" + count + "'-title' class='rabbit-hole-titles'>" + videos[i]['title'] + '</p>'
          $('.rabbit-holes').prepend(video, videoTitle)
        }
        count++
      }
    })
  getComments
    .then(function (comments) {
      for (let i = 0, l = comments.length; i < l; i++) {
        if (comments[i]['title'] === 'Something More') {
          let newComment = "<div class='well'>Username: " + comments[i]['author'] + '<br>Date: ' + comments[i]['dateposted'] + '<br>Comment: ' + comments[i]['comment'] + '<br><br><br>'
          $('#db-comments').prepend(newComment)
        }
      }
    })
  //
  // Comment character count
  //
  $(document).on('keyup', '#comment-bar', function () {
    const commentLength = $('#comment-bar').val().length
    $('#comment-count').text(commentLength)
  })
  //
  // On click of the drodown content
  //
  $(document).on('click', '.dropdown-titles', function () {
    const videoTitle = $(this).text()
    getVideos
    // Data: title, src, description, height, width, poster
      .then(function (videos) {
        console.log('resolved: ' + videos)
      }) // above runs if resolved
      .catch(function (videos) {
        console.log('rejected: ' + videos)
      })
  })
  //
  // On Click of A Rabbit Hole Video
  //
  $(document).on('click', '.rabbit-hole-videos', function () {
    getVideosAndComments($(this).prop('title'), 80)
  })
  //
  // On Click of Search Button
  //
  $(document).on('click', '#search-button', function () {
    getVideosAndComments($('#search-bar').val(), 80)
  })
})
