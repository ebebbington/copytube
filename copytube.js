// Globals
/* global alert, prompt, $ */

$(document).ready(function () {
  // Ensure username is correct
  function getUsername () {
    username = encodeURI(prompt('Please enter your username below'))
    let count = username.split('%20')
    let i = 0
    while (i !== count.length) {
      i++
      username = username.replace('%20', ' ')
    }
    if (username.length > 80 || username === 'null' || username.trim().length === 0) {
      alert('Please enter an appropriate username between 0 and 81 characters long')
      getUsername()
    }
    const welcomeMessage = 'Hello ' + username + ', and welcome to CopyTube'
    $('#welcome').text(welcomeMessage)
  }
  // Prepare data
  let username
  $('#comment-count').text('0')
  getUsername()

  // Comment character count
  $(document).on('keyup', '#comment-bar', function () {
    let string = $('#comment-bar').val()
    let count = string.length
    $('#comment-count').text(count)
  })

  // ToDo Feature: Removing Drop-down for Search in Prep for Auto-complete
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
    let description = encodeURI($('#comment-bar').val())
    let count = description.split('%20')
    let i = 0
    while (i !== count.length) {
      i++
      description = description.replace('%20', ' ')
    }
    const maxLength = 400
    if (description === '' || description.length > maxLength || description.trim().length === 0) {
      alert('Please input a comment and have it be less than 401 characters long')
      $('#comment-bar').val('')
      $('#comment-count').text('0')
    } else {
      // Create comment date
      let today = new Date()
      const dd = today.getDate()
      const mm = today.getMonth() + 1 // Month was 1 month behind
      const yyyy = today.getFullYear()
      today = yyyy + '-' + mm + '-' + dd
      // Concatenate full comment
      var actualComment = '<br>' + '<br>' + 'Username: ' + username + '<br>' + 'Date: ' + today + '<br>' + 'Comment: ' + description + '<br>' + '<br>'
      $('#user-comments').prepend(actualComment)
      $('#comment-bar').val('')
      $('#comment-count').text('0')
      // Save comment to database
      const mainVidTitle = $('#main-video-title').text()
      $.ajax({
        type: 'POST',
        url: 'models/savecomment.php',
        data: {
          author: username,
          comment: description,
          dateposted: today,
          videotitle: mainVidTitle
        },
        success: function (response) {
          console.log('%cAJAX POST Comment Request Completed', 'color: green')
        },
        error: function (err) {
          console.log('%cAJAX POST Comment Request Failed', 'color: red')
        }
      }) //I can use "var_dump($_[typename])" to get props in network response which i an then do "var_dump($_POST[author])" to get value of this property
    }
  })

  //region On Click of Rabbit Hole Video
  $(document).on('click', '.rabbit-hole-vid', function () {

    //region AJAX Request: Get Videos
    var clicked_vid_title = $(this).prop('title')
    $.ajax({
      type: 'GET',
      url: 'models/getvideos.php',
      data: {
        videotitle: clicked_vid_title
      },
      //region On Success
      success: function (response) {
        console.log('%cAJAX GET Videos Request Completed', 'color: green')
        //parsing the string from the ajax request into an object
        var videos = JSON.parse(response)
        //Looking For Videos
        var rabbit_hole_vids = []
        var found = null
        for (var i = 0, l = videos.length; i < l; i++) {

          if (clicked_vid_title == videos[i].title) {
            found = videos[i]
            $('#main-video').prop('title', found.title)
            $('#main-video').prop('src', found.src)
            $('#main-video').prop('poster', found.poster)
            $('#main-video-title').text(found.title)
            $('#main-video-description').text(found.description)
          } else {
            rabbit_hole_vids.push(videos[i])
          }
        }

        //changing rabbit hole elements
        a = 1
        var rabbit_holes = $('.rabbit-holes')
        rabbit_holes.html('')
        rabbit_hole_vids.forEach(function (video, i) {
          //creating and displaying new video elements
          var video_html =
            '<video id=\'' + 'rabbit-hole-vid-' + a + '\' class=\'rabbit-hole-vid\' controls' +
            ' muted' + ' ' +
            'poster=\'' + rabbit_hole_vids[i].poster + '\'' +
            'title=\'' + rabbit_hole_vids[i].title + '\'' +
            'src=\'' + rabbit_hole_vids[i].src + '\'' +
            'width=\'' + rabbit_hole_vids[i].width + '\'' +
            'height=\'' + rabbit_hole_vids[i].height + '\'' +
            'Sorry, your browser doesn/\'t support embedded videos.' +
            ' </video>'
          rabbit_holes.append(video_html)
          //creating and displaying new rabbit hole title elements
          var title_html =
            '<p id=rabbit-hole-vid-' + a + '-title class=rabbit-hole-titles>' + rabbit_hole_vids[i].title + '</p>'
          rabbit_holes.append(title_html)
          a++
        })
      },
      //endregion

      //region On Failure
      error: function (err) {
        console.log('%cAJAX GET Videos Request Failed', 'color: red')
      }
      //endregion
    })
    //endregion

    //region AJAX Request: Get Comments Relative to Video
    $.ajax({
      type: 'GET',
      url: 'models/getcomment.php',
      data: {
        videotitle: $(this).prop('title')
      },
      //region On Success
      success: function (response) {
        console.log('%cAJAX GET Comments Relative to Video Request Completed', 'color: green')
        //parsing the string from the ajax request into an object
        var obj = JSON.parse(response)
        //clear all comments
        $('#user-comments').empty()
        $('#db-comments').empty()
        //for loop to diSplay new comments based on clicked video
        for (var i = 0; i < obj.length; i++) {
          $('#db-comments').prepend('<br>' + 'Username: ' + obj[i].author + '<br>' + 'Date: ' + obj[i].dateposted + '<br>' + 'Comment: ' + obj[i].comment + '<br>')
        }
      },
      //endregion

      //region On Failure
      error: function (err) {
        console.log('%cAJAX GET Comments Relative to Video Request Failed', 'color: red')
      }
      //endregion
    })
    //endregion
  })
  //endregion

  //region On Click of Search Button
  $(document).on('click', '#search-button', function () {
    //region Encoding & Validating Input
    var input = encodeURI($('#search-bar').val())
    var count = input.split('%20')
    var i = 0
    while (i != count.length) {
      i++
      input = input.replace('%20', ' ')
    }
    if (input == '' || input == ' ' || (jQuery.trim(input)).length == 0) {
      alert('Please input a video title.')
      $('#search-bar').val('')
    }
    //endregion

    //region GET Videos Request
    var searched_vid_title = $('#search-bar').val()
    $.ajax({
      type: 'GET',
      url: 'models/getvideos.php',
      data: {
        videotitle: searched_vid_title
      },
      //region On Success
      success: function (response) {
        console.log('%cAJAX GET Videos Request Completed', 'color: green')
        //parsing the string from the ajax request into an object
        var videos = JSON.parse(response)
        //Looking For Videos
        var complete = false
        var found = null
        var rabbit_hole_vids = []
        var rabbit_hole_titles = []
        //region Getting main video and rabbit hole data
        for (var i = 0, l = videos.length; i < l; i++) {
          if ((videos[i].title.toLowerCase() === input.toLowerCase()) || videos[i].title.toLowerCase().indexOf(input.toLowerCase()) > -1) {
            found = videos[i]
            $('#main-video').prop('title', found.title)
            $('#main-video').prop('src', found.src)
            $('#main-video').prop('poster', found.poster)
            $('#main-video0').prop('description', found.description)
            $('#main-video-title').text(found.title)
            $('#main-video-description').text(found.description)
            searched_vid_title = found.title
          } else {
            //Pushes object to variable
            rabbit_hole_vids.push(videos[i])
            rabbit_hole_titles.push(videos[i].title)
          }
        }
        complete = true
        //endregion

        //region Displaying Rabbit Hole Videos
        if (complete == true) {
          var b = 0
          a = 1
          var rabbit_holes = $('.rabbit-holes')
          rabbit_holes.html('')
          rabbit_hole_vids.forEach(function (video, i) {
            //creating and displaying new video elements
            var video_html =
              '<video id=\'' + 'rabbit-hole-vid-' + a + '\' class=\'rabbit-hole-vid\' controls' +
              ' muted' + ' ' +
              'poster=\'' + rabbit_hole_vids[b].poster + '\'' +
              'title=\'' + rabbit_hole_vids[b].title + '\'' +
              'src=\'' + rabbit_hole_vids[b].src + '\'' +
              'width=\'' + rabbit_hole_vids[b].width + '\'' +
              'height=\'' + rabbit_hole_vids[b].height + '\'' +
              'Sorry, your browser doesn/\'t support embedded videos.' +
              ' </video>'
            rabbit_holes.append(video_html)
            //creating and displaying new rabbit hole title elements
            var title_html =
              '<p id=rabbit-hole-vid-' + a + '-title class=rabbit-hole-titles></p>'
            rabbit_holes.append(title_html)
            a++
            b++

          })
          //setting content for rabbit holes using unused array titles
          $('#rabbit-hole-vid-1-title').text(rabbit_hole_titles[0])
          $('#rabbit-hole-vid-2-title').text(rabbit_hole_titles[1])
        } else {
          alert('No video with the title of ' + '\'' + input + '\' has been found.')
        }
        //endregion

        //region GET Comments Request
        $.ajax({
          type: 'GET',
          url: 'models/getcomment.php',
          data: {
            videotitle: found.title
          },
          //region On Success
          success: function (response) {
            console.log('%cAJAX GET Comments Relative to Video Request Completed', 'color: green')
            //parsing the string from the ajax request into an object
            var obj = JSON.parse(response)
            //clear all comments
            $('#user-comments').empty()
            $('#db-comments').empty()
            //for loop to diSplay new comments based on clicked video
            for (var i = 0; i < obj.length; i++) {
              $('#db-comments').prepend('<br>' + 'Username: ' + obj[i].author + '<br>' + 'Date: ' + obj[i].dateposted + '<br>' + 'Comment: ' + obj[i].comment + '<br>')
            }
          },
          //endregion

          //region On Failure
          error: function (err) {
            console.log('%cAJAX GET Comments Relative to Video Request Failed', 'color: red')
          }
          //endregion
        })
        $('#search-bar').val('')
        //endregion
      },
      //endregion

      //region On Failure
      error: function (err) {
        console.log('%cAJAX GET Videos Request Failed', 'color: red')
      }
      //endregion
    })
    //endregion
  })
  //endregion
})