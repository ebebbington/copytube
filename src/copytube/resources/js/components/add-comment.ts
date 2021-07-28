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
    function postComment(comment: string, date: string, videoPostedOn: string) {
      $.ajax({
        headers: {
          "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        url: "/video/comment",
        method: "POST",
        data: {
          comment: comment,
          datePosted: date,
          videoPostedOn: videoPostedOn,
        },
        success: function (data) {
          if (data.success) {
            Notifier.success("Add Comment", "Success");
            //newCommentHtml[0].children[0].children[0].src = data.data.image
            $("#add-comment-input").val("");
            $("#comment > span > p").text("0");
          }
          Loading(false);
        },
        error: function (err) {
          console.log("error");
          //@ts-ignore
          Notifier.error("Add Comment", JSON.parse(err.responseText).message);
          console.error(err);
          console.log(JSON.parse(err.responseText));
          Loading(false);
        },
      });
    }

    return {
      postComment: postComment,
    };
  })();
  const Handlers = (function () {
    $(document).ready(function () {
      $("#comment textarea").on("keyup", function (event: any) {
        const comment = event.target.value;
        const length = comment.length;
        if (length > 400) {
          $("#comment > span > p").css("color", "red");
        } else {
          $("#comment > span > p").css("color", "var(--custom-dark-grey");
        }
        $("#comment > span > p").text(length);
      });

      $("#comment > button").on("click", function (event: any) {
        Loading(true);
        const comment = $("#comment > span > textarea").val();
        const datePosted = getCurrentDate();
        // ajax
        const videoPostedOn: string = $("#main-video-holder > video").attr(
          "title"
        );

        // add the comment to db and use the template in layout.blade to display in the UI
        Methods.postComment(comment, datePosted, videoPostedOn);
      });
    });
  })();
  return {};
})();
