const Loading = function (isLoading: boolean, elemToHalt?: any) {
  const overlayElement = document.getElementById("overlay-container");
  const loadingElement = document.getElementById("loading-container");
  if (isLoading) {
    overlayElement.style.visibility = "visible";
    loadingElement.style.visibility = "visible";
    if (elemToHalt) elemToHalt.disabled = true;
    $("#loading-circle").css("animation", "pulse 1.5s infinite");
    // $(".loading-circles#loading-circle-one").css(
    //     "animation",
    //     "pulse 1.5s infinite"
    // );
    // $(".loading-circles#loading-circle-two").css(
    //     "animation",
    //     "pulse 1.5s infinite 0.2s"
    // );
    // $(".loading-circles#loading-circle-four").css(
    //     "animation",
    //     "pulse 1.5s infinite .4s"
    // );
    // $(".loading-circles#loading-circle-five").css(
    //     "animation",
    //     "pulse 1.5s infinite .8s"
    // );
    // $(".loading-circles#loading-circle-three").css(
    //     "animation",
    //     "pulse 1.5s infinite 1.2s"
    // );
  }

  if (!isLoading) {
    overlayElement.style.visibility = "hidden";
    loadingElement.style.visibility = "hidden";
    if (elemToHalt) elemToHalt.disabled = false;
    $(".loading-circles").css("animation", "");
  }
};

export default Loading;
