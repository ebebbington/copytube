import Loading from "./loading";

const Home = (function () {
  const Methods = (function () {
    /**
     * Handler for scrolling and the search bar
     * @param elem
     * @param top
     */
    function handleScroll(elem: HTMLElement, top: number): void {
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
      const searchElem = document.getElementById("search");
      if (searchElem && typeof searchElem.offsetTop === "number") {
        const top = searchElem.offsetTop;
        window.onscroll = function () {
          Methods.handleScroll(searchElem, top);
        };
      }

      const searchBar = document.querySelector("#search-bar");
      if (searchBar) {
        searchBar.addEventListener("keyup", async function (event) {
          const value = (event.target as HTMLInputElement).value;
          console.log(value);
          const dropdown = document.querySelector<HTMLUListElement>(
            "#search-bar-matching-dropdown"
          );
          const items = dropdown.querySelectorAll("li");
          items.forEach((item) => item.remove());
          if (!value) {
            return;
          }
          dropdown.append("<li>Loading...</li>");
          const res = await fetch("/video/titles?title=" + value);
          const data = await res.json();
          console.log(data);
          if (data.success) {
            const matchingTitles = data.data;
            items.forEach((item) => item.remove());
            matchingTitles.forEach((element: string) => {
              dropdown.append("<li>" + element + "</li>");
            });
          }
        });

        const items = document.querySelectorAll<HTMLLIElement>(
          "#search-bar-matching-dropdown li"
        );
        items.forEach((item) =>
          item.addEventListener("click", function (event) {
            console.log("clicked a video dropdown title");
            const title = (event.target as unknown as { textContent: string })
              .textContent;
            Methods.requestVideo(title);
          })
        );

        const search = document.querySelector("#search-button");
        if (search) {
          search.addEventListener("click", function () {
            const videoTitle = document
              .querySelector<HTMLInputElement>("#search-bar")
              .value.toString();
            Methods.requestVideo(videoTitle);
          });
        }
      }
    });
  })();
})();
