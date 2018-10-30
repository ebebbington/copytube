$(document).ready(function(){

	//region Getting Username, Validation & Display Welcome Message
	//GET USERNAME, ENCODE & REPAIR
    function getusername() {
        username = encodeURI(prompt("Please enter your username below"));
        var count = username.split('%20');
        var i = 0;
        while (i != count.length) {
            i++;
            username = username.replace("%20", " ");
        }
        //VALIDATION
        if (username.length > 80 || username == "null" || (jQuery.trim(username)).length==0){
            alert("Please enter an appropriate username between 0 and 81 characters long");
            getusername();
        }
    }
    //RUN FUNCTION
    getusername();

	//generates the welcome message with the username
	$('#welcome').text("Hello " + username + ", and welcome to CopyTube, where you will find a plagiarised version of YouTube");
	//endregion

	//region Object Array for Videos
	var arr = [{
		title: 'Something More',
		src: 'http://mazwai.com/system/posts/videos/000/000/191/original/something-more.mp4?1445788608',
		height: '220',
		width: '230',
		description: "Watch this inspirational video as we look at all of the beautiful things inside this world",
		poster: "imageresources/something_more.jpg",
	},
	{
		title: 'Lava Sample',
		src: 'https://upload.wikimedia.org/wikipedia/commons/transcoded/2/22/Volcano_Lava_Sample.webm/Volcano_Lava_Sample.webm.360p.webm',
		height: '220',
		width: '230',
		description: "Watch this lava flow through the earth, burning and sizzling as it progresses",
		poster: "imageresources/lava_sample.jpg",
	},
	{
		title: 'An Iceland Venture',
		src: 'http://mazwai.com/system/posts/videos/000/000/229/original/omote_iceland__an_iceland_venture.mp4?1528050680',
		height: '220',
		width: '230',
		description: "Iceland, beautiful and static, watch as we venture through this glorious place",
		poster: "imageresources/an_iceland_venture.jpg",
	}];
	//endregion

	//region Counter for Characters While User Types a New Comment
	$('#comment-count').text("0");
	$(document).on('keyup', '#comment-bar',function(){
		var string = $('#comment-bar').val();
		var count = string.length;
		$('#comment-count').text(count);
    })
	//endregion

    //region //ToDo: Removing Drop-down for Search in Prep for Auto-complete
    /*var drop_down = true;
    $(document).on('keyup', '#search-bar',function(){
        disable drop-down elements
        drop_down = false;
        if (drop_down == false){
            $('.dropdown-content').prop('textContent', "");
        }
    })*/
    //endregion

	//region On Click of Add Comment Button
	$('#comment-button').on('click', function(){

	    //region ENCODING, REPAIRING & VALIDATING
        var description = encodeURI($('#comment-bar').val());
		var count = description.split('%20');
		var i = 0;
		while (i != count.length) {
            i++;
            description = description.replace("%20", " ");
        }
		var max_length = 400;
		if (description == "" || description.length > max_length || (jQuery.trim(description)).length==0){
			alert("Please input a comment and have it be less than 401 characters long");
			$('#comment-bar').val("");
			$('#comment-count').text("0");
		}
		//endregion

        //region IF comment is OK
		else
		    {
		    //region Setting actualcomment and Displaying
            var today = new Date();
            var dd = today.getDate();
            var mm = today.getMonth();
            mm += 1; //some reason, month was 1 behind
            var yyyy = today.getFullYear();
            today = yyyy + "-" + mm + "-" + dd;

            //Concatenating comment, date and author
            var actualcomment = '<br>' + '<br>' + "Username: " + username + "<br>" + "Date: " + today + "<br>" + "Comment: " + description + "<br>" + "<br>";
            //assign above variable to the id for the div to display
            $('#user-comments').prepend(actualcomment);
            //clear comment-bar and comment-count
            $('#comment-bar').val("");
            $('#comment-count').text("0");
            //endregion

            //region AJAX save-comments Request
            //start of setting up ajax request by setting url to go to and data
            var vid_title = $('#main-video-title').text();
            $.ajax({
                type: "POST",
                url: "models/savecomment.php",
                data: {
                    author: username,
                    comment: description,
                    dateposted: today,
                    videotitle: vid_title
                },
                //if working or if not
                success: function (response) {
                    console.log('%cAJAX Request Completed', 'color: green');
                },
                error: function (err) {
                    console.log('%cAJAX Request Failed', 'color: red');
                }
            }); //I can use "var_dump($_[typename])" to get props in network response which i an then do "var_dump($_POST[author])" to get value of this property
            // endregion
		}
		//endregion
	})
	//endregion

    //region On Click of Rabbit Hole Video
	$(document).on('click', '.rabbit-hole-vid',function(){

        //region AJAX Request: Get Videos
        $.ajax({
            type: "GET",
            url: "models/getvideos.php",
            data: {
                videotitle: $(this).prop('title')
            },
            //region On Success
            success: function (response) {
                console.log('%cAJAX Request Completed', 'color: green');
                //parsing the string from the ajax request into an object
                var videos = JSON.parse(response);
                //Looking For Videos
                var clicked_vid = $(this);
                var rabbit_hole_vids = [];
                var found = null;
                for (var i=0, l=videos.length; i<l; i++){

                    if (clicked_vid.title == videos[i].title){

                        found = videos[i];
                        $('#main-video').prop('title', found.title);
                        $('#main-video').prop('src', found.src);
                        $('#main-video').prop('poster', found.poster);
                        $('#main-video-title').text(found.title);
                        $('#main-video-description').text(found.description);

                    } else{
                        rabbit_hole_vids.push(videos[i]);
                    }
                }
                console.log(found);
                console.log(rabbit_hole_vids);

                //changing rabbit hole elements
                a=1;
                var rabbit_holes = $('.rabbit-holes');
                rabbit_holes.html('');
                rabbit_hole_vids.forEach(function (video, i) {
                    //creating and displaying new video elements
                    var video_html =
                        "<video id='" + "rabbit-hole-vid-" + a + "' class='rabbit-hole-vid' controls" +
                        " muted" + " " +
                        "poster='" + rabbit_vids.poster + "'" +
                        "title='" + rabbit_vids.title + "'" +
                        "src='" + rabbit_vids.src + "'" +
                        "width='" + rabbit_vids.width + "'" +
                        "height='" + rabbit_vids.height + "'" +
                        "Sorry, your browser doesn/'t support embedded videos." +
                        " </video>";
                    rabbit_holes.append(video_html);
                    //creating and displaying new rabbit hole title elements
                    var title_html =
                        "<p id=rabbit-hole-vid-" + a + "-title class=rabbit-hole-titles>" + rabbit_vids.title + "</p>";
                    rabbit_holes.append(title_html);
                    a++;
                });
            },
            //endregion

            //region On Failure
            error: function (err) {
                    console.log('%cAJAX Request Failed', 'color: red');
            }
            //endregion
        });
		//endregion

		//region AJAX Request: Get Comments Relative to Video
        $.ajax({
            type: "GET",
            url: "models/getcomment.php",
            data: {
                videotitle: $(this).prop('title')
            },
            //if working
            success: function (response) {
                console.log('%cAJAX Request Completed', 'color: green');
                //parsing the string from the ajax request into an object
                var obj = JSON.parse(response);
                //clear all comments
                $('#user-comments').empty();
                $('#db-comments').empty();
                //for loop to diSplay new comments based on clicked video
                for (var i = 0; i < obj.length; i++) {
                    $('#db-comments').prepend('<br>' + "Username: " + obj[i].author + "<br>" + "Date: " + obj[i].dateposted + "<br>" + "Comment: " + obj[i].comment + "<br>");
                }},
            //if not working
            error: function (err) {
                console.log('%cAJAX Request Failed', 'color: red');
            }
        });
        //endregion
	})
    //endregion

    //region On Click of Search Button
    $(document).on('click', '#search-button',function(){
        //region Encoding & Validating Input
        var input = encodeURI($('#search-bar').val());
        var count = input.split('%20');
        var i=0;
        while (i != count.length) {
            i++;
            input = input.replace("%20", " ");
        }
        //validation
        if (input == "" || input == " " || (jQuery.trim(input)).length==0) {
            alert("Please input a video title.");
            $('#search-bar').val("");
        }
        //endregion

        //region MAIN VIDEO & COMMENTS
        var complete = false;
        var found = null;
        var rabbit_hole_vids = [];
        var rabbit_hole_titles = [];
        for(var i=0, l=arr.length; i < l; i++){
            //region IF Video is Found
            if((arr[i].title.toLowerCase() === input.toLowerCase()) || arr[i].title.toLowerCase().indexOf(input.toLowerCase()) > -1) {
                found = arr[i];

                //region Displaying Main Video
                $('#main-video').prop('title', found.title);
                $('#main-video').prop('src', found.src);
                $('#main-video').prop('poster', found.poster);
                $('#main-video0').prop('description', found.description);
                $('#main-video-title').text(found.title);
                $('#main-video-description').text(found.description);
                complete = true;
                //endregion

                //region AJAX Request for Getting Comments
                $.ajax({
                    type: "GET",
                    url: "models/getcomment.php",
                    data: {
                        videotitle: $('#main-video').prop('title')
                    },
                    //if working
                    success: function (response) {
                        console.log('AJAX get-comment Response: AJAX request has followed through.');
                        //parsing the string from the ajax request into an object
                        var obj = JSON.parse(response);
                        //clear all comments
                        $('#user-comments').empty();
                        $('#db-comments').empty();
                        //for loop to diSplay new comments based on clicked video
                        for (var i = 0; i < obj.length; i++) {
                            $('#db-comments').prepend('<br>' + "Username: " + obj[i].author + "<br>" + "Date: " + obj[i].dateposted + "<br>" + "Comment: " + obj[i].comment + "<br>");
                        }
                    },
                    //if not working
                    error: function (err) {
                        console.log('AJAX get-comment Response: ERROR - Request for AJAX has not passed.');
                    }
                })
                //endregion

            } else {
                //Pushes array object to this variable every time found != arr[i] so the var below gathers up all unused array objects
                rabbit_hole_vids.push(arr[i]);
                rabbit_hole_titles.push(arr[i].title);
            }
            //endregion
        }
        //endregion

        //region Displaying Rabbit Hole Videos
        if (complete == true){
            a=1;
            var rabbit_holes = $('.rabbit-holes');
            rabbit_holes.html('');
            rabbit_hole_vids.forEach(function (video, i) {
                //creating and displaying new video elements
                var video_html =
                    "<video id='" + "rabbit-hole-vid-" + a + "' class='rabbit-hole-vid' controls" +
                    " muted" + " " +
                    "poster='" + video.poster + "'" +
                    "title='" + video.title + "'" +
                    "src='" + video.src + "'" +
                    "width='" + video.width + "'" +
                    "height='" + video.height + "'" +
                    "Sorry, your browser doesn/'t support embedded videos." +
                    " </video>";
                rabbit_holes.append(video_html);
                //creating and displaying new rabbit hole title elements
                var title_html =
                    "<p id=rabbit-hole-vid-" + a + "-title class=rabbit-hole-titles></p>";
                rabbit_holes.append(title_html);
                a++;

            });
            //setting content for rabbit holes using unused array titles
            $('#rabbit-hole-vid-1-title').text(rabbit_hole_titles[0]);
            $('#rabbit-hole-vid-2-title').text(rabbit_hole_titles[1]);
        } else {
            alert("No video with the title of " + "'" + input + "' has been found.");
        }
        //endregion
        $('#search-bar').val("");
    });
	//endregion
})