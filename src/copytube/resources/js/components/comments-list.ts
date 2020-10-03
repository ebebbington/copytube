import Realtime from "./realtime";
import Notifier from "./notifier";
import Loading from "./loading";

const Commentslist = (function () {

    const Methods =  (function () {

    })()

    const Handlers = (function () {

        $(document).ready(function () {

            //@ts-ignore
            Realtime.handleUserDeleted = function (message: { channel: string, type: string, userId: number }) {
                const $allComments = $('#comment-list .media[data-user-id="' + message.userId + '"]')
                if (!$allComments.length)
                    return false
                $allComments.each(function () {
                    $(this).remove()
                })
            }

            //@ts-ignore
            Realtime.handleNewVideoComment = function (message) {
                if ($('#main-video-holder > h2').text() !== message.comment.video_posted_on)
                    return false
                const newCommentHtml: any = $('#templates > #user-comment-template').clone()
                newCommentHtml.attr('id', '')
                const [year, month, day] = message.comment.date_posted.split('-')
                const formattedDate = day + '/' + month + '/' + year
                newCommentHtml[0].children[1].children[1].textContent = formattedDate
                newCommentHtml[0].children[1].children[2].textContent = message.comment.comment
                newCommentHtml[0].children[0].children[0].src = message.comment.profile_picture
                newCommentHtml[0].children[1].children[0].textContent = message.comment.author
                newCommentHtml.find('i.delete-comment').data('comment-id', message.comment.id)
                newCommentHtml.find('i.edit-comment').data('comment-id', message.comment.id)
                // TODO set comment id for edit and delete icon

                $('#comment-list').prepend(newCommentHtml)
            }

            $('body').on('click', '#comment-list .media > i.delete-comment', function () {
                const wantsToDelete = confirm("Are you sure you want to delete this comment?")
                if (!wantsToDelete) {
                    return false
                }
                const commentId = $(this).attr("data-comment-id")
                if (!commentId) {
                    return false
                }
                Loading(true)
                const $deleteElem = $(this)
                $.ajax({
                    url: "/video/comment?id=" + commentId,
                    method: "DELETE",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    dataType: 'json',
                    success(data: any, textStatus: string, jqXHR: JQueryXHR): any {
                        console.log(data)
                        Loading(false)
                        if (data.success === false) {
                            Notifier.error('Delete comment',  data.message)
                        } else {
                            Notifier.success("Delete comment", data.message)
                            $deleteElem.closest('.media').remove()
                        }
                    },
                    error(jqXHR: JQueryXHR, textStatus: string, errorThrown: string): any {
                        Loading(false)
                        console.log(errorThrown)
                    }
                })
            })

            $('body').on('click', '#comment-list > media > i.edit-comment', function () {
                console.log($(this))
                // TODO
            })
        })

    })()

    return {

    }

})()
