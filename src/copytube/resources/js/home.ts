import Notifier from './notifier'
import Loading from "./loading";
import Realtime from "./realtime";

const Home = (function () {

    const Methods = (function () {

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

        /**
         * Handler for scrolling and the search bar
         * @param elem
         * @param top
         */
        function handleScroll (elem: any, top: any): void {
            if (window.pageYOffset > top) {
                elem.classList.add('stick')
            } else {
                elem.classList.remove('stick')
            }
        }

        /**
         * format: yyyy-mm-dd
         */
        function getCurrentDate () {
            const today: Date = new Date()
            const year = today.getFullYear()
            const month = (today.getMonth() + 1) > 9 ? today.getMonth() + 1 : '0' + (today.getMonth() + 1)
            const day = today.getDate()
            const date: string = year + '-' + month + '-' + day
            return date
        }

        /**
         * AJAX request to post a comment
         * @param comment
         * @param date
         * @param videoPostedOn
         * @param newCommentHtml
         */
        function postComment (comment: string, date: string, videoPostedOn: string) {
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
                    Notifier.error('Add Comment', 'Failed')
                    console.error(err)
                    console.log(JSON.parse(err.responseText))
                    Loading(false)
                }
            })
        }

        function requestVideo (videoTitle: string) {
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
            getCurrentDate: getCurrentDate,
            postComment: postComment,
            requestVideo: requestVideo
        }

    })()

    const Handlers = (function () {

        $(document).ready(function () {

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
                dropdown.append('<li>Loading...</li>')
                $.ajax({
                    url: '/video',
                    data: {
                        title: value
                    },
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
                        console.error(err)
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

            $('.rabbit-hole-video-holder > video').on('click', function (event: any) {
                // Make this the main video
                console.log('clicked rabbuit hole vid')
                const rabbitHoleVideo = $(this)
                const clickedVideoTitle = rabbitHoleVideo.attr('title')
                Methods.requestVideo(clickedVideoTitle)
            })

            $('#comment textarea').on('keyup', function (event: any) {
                const comment = event.target.value
                const length = comment.length
                if (length > 400) {
                    $('#comment > span > p').css('color', 'red')
                } else {
                    $('#comment > span > p').css('color', 'var(--custom-dark-grey')
                }
                $('#comment > span > p').text(length)
            })

            $('#comment > button').on('click', function (event: any) {
                Loading(true)
                const comment = $('#comment > span > textarea').val()
                const datePosted = Methods.getCurrentDate()
                // ajax
                const videoPostedOn: string = $('#main-video-holder > video').attr('title')

                // add the comment to db and use the template in layout.blade to display in the UI
                Methods.postComment(comment, datePosted, videoPostedOn)
            })

        })
    })()

    return {
        Methods: Methods,
    }

})()
