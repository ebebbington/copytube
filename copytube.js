$(document).ready(function(){
	//region Getting Username, Validation & Display Welcome Message
	//getting a username by asking for an input and saving this to a variable
	var username = encodeURI(prompt("Please enter your username below or result to closing the tab."));
	while (username.length > 80){
		username = encodeURI(prompt("Please enter a username less than 80 characters long"));
	}

	//If the user ignores this and presses cancel (which equals null) or types nothing and clicks ok then window will close

	if (username == "null" || username == ""){
		var imsorry = "0";
		var left = "10000";
		while (imsorry != "10000") {
			imsorry++;
			left -= 1;
			alert("This alert will pop up " + left + "more times. Suggestion: close the tab.");
			console.log("Alerts left: " + left);
		}
	}
	//generates the welcome message with the users username
	$('#welcome').text("Hello " + username + ", and welcome to CopyTube, where you will find a plagiarised version of YouTube");
	//endregion

	//region Object Array for Videos
	var arr = [{
		title: 'Something More',
		src: 'http://mazwai.com/system/posts/videos/000/000/191/original/something-more.mp4?1445788608',
		height: '220',
		width: '230',
		description: "Watch this inspirational video as we look at all of the beautiful things inside this world",
	},
	{
		title: 'Lava Sample',
		src: 'https://upload.wikimedia.org/wikipedia/commons/transcoded/2/22/Volcano_Lava_Sample.webm/Volcano_Lava_Sample.webm.360p.webm',
		height: '220',
		width: '230',
		description: "Watch this lava flow through the eart, burning and sizzling as it progresses",
	},
	{
		title: 'An Iceland Venture',
		src: 'http://mazwai.com/system/posts/videos/000/000/229/original/omote_iceland__an_iceland_venture.mp4?1528050680',
		height: '220',
		width: '230',
		description: "Iceland, beautiful and static, watch as we venture through this glorious place",
	}];
	//endregion

	//region Pre-define and Display Titles, and Description
	var main_vid_title = arr[0].title;
	$('#main-video-title').text(main_vid_title);
	var main_vid_description = arr[0].description;
	$('#main-video-description').text(main_vid_description);
	var rabbit_hole_vid_1_title = arr[1].title;
	$('#rabbit-hole-vid-1-title').text(rabbit_hole_vid_1_title);
	var rabbit_hole_vid_2_title = arr[2].title;
	$('#rabbit-hole-vid-2-title').text(rabbit_hole_vid_2_title);
	//endregion

	//region When Add Comment Button is Clicked
	$('#comment-button').on('click', function(){

		var description = encodeURI($('#comment-bar').val());
		var max_length = 400;
		if (description == ""){
			alert("Please input a comment");
		} else {
            if (description.length > max_length) {
                alert("Comment exceeds maximum characters, maximum characters is 400 and your comment is " + description.length + " characters long");
            } else {
            	//region Setting actualcomment and Displaying
                //setting "today" to equal todays date
                var today = new Date();
                var dd = today.getDate();
                var mm = today.getMonth();
                mm += 1; //some reason, month was 1 behind
                var yyyy = today.getFullYear();
                today = yyyy + "-" + mm + "-" + dd;

                //combing these variables into one variable to concatenate them and display them in order
                var actualcomment = '<br>' + '<br>' + "Username: " + username + "<br>" + "Date: " + today + "<br>" + "Comment: " + description + "<br>";
                //assign above variable to the id for the div to display
                $('#user-comments').prepend(actualcomment);
                //clear comment text bar
                $('#comment-bar').val("");
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
                        console.log('AJAX save-comment Response: AJAX request has followed through.');
                    },
                    error: function (err) {
                        console.log('AJAX save-comment Response: ERROR - Request for AJAX has not passed.');
                    }
                }); //I can use "var_dump($_[typename])" to get props in network response which i an then do "var_dump($_POST[author])" to get value of this property
                //endregion
            }
        }
	})
	//endregion

    //region When Rabit Hole is Clicked
	$(document).on('click', '.rabbit-hole-vid',function(){

		//region Changing Videos, Titles & Descriptions

		//creating variables for titles and description
		var clicked_vid_title = $(this).prop('title');
		var i = 0;
		while (clicked_vid_title != arr[i].title)
		{
			i++; //This was originally a problem, but solved it by trying to match the description of a clicked video. This works by: matching name of clicked video and finding the description in that object (easy)
		}
		var clicked_vid_description = arr[i].description;
		var main_vid_title = $('#main-video-title').text();

		//creating variables for the main and clicked video source
		var main_vid_src = $('#main-video').prop('src');
		var clicked_vid_src = this.currentSrc;

		//setting titles and descriptions
		$('#main-video-title').text(clicked_vid_title);
		$('#main-video-description').text(clicked_vid_description);
        $(this).prop('title', main_vid_title);
        if ($(this).prop('id') == "rabbit-hole-vid-1")
		{
			$('#rabbit-hole-vid-1-title').text(main_vid_title);
        } else {
            $('#rabbit-hole-vid-2-title').text(main_vid_title);
        }

		//setting main and clicked video source
		$('#main-video').prop('src', clicked_vid_src);
		$(this).prop('src', main_vid_src);

		//endregion

		//region AJAX Request to get comments based on clicked video
        $.ajax({
            type: "GET",
            url: "models/getcomment.php",
            data: {
				videotitle: clicked_vid_title
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
        });
        //endregion
	})
    //endregion
})