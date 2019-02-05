/* globals $ */

// Retrieve videos and comments from DB and export
export function getVideosAndComments (arg1) {
  const clickedVideoTitle = arg1
  // Get Videos
  $.ajax({
    type: 'GET',
    url: 'models/get_videos.php',
    success: function (response) {
      const videos = JSON.parse(response)
      let [ rabbitHoleVideos, found ] = [ [], null ]
      // Find video
      // todo :: add validation for if user uses search bar
      for (let i = 0, l = videos.length; i < l; i++) {
        if (clickedVideoTitle === videos[ i ].title) {
          found = videos[ i ]
          $('#main-video').prop({ 'title': found.title, 'src': found.src, 'poster': found.poster })
          $('#main-video-title').text(found.title)
          $('#main-video-description').text(found.description)
        } else {
          rabbitHoleVideos.push(videos[ i ])
        }
      }
      // Change html elements to reflect found video
      let [ rabbitHole, a ] = [ $('.rabbit-holes'), 1 ]
      rabbitHole.html('')
      rabbitHoleVideos.forEach(function (video, i) {
        let videoHtml =
          '<video id=\'' + 'rabbit-hole-vids-' + a + '\' class=\'rabbit-hole-vids\' controls' +
          ' muted' + ' ' +
          'poster=\'' + rabbitHoleVideos[ i ].poster + '\'' +
          'title=\'' + rabbitHoleVideos[ i ].title + '\'' +
          'src=\'' + rabbitHoleVideos[ i ].src + '\'' +
          'width=\'' + rabbitHoleVideos[ i ].width + '\'' +
          'height=\'' + rabbitHoleVideos[ i ].height + '\'' +
          'Sorry, your browser doesn/\'t support embedded videos.' +
          ' </video>'
        rabbitHole.append(videoHtml)
        let titleHtml =
          '<p id=rabbit-hole-vid-' + a + '-title class=rabbit-hole-titles>' + rabbitHoleVideos[ i ].title + '</p>'
        rabbitHole.append(titleHtml)
        a++
      })
    },
    error: function (error) {
      console.log('%cAJAX GET Videos Failed: ' + error, 'color: red')
    }
  })
  // Get Comments
  $.ajax({
    type: 'GET',
    url: 'models/get_comment.php',
    success: function (response) {
      const comments = JSON.parse(response)
      $('#user-comments, #db-comments').empty()
      // for loop to diSplay new comments based on clicked video
      for (let i = 0; i < comments.length; i++) {
        $('#db-comments').prepend('<br>' + 'Username: ' + comments[ i ].author + '<br>' + 'Date: ' + comments[ i ].dateposted + '<br>' + 'Comment: ' + comments[ i ].comment + '<br>')
        $('#search-bar').val('')
      }
    },
    // On Failure
    error: function (error) {
      console.log('%cAJAX GET Comments Failed: ' + error, 'color: red')
    }
  })
}
