/* global $, alert */
'use strict'

const getKey = new Promise(function (resolve, reject) {
  $.ajax({
    type: 'POST',
    url: '../../classes/controllers/user.php',
    data: { action: 'getKey' },
    success: function (response) {
      console.log(response)
      const key = JSON.parse(response)
      resolve(key)
    },
    error: function (error) {
      reject(error)
    }
  })
})
function api () {
  getKey
    .then(function (key) {
      $.ajax({
        type: 'POST',
        url: 'http://localhost:3003/users/Edward',
        data: {
          uid: key[0],
          key: key[1]
        },
        success: function (response) {
          console.log(response)
        },
        error: function (error) {
          console.log('error accessing api server ' + Object.getOwnPropertyNames(error))
        }
      })
    })
}

//
// Check if user is logged in
//
$.ajax({
  type: 'POST',
  url: '../../classes/controllers/user.php',
  data: {
    action: 'checkSession'
  },
  success: function (response) {
    if (response === '') {
    } else {
      const output = JSON.parse(response)
      if (output[ 0 ] === 'logout' && output[ 1 ] === true) {
        window.location.replace('login.html')
      } else {
      }
    }
  }
})

//
// Module Design Pattern
//
let IndexFunctionality = (function () {
  function init () {
    // /////////////////////////////////////////
    //              Promises data
    // /////////////////////////////////////////
    // Thi
    const getUser = new Promise(function (resolve, reject) {
      $.ajax({
        type: 'POST',
        url: '../../classes/controllers/user.php',
        data: {
          action: 'getUser'
        },
        success: function (response) {
          console.log(response)
          const user = JSON.parse(response)
          resolve(user)
        },
        error: function (error) {
          console.log(error + reject)
          reject(error)
        }
      })
    })
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
    // /////////////////////////////////////////
    //                Functions data
    // /////////////////////////////////////////
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
    function addComment () {
      // noinspection JSJQueryEfficiency
      const [ comment, maxLength ] = [ $('#comment-bar').val(), 400 ]
      // noinspection JSJQueryEfficiency
      $('#comment-error').text('')
      // Validation
      if (comment === '' || comment > maxLength || comment.trim().length === 0 || comment === null || comment === undefined) {
        $('#comment-error').text('Enter correct information you lil rascal with a max length of: ' + maxLength)
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
            const output = JSON.parse(response)
            if (output === false) {
              $('#comment-error').text('Unable to save comment')
            } else {
              let test = "<div class='media'>" +
                "<div class='media-left'>" +
                "<img src='../../images/sample.jpg' class='media-object' style='width:45px; height:45px; border-radius:80%'>" +
                '</div>' +
                "<div class='media-body'>" +
                "<h4 class='media-heading'>" + output[ 0 ] +
                '<small><i> ' + output[ 2 ] + '</i></small></h4>' +
                '<p style="word-break: break-all">' + output[ 1 ] + '</p>' +
                '</div>' +
                '</div>' + '<br>'
              $('#db-comments').prepend(test)
              $('#comment-bar').val('')
              $('#comment-count').text(0)
            }
          },
          error: function (err) {
            console.log('%cAJAX POST Comment Request Failed: ' + err, 'color: red')
          }
        })
      }
    }
    function getContent (videoTitle) {
      getVideos
        .then(function (videos) {
          let [ mainVideo, rabbitHoleVideos, titleFound ] = [ [], [], false ]
          //
          // Validation
          //
          for (let i = 0, l = videos.length; i < l; i++) {
            if (videoTitle.toLowerCase() === videos[ i ][ 'title' ].toLowerCase()) {
              titleFound = true
              mainVideo.push(videos[ i ])
            } else {
              rabbitHoleVideos.push(videos[ i ])
              if (i === videos.length && titleFound === false) {
                alert('No video has been found')
                break
              }
            }
          }
          //
          // Main Video
          //
          $('#my-video-info').html('')
          let eContainer = $('.my-video').html('')
          let html = "<div class='my-video col-xs-12'>" +
            "<video id='main-video' controls " + 'autoplay' + ' muted' +
            " poster='" + mainVideo[ 0 ][ 'poster' ] + "'" +
            " title='" + mainVideo[ 0 ][ 'title' ] + "'" +
            " src='" + mainVideo[ 0 ][ 'src' ] + "'" +
            " width='750'" + " height='400'" +
            ' Sorry, your browser doesnt support embedded videos' + '</video>'
          eContainer.append(html)
          html = "<p id='main-video-title'>" + mainVideo[ 0 ][ 'title' ] + '</p>' +
            '<br>' +
            "<p id='main-video-description'>" + mainVideo[ 0 ][ 'description' ] + '</p>' + '<br>'
          $('#my-video-info').append(html)
          //
          // Dropdown titles
          //
          // Below it throws a wobbly on unused variables
          eContainer = $('.dropdown-content').html('')
          let count = 1
          for (let i = 0, l = videos.length; i < l; i++) {
            html = "<a href='#' id='dropdown-title-" + count + "'" + ' class="dropdown-titles">' + videos[ i ][ 'title' ] + '</a>'
            eContainer.prepend(html)
          }
          //
          // Rabbit holes
          //
          eContainer = $('.rabbit-hole-content').html('')
          count = 1
          for (let i = 0, l = rabbitHoleVideos.length; i < l; i++) {
            html = '<div id="video-' + count + '">' +
              '<video id=rabbit-hole-vid-' + count + " class='rabbit-hole-videos' controls " +
              ' muted ' +
              ' poster=' + rabbitHoleVideos[ i ][ 'poster' ] +
              ' title="' + rabbitHoleVideos[ i ][ 'title' ] + '"' + ' src=' + rabbitHoleVideos[ i ][ 'src' ] +
              ' width=' + rabbitHoleVideos[ i ][ 'width' ] + ' height="' + rabbitHoleVideos[ i ][ 'height' ] + '"></video>'
            eContainer.append(html)
            html = "<p id='rabbit-hole-vid-'" + count + "'-title' class='rabbit-hole-titles'>" + rabbitHoleVideos[ i ][ 'title' ] + '</p>'
            eContainer.append(html)
            count++
          }
        })
      getComments
        .then(function (comments) {
          $('#user-comments, #db-comments').empty()
          for (let i = 0, l = comments.length; i < l; i++) {
            if (comments[ i ][ 'title' ] === videoTitle) {
              let test = "<div class='media'>" +
                "<div class='media-left'>" +
                "<img src='../../images/sample.jpg' class='media-object' style='width:45px; height:45px; border-radius:80%'>" +
                '</div>' +
                "<div class='media-body'>" +
                "<h4 class='media-heading'>" + comments[ i ][ 'author' ] +
                '<small><i> ' + comments[ i ][ 'dateposted' ] + '</i></small></h4>' +
                '<p style="word-break: break-all">' + comments[ i ][ 'comment' ] + '</p>' +
                '</div>' +
                '</div>' + '<br>'
              console.log(test)
              $('#db-comments').prepend(test)
            }
          }
        })
      $('#search-bar').val('')
    }
    // /////////////////////////////////////////
    //                Load content
    // /////////////////////////////////////////
    (function () {
      getUser
        .then(function (user) {
          $('#welcome').text(user[0]['username'] + ', welcome to CopyTube')
          console.table(user)
        })
      getContent('Something More')
    })()
    // /////////////////////////////////////////
    //                Event Handler
    // /////////////////////////////////////////
    $(document).ready(function () {
      //
      // Comment character count
      //
      $('#comment-bar').on('keyup', function () {
        $('#comment-count').text($('#comment-bar').val().length)
      })
      //
      // On click of Rabbit Hole
      //
      $('.rabbit-hole-content').on('click', '.rabbit-hole-videos', function () {
        getContent($(this).attr('title'))
      })
      //
      // On click of Search Button
      //
      $('#search-button').on('click', function () {
        getContent($('#search-bar').val())
      })
      //
      // On click of Dropdown
      //
      $('.dropdown-content').on('click', '.dropdown-titles', function () {
        getContent($(this).text())
      })
      // On click of log out
      //
      $('#log-out').on('click', function () {
        logOut()
      })
      //
      // On click of Add Comment
      //
      $('#comment-button').on('click', function () {
        addComment()
      })
    })
  }
  return {
    init: init,
    key: init.getKey
  }
})()

IndexFunctionality.init()

/*
// /////////////////////////////////////////
//              Promises
// /////////////////////////////////////////
//
// User promise
//
const getUser = new Promise(function (resolve, reject) {
  $.ajax({
    type: 'POST',
    url: '../../classes/controllers/user.php',
    data: {
      action: 'getUser'
    },
    success: function (response) {
      const user = JSON.parse(response)
      resolve(user)
    },
    error: function (error) {
      console.log(error + reject)
      reject(error)
    }
  })
})
//
// Videos promise
//
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
//
// Comments promise
//
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

// /////////////////////////////////////////
//                Functions
// /////////////////////////////////////////
//
// Log Out function
//
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
//
// Save comments
//
function addComment () {
  // noinspection JSJQueryEfficiency
  const [ comment, maxLength ] = [ $('#comment-bar').val(), 400 ]
  // noinspection JSJQueryEfficiency
  $('#comment-error').text('')
  // Validation
  if (comment === '' || comment > maxLength || comment.trim().length === 0 || comment === null || comment === undefined) {
    $('#comment-error').text('Enter correct information you lil rascal with a max length of: ' + maxLength)
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
        const output = JSON.parse(response)
        if (output === false) {
          $('#comment-error').text('Unable to save comment')
        } else {
          const newComment = "<div class='well'>Username: " + output + '<br>Date: ' + today + '<br>Comment: ' + comment
          $('#db-comments').prepend(newComment)
          $('#comment-bar').val('')
          $('#comment-count').text(0)
          alert(output)
        }
      },
      error: function (err) {
        console.log('%cAJAX POST Comment Request Failed: ' + err, 'color: red')
      }
    })
  }
}
//
// Display Videos and Comments
//
function getContent (videoTitle) {
  getVideos
    .then(function (videos) {
      let [ mainVideo, rabbitHoleVideos, titleFound ] = [ [], [], false ]
      //
      // Validation
      //
      for (let i = 0, l = videos.length; i < l; i++) {
        if (videoTitle.toLowerCase() === videos[ i ][ 'title' ].toLowerCase()) {
          titleFound = true
          mainVideo.push(videos[ i ])
        } else {
          rabbitHoleVideos.push(videos[ i ])
          if (i === videos.length && titleFound === false) {
            alert('No video has been found')
            break
          }
        }
      }
      //
      // Main Video
      //
      let eContainer = $('.my-video').html('')
      let html = "<div class='my-video col-xs-12'>" +
        "<video id='main-video' controls " + 'autoplay' + ' muted' +
        " poster='" + mainVideo[ 0 ][ 'poster' ] + "'" +
        " title='" + mainVideo[ 0 ][ 'title' ] + "'" +
        " src='" + mainVideo[ 0 ][ 'src' ] + "'" +
        " width='750'" + " height='400'" +
        ' Sorry, your browser doesnt support embedded videos' + '</video>'
      eContainer.append(html)
      html = "<p id='main-video-title'>" + mainVideo[ 0 ][ 'title' ] + '</p>' +
        '<br>' +
        "<p id='main-video-description'>" + mainVideo[ 0 ][ 'description' ] + '</p>' + '<br>'
      eContainer.append(html)
      //
      // Dropdown titles
      //
      eContainer = $('.dropdown-content').html('')
      let count = 1
      for (let i = 0, l = videos.length; i < l; i++) {
        html = "<a href='#' id='dropdown-title-'" + count + " class='dropdown-titles'>" + videos[ i ][ 'title' ] + '</a>'
        eContainer.prepend(html)
      }
      //
      // Rabbit holes
      //
      eContainer = $('.rabbit-hole-content').html('')
      count = 1
      for (let i = 0, l = rabbitHoleVideos.length; i < l; i++) {
        html = '<video id=rabbit-hole-vid-' + count + " class='rabbit-hole-videos' controls " +
          ' muted ' +
          ' poster=' + rabbitHoleVideos[ i ][ 'poster' ] +
          ' title="' + rabbitHoleVideos[ i ][ 'title' ] + '" src=' + rabbitHoleVideos[ i ][ 'src' ] +
          ' width=' + rabbitHoleVideos[ i ][ 'width' ] + ' height="' + rabbitHoleVideos[ i ][ 'height' ] + '"></video>'
        eContainer.append(html)
        html = "<p id='rabbit-hole-vid-'" + count + "'-title' class='rabbit-hole-titles'>" + rabbitHoleVideos[ i ][ 'title' ] + '</p>'
        eContainer.append(html)
        count++
      }
    })
  getComments
    .then(function (comments) {
      $('#user-comments, #db-comments').empty()
      for (let i = 0, l = comments.length; i < l; i++) {
        if (comments[ i ][ 'title' ] === videoTitle) {
          let test = "<div class='media'>" +
            "<div class='media-left'>" +
            "<img src='../../images/sample.jpg' class='media-object' style='width:45px; height:45px; border-radius:80%'>" +
            '</div>' +
            "<div class='media-body'>" +
            "<h4 class='media-heading'>" + comments[ i ][ 'author' ] +
            '<small><i> ' + comments[ i ][ 'dateposted' ] + '</i></small></h4>' +
            '<p style="word-break: break-all">' + comments[ i ][ 'comment' ] + '</p>' +
            '</div>' +
            '</div>' + '<br>'
          $('#db-comments').prepend(test)
        }
      }
    })
  $('#search-bar').val('')
}

// /////////////////////////////////////////
//                Events
// /////////////////////////////////////////
$(document).ready(function () {
  //
  // Display content
  //
  getContent('Something More')
  //
  // Display welcome message
  //
  $('#welcome').text(function () {
    getUser
      .then(function (user) {
        $('#welcome').text(user[0][ 'username' ])
      })
  })
  //
  // Comment character count
  //
  $('#comment-bar').on('keyup', function () {
    $('#comment-count').text($('#comment-bar').val().length)
  })
  //
  // On click of Rabbit Hole
  //
  $('.rabbit-hole-content').on('click', function () {
    getContent($(this).prop('title'))
  })
  //
  // On click of Search Button
  //
  $('#search-button').on('click', function () {
    getContent($('#search-bar').val())
  })
  //
  // On click of Dropdown
  //
  $('.dropdown-content').on('click', function () {
    getContent($(this).text())
  })
  //
  // On click of log out
  //
  $('#log-out').on('click', function () {
    logOut()
  })
  //
  // On click of Add Comment
  //
  $('#comment-button').on('click', function () {
    addComment()
  })
})
*/
