$(document).ready(function(){

	//getting the users name by asking for an input and saving this to use for comments later
	var username = prompt("Please Enter Your Username Below or You Cannot use This Web Page", "Remove me when you aren't refreshing the page a million times");

	//If the user ignores this and presses cancel (which equals null) or types nothing and clicks ok then window will close
	if (username == null){
		alert("I warned you.");
		close();
	}
	if (username == ""){
		alert("I warned you.");
		close();
	}

	//generates the welcome message with the users username
	$('#welcome').text("Hello " + username + ", and welcome to CopyTube, where you will find a plagurised version of YouTube");

	//object array for videos
	var arr = [{
		name: 'Big Buck Bunner Trailer (2018)',
		src: 'http://dl3.webmfiles.org/big-buck-bunny_trailer.webm',
		height: '200',
		width: '210',
		description: "This tells a story of a Bunny, that doesn't have the greatest time. But one thing changes this and shifts the Bunny's life around",
		id: '0'
	},
	{
		name: 'An Elephants Dream',
		src: 'http://dl3.webmfiles.org/elephants-dream.webm',
		height: '200',
		width: '210',
		description: "A great animation - I don't really know what else to say about it",
		id: '1'
	},
	{
		name: 'Lego Display',
		src: 'http://techslides.com/demos/sample-videos/small.mp4',
		height: '200',
		width: '210',
		description: "It's a bird! It's a plane! Its... Lego?",
		id: '2'
	}];

	//pre-defining description & title and assigning these to the id for display
	var main_video_title = arr[0].name;
	$('#main-video-title').text(main_video_title);
	var description = arr[0].description;
	$('#main-video-description').text(description);

	//Array for loop to display rabbit hole videos on load of document
	for(var i=0, l=arr.length; i<l; i++){

		var $container = $('#rabbit-holes');

		var html = '<div id="rabbit-holes col xs-12">' +
						'<video id="2ndrabbithold" class="rabbit-hole-vid" controls muted src="'+ arr[i].src + '" data-array-element="'+i+'" width="210" height="200">' +
						   ' Sorry, your browser doesn\'t support embedded videos.' +
						'</video>' +
						'<p>' + arr[i].name + '</p>'
					'</div>';

		$container.append(html);
	}

	//create array for storing comments and display
	//var commentarray = [,];
	//$('#user-comments').val(commentarray);

	//when the add comment button is clicked
	$('#comment-button').on('click', function(){
		var emptycheck = $('#comment-bar').val();
		if (emptycheck == ""){
			alert("Please input a comment");
		} else {

			//setting "description" to equal value in comment bar
			var description = $('#comment-bar').val();
			//setting "today" to equal todays date
			var today = new Date();
			var dd = today.getDate();
			var mm = today.getMonth();
			mm += 1; //some reason, month was 1 behind
			var yyyy = today.getFullYear();
			today = dd + '/' + mm + '/' + yyyy;
			//setting "time" to equal current time
			var time = new Date();
			var h = time.getHours();
			var m = time.getMinutes();
			if (m < "10"){ //slight problem, any minute below 10 was displayed as 15:1 or 15:8, this fixes it by adding a zero before the minute
				m = "0" + m;
			}
			time = h + ":" + m;
			//combing these variables into one variable to concatenate them and display them in order
			var actualcomment = '<br>' + '<p>' + "Author: " + username + '</p>' + '<p>' + "Date: " + today + '</p>' + '<p>' + "Time: " + time + '</p>' + '<p>' + description + '</p>';
			//assign above variable to the id
			$('#user-comments').append(actualcomment);
			//clear comment text bar
			$('#comment-bar').val("");
		}
	})

	//when a rabbit hole video is clicked
	$(document).on('click', '.rabbit-hole-vid',function(){

		//setting variables: IDelement, source of main video, source of clicked video
		var array_element = $(this).data('array-element');
		var main_vid_src = $('#main-video').prop('src');
		var clicked_vid_src = this.currentSrc;
		//setting the clicked rabbit hole video  as the main video
		$('.rabbit-hole-vid').prop('src', main_vid_src);
		//setting the main videos as the clicked rabbit hole video
		$('#main-video').prop('src', clicked_vid_src);

		//setting clicked element to a variable
		var clicked_video_element = $(this).data('array-element');
		console.log("Clicked array element on is: %s - obj: %o",clicked_video_element, arr[clicked_video_element].name);
		//variable now equals that elements name
		main_video_title = arr[clicked_video_element].name;
		$('#main-video-title').text(main_video_title);
		console.log("Main video element is: " + clicked_video_element);

		//testing a new way to display title which should be a more efficient way
		console.log($('#main-video').name);
		console.log($('#main-video').prop('name'));
	})

	//Another way to display rabbit hole videos (commented out)
		//To replace a rabbit hole video with the main video, use this:
		//$('.[vid clas name]').prop('src', videotoreplace);
		//To replace main video with a rabbit hole video, use this:
		//$('#main-video').prop('src', src);
		//this is because "src" is a variable and has already grabbed the source from the clicked video so you are setting the source of main vid to this source
})