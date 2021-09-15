import Loading from "./loading";
import { getCurrentDate } from "../global";
import Notifier from "./notifier";

const AddComment = (function () {
  const Methods = (function () {
    /**
     * AJAX request to post a comment
     * @param comment
     * @param date
     * @param videoPostedOn
     * @param newCommentHtml
     */
    async function postComment(comment: string, date: string, videoPostedOn: string) {
      const res = await fetch("/video/comment", {
        headers: {
          "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
          "Content-Type": "application/json",
          "Accept": "application/json"
        },
        method: "POST",
        body: JSON.stringify({
          comment: comment,
          datePosted: date,
          videoPostedOn: videoPostedOn,
        })
      })
      const data = await res.json()
      Loading(false);
      if (data.success) {
        Notifier.success("Add Comment", "Success");
        //newCommentHtml[0].children[0].children[0].src = data.data.image
        document.querySelector<HTMLInputElement>("#add-comment-input").value = "";
        document.querySelector("#comment > span > p").textContent = "0";
        return true
      }
      console.log("error");
      Notifier.error("Add Comment", data.message);
    }

    return {
      postComment: postComment,
    };
  })();
  const Handlers = (function () {
    document.addEventListener("DOMContentLoaded", () => {
      document.querySelector("#comment textarea").addEventListener("keyup", function (event) {
        const comment = (event.target as HTMLInputElement).value;
        const length = comment.length;
        if (length > 400) {
          document.querySelector<HTMLParagraphElement>("#comment > span > p").style.color = "red";
        } else {
          document.querySelector<HTMLParagraphElement>("#comment > span > p").style.color = "var(--custom-dark-grey";
        }
        document.querySelector<HTMLParagraphElement>("#comment > span > p").textContent = "length";
      });

      document.querySelector("#comment > button").addEventListener("click", function () {
        Loading(true);
        const comment = document.querySelector<HTMLTextAreaElement>("#comment > span > textarea").value.toString();
        const datePosted = getCurrentDate();
        // ajax
        const videoPostedOn: string = document.querySelector("#main-video-holder > video").getAttribute(
          "title"
        );

        // add the comment to db and use the template in layout.blade to display in the UI
        Methods.postComment(comment, datePosted, videoPostedOn);
      });
    });
  })();
  return {};
})();
