$(document).ready(function(){
	//getting the users name by asking for an input and saving this to use for comments later
	var username = prompt("Please enter your username below or result to 'ALT+F4'...");

	//If the user ignores this and presses cancel (which equals null) or types nothing and clicks ok then window will close

	if (username == null || username == ""){
		var imsorry = "0";
		var left = "10000";
		while (imsorry != "10000") {
			imsorry += 1;
			left -= 1;
			alert("This alert will pop up " + left + "more times. Suggestion: close the tab.");
			console.log("Alerts: " + imsorry + "/" + "10000");
			console.log("Alerts left: " + left);
		}
	}

	//generates the welcome message with the users username
	$('#welcome').text("Hello " + username + ", and welcome to CopyTube, where you will find a plagurised version of YouTube");

	//object array for videos
	var arr = [{
		name: 'Big Buck Bunner Trailer (2018)',
		src: 'http://dl3.webmfiles.org/big-buck-bunny_trailer.webm',
		height: '220',
		width: '230',
		description: "This tells a story of a Bunny, that doesn't have the greatest time. But one thing changes this and shifts the Bunny's life around",
	},
	{
		name: 'An Elephants Dream',
		src: 'http://dl3.webmfiles.org/elephants-dream.webm',
		height: '220',
		width: '230',
		description: "A great animation - I don't really know what else to say about it",
	},
	{
		name: 'Lego Display',
		src: 'http://techslides.com/demos/sample-videos/small.mp4',
		height: '220',
		width: '230',
		description: "It's a bird! It's a plane! Its... Lego?",
	}];

	//pre-defining description & title and assigning these to the id for display
	var main_vid_title = arr[0].name;
	$('#main-video-title').text(main_vid_title);
	var main_vid_description = arr[0].description;
	$('#main-video-description').text(main_vid_description);
	var rabbit_hole_vid_1_title = arr[1].name;
	$('#rabbit-hole-vid-1-title').text(rabbit_hole_vid_1_title);
	var rabbit_hole_vid_2_title = arr[2].name;
	$('#rabbit-hole-vid-2-title').text(rabbit_hole_vid_2_title);

	//when the add comment button is clicked
	$('#comment-button').on('click', function(){

		var description = $('#comment-bar').val();
		var max_length = 400;
		if (description == ""){
			alert("Please input a comment");
		} else {
			if (description.length > max_length){
				alert("Comment exceeds maximum characters, maximum characters is 400 and your comment is " + description.length + " characters long");
			} else {
				//setting "today" to equal todays date
				var today = new Date();
				var dd = today.getDate();
				var mm = today.getMonth();
				mm += 1; //some reason, month was 1 behind
				var yyyy = today.getFullYear();
				//today = dd + '/' + mm + '/' + yyyy;
				today = yyyy + "-" + mm + "-" + dd;

				//combing these variables into one variable to concatenate them and display them in order
				var actualcomment = '<br>' + '<br>' + "Username: " + username + "<br>" + "Date: " + today + "<br>" + "Comment: " + description + "<br>";
				//assign above variable to the id for the div to display
				$('#user-comments').prepend(actualcomment);
				//clear comment text bar
				$('#comment-bar').val("");

					//start of setting up ajax request by setting url to go to and data
				$.ajax({
				type: "POST",
					url: "models/savecomment.php",
					data: {
						author: username,
						comment: description,
						dateposted: today
					},
					//if working or if not
					success: function(response){
						console.log('AJAX save-comment Response: AJAX request has followed through.');
					},
					error: function(err) {
						console.log('AJAX save-comment Response: ERROR - Request for AJAX has not passed.');
					}
				}); //I can use "var_dump($_[typename])" to get props in network response which i an then do "var_dump($_POST[author])" to get value of this property
			}
		}

	})

	//when a rabbit hole video is clicked
	$(document).on('click', '.rabbit-hole-vid',function(){

		//creating varibles for titles and description
		var clicked_vid_title = $(this).prop('title');
		var clicked_vid_description = $(this).prop('description'); //doesnt work, add in description in video props and test again
		var main_vid_title = $('#main-video-title').text();

		//creating variables for the main and clicked video source
		var main_vid_src = $('#main-video').prop('src');
		var clicked_vid_src = this.currentSrc;

		//setting titles and descriptions
		$('#main-video-title').text(clicked_vid_title);
		$('#main-video-description').text(clicked_vid_description); //doesnt work, fix variable first then this will work
		$(this).prop('title', main_vid_title); //doesnt work, displays ALL of 'this' (such as using: console.log($(this))) and not the title i.e. gets all of the props and not just the title

		//setting main and clicked video source
		$('#main-video').prop('src', clicked_vid_src);
		$(this).prop('src', main_vid_src);
	})
})