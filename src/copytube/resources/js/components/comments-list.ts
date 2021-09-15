import Realtime from "./realtime";
import Notifier from "./notifier";
import Loading from "./loading";

const Commentslist = (function () {
  const Methods = (function () {})();

  const Handlers = (function () {
    document.addEventListener("DOMContentLoaded", () => {
      //@ts-ignore
      Realtime.handleUserDeleted = function (message: {
        channel: string;
        type: string;
        userId: number;
      }) {
        const allComments = document.querySelectorAll(
          '#comment-list .media[data-user-id="' + message.userId + '"]'
        );
        if (!allComments.length) return false;
        allComments.forEach(comment => comment.remove())
      };

      //@ts-ignore
      Realtime.handleNewVideoComment = function (message) {
        if (
          document.querySelector("#main-video-holder > h2").textContent !==
          message.comment.video_posted_on
        )
          return false;
        const newCommentHtml = document.querySelector<HTMLDivElement>(
          "#templates > #user-comment-template"
        ).cloneNode(true) as HTMLDivElement;
        newCommentHtml.setAttribute("id", "");
        const [year, month, day] = message.comment.date_posted.split("-");
        const formattedDate = day + "/" + month + "/" + year;
        newCommentHtml.setAttribute("data-user-id", message.comment.user_id);
        newCommentHtml.children[1].children[1].textContent = formattedDate;
        newCommentHtml.children[1].children[2].textContent =
          message.comment.comment;
        (newCommentHtml.children[0].children[0] as HTMLImageElement).src =
          message.comment.profile_picture;
        newCommentHtml.children[1].children[0].textContent =
          message.comment.author;
        newCommentHtml
          .querySelector("span.ml-4.delete-comment")
          .setAttribute("data-comment-id", message.comment.id);
        newCommentHtml
          .querySelector("span.ml-4.edit-comment")
          .setAttribute("data-comment-id", message.comment.id);
        // TODO set comment id for edit and delete icon

        document.querySelector("#comment-list").prepend(newCommentHtml);
      };

      const deleteComment = document.querySelector("#comment-list .media > span.delete-comment")
      if (!deleteComment) {
        return false;
      }
      deleteComment.addEventListener(
        "click",
        async function (event) {
          const wantsToDelete = confirm(
            "Are you sure you want to delete this comment?"
          );
          if (!wantsToDelete) {
            return false;
          }
          const deleteElem = event.target as HTMLDivElement;
          const commentId = deleteElem.getAttribute("data-comment-id");
          if (!commentId) {
            return false;
          }
          Loading(true);
          const res = await fetch("/video/comment?id=" + commentId, {
            method: "DELETE",
            headers: {
              "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
            },
          })
          const data = await res.json()
          Loading(false);
          if (!data.success) {
            console.log(data)
            return false;
          }
          console.log(data);
          Loading(false);
          if (data.success === false) {
            Notifier.error("Delete comment", data.message);
          } else {
            Notifier.success("Delete comment", data.message);
            deleteElem.closest(".media").remove();
          }
        }
      );

      const edit = document.querySelector("#comment-list .media > span.edit-comment")
      if (!edit) {
        return;
      }
      edit.addEventListener('click',
        async (event) => {
          const target = (event.target as HTMLSpanElement)
          const container = target.parentElement;
          const comment = container.querySelector<HTMLParagraphElement>('.media-body > p');
          if (comment.getAttribute("contenteditable")) {
            comment.setAttribute("contenteditable", "false");
            const id = target.getAttribute("data-comment-id");
            const newComment = comment.textContent;
            // send post
            Loading(true);
            const res = await fetch("/video/comment", {
              method: "PUT",
              headers: {
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
                "Content-Type": "application/json",
                "Accept": "application/json"
              },
              body: JSON.stringify({
                id: id,
                newComment: newComment,
              })
            })
            const data = await res.json()
            Loading(false);
            if (data.success) {
              Notifier.success("Update", data.message);
            } else {
              Notifier.error("Update", data.message);
            }
            console.error(data);
          } else {
            comment.setAttribute("contenteditable", "true");
            comment.focus();
          }
        }
      );
    });
  })();

  return {};
})();
