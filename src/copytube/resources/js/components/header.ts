import Loading from "./loading";

const Header = (function () {
  const Methods = (function () {})();

  const Handlers = (function () {
    document.addEventListener('DOMContentLoaded', () => {
      document.querySelector("header img.profile-picture").addEventListener("click", function (event) {
        document.querySelector("header div.gear-dropdown").classList.toggle("hide");
      });

      document.querySelector("#delete-account-trigger").addEventListener("click", async function () {
        const confirmation = confirm("Are you sure?");
        if (confirmation) {
          Loading(true);
          await fetch("/user", {
            headers: {
              "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
            },
            method: "DELETE",
          });
          window.location.href = "/register";
        }
      });
    });
  })();

  return {
    Methods: Methods,
  };
})();
