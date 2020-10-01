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
                elem.classList.add('stick')
            } else {
                elem.classList.remove('stick')
            }
        }

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
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: '/video/comment',
                method: 'POST',
                data: {
                    comment: comment,
                    datePosted: date,
                    videoPostedOn: videoPostedOn
                },
                success: function (data) {
                    if (data.success) {
                        Notifier.success('Add Comment', 'Success')
                        //newCommentHtml[0].children[0].children[0].src = data.data.image
                        $('#add-comment-input').val('')
                        $('#comment > span > p').text('0')
                    }
                    Loading(false)
                },
                error: function (err) {
                    console.log('error')
                    //@ts-ignore
                    Notifier.error('Add Comment', JSON.parse(err.responseText).message)
                    console.error(err)
                    console.log(JSON.parse(err.responseText))
                    Loading(false)
                }
            })
        }

        function requestVideo(videoTitle: string) {
            Loading(true)
            const form = document.createElement('form')
            form.method = 'GET'
            form.action = '/home'
            const data = document.createElement('input')
            data.name = 'requestedVideo'
            data.value = videoTitle
            form.appendChild(data)
            document.body.appendChild(form)
            form.submit()
        }

        return {
            handleScroll: handleScroll,
            postComment: postComment,
            requestVideo: requestVideo
        }

    })()

    const Handlers = (function () {

        $(document).ready(function () {

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

            //@ts-ignore
            Realtime.handleUserDeleted = function (message: { channel: string, type: string, userId: number }) {
                const $allComments = $('#comment-list .media[data-user-id="' + message.userId + '"]')
                if (!$allComments.length)
                    return false
                $allComments.each(function () {
                    $(this).remove()
                })
            }

            const searchElem: any = document.getElementById('search')
            if (searchElem && typeof searchElem.offsetTop === 'number') {
                const top = searchElem.offsetTop
                window.onscroll = function () {
                    Methods.handleScroll(searchElem, top)
                }
            }

            $('#search-bar').on('keyup', function (event: any) {
                const value = event.target.value
                console.log(value)
                const dropdown = $('#search-bar-matching-dropdown')
                dropdown.empty()
                if (!value) {
                    return
                }
                dropdown.append('<li>Loading...</li>')
                if(xhr && xhr.readyState !== 4){
                    xhr.abort();
                }
                xhr = $.ajax({
                    url: '/video?title=' + value,
                    method: 'GET',
                    dataType: 'json',
                    success: function (data) {
                        console.log(data)
                        if (data.success) {
                            const matchingTitles = data.data
                            dropdown.empty()
                            matchingTitles.forEach((element: string) => {
                                dropdown.append('<li>' + element + '</li>')
                            });
                        }
                    },
                    error: function (err) {
                        if (err.statusText !== "abort") {
                            console.error(err)
                        }
                    }
                })
            })

            $('#search-bar-matching-dropdown').on('click', 'li', function (event) {
                console.log('clicked a video dropdown title')
                console.log($(this).text())
                const title = $(this).text()
                Methods.requestVideo(title)
            })

            $('#search-button').on('click', function (event: any) {
                const videoTitle = $('#search-bar').val()
                Methods.requestVideo(videoTitle)
            })

        })
    })()
})()
