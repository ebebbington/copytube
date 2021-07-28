import Loading from "./loading";

const RabbitHole = (function () {
  const Methods = (function () {
    function requestVideo(videoTitle: string) {
      Loading(true);
      window.location.href = "/video?requestedVideo=" + videoTitle;
      // Loading(true)
      // const form = document.createElement('form')
      // form.method = 'GET'
      // form.action = '/video'
      // const data = document.createElement('input')
      // data.name = 'requestedVideo'
      // data.value = videoTitle
      // form.appendChild(data)
      // document.body.appendChild(form)
      // form.submit()
    }

    return {
      requestVideo: requestVideo,
    };
  })();

  const Handlers = (function () {
    $(document).ready(function () {
      $(".rabbit-hole-video-holder > video").on("click", function (event: any) {
        // Make this the main video
        console.log("clicked rabbuit hole vid");
        const rabbitHoleVideo = $(this);
        const clickedVideoTitle = rabbitHoleVideo.attr("title");
        Methods.requestVideo(clickedVideoTitle);
      });
    });
  })();
})();
