import Realtime from "./realtime";
import Notifier from "./notifier";
import Loading from "./loading";

let xhr: JQueryXHR;

const Home = (function () {
  const Methods = (function () {
    /**
     * Handler for scrolling and the search bar
     * @param elem
     * @param top
     */
    function handleScroll(elem: any, top: any): void {
      if (window.pageYOffset > top) {
        elem.classList.add("stick");
      } else {
        elem.classList.remove("stick");
      }
    }

    function requestVideo(videoTitle: string) {
      Loading(true);
      const form = document.createElement("form");
      form.method = "GET";
      form.action = "/video";
      const data = document.createElement("input");
      data.name = "requestedVideo";
      data.value = videoTitle;
      form.appendChild(data);
      document.body.appendChild(form);
      form.submit();
    }

    return {
      handleScroll: handleScroll,
      requestVideo: requestVideo,
    };
  })();

  const Handlers = (function () {
    document.addEventListener("DOMContentLoaded", () => {
      const searchElem: any = document.getElementById("search");
      if (searchElem && typeof searchElem.offsetTop === "number") {
        const top = searchElem.offsetTop;
        window.onscroll = function () {
          Methods.handleScroll(searchElem, top);
        };
      }

      document
        .querySelector("#search-bar")
        .addEventListener("keyup", function (event: any) {
          const value = event.target.value;
          console.log(value);
          const dropdown = $("#search-bar-matching-dropdown");
          dropdown.empty();
          if (!value) {
            return;
          }
          dropdown.append("<li>Loading...</li>");
          if (xhr && xhr.readyState !== 4) {
            xhr.abort();
          }
          xhr = $.ajax({
            url: "/video/titles?title=" + value,
            method: "GET",
            dataType: "json",
            success: function (data) {
              console.log(data);
              if (data.success) {
                const matchingTitles = data.data;
                dropdown.empty();
                matchingTitles.forEach((element: string) => {
                  dropdown.append("<li>" + element + "</li>");
                });
              }
            },
            error: function (err) {
              if (err.statusText !== "abort") {
                console.error(err);
              }
            },
          });
        });

      $("#search-bar-matching-dropdown").on("click", "li", function (event) {
        console.log("clicked a video dropdown title");
        console.log($(this).text());
        const title = $(this).text();
        Methods.requestVideo(title);
      });

      document
        .querySelector("#search-button")
        .addEventListener("click", function (event: any) {
          const videoTitle = document
            .querySelector<HTMLInputElement>("#search-bar")
            .value.toString();
          Methods.requestVideo(videoTitle);
        });
    });
  })();
})();
