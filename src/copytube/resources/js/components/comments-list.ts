import Realtime from "./realtime";

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
                $('#comment-list').prepend(newCommentHtml)
            }
        })

    })()

    return {

    }

})()
