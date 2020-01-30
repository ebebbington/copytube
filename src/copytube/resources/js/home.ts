import Notifier from './notifier'

const Home = (function () {

    const Methods = (function () {

        function handleScroll (elem: any, top: any) {
            console.log('you scrolled!')
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

        function postComment (comment: string, date: string, videoPostedOn: string, newCommentHtml: any) {
            console.log('comment: ' + comment)
            console.log('date: ' + date)
            console.log(videoPostedOn)
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
                    console.log('success')
                    console.log(data)
                    if (data.success) {
                        Notifier.success('Add Comment', 'Success')
                        //newCommentHtml[0].children[0].children[0].src = data.data.image
                        newCommentHtml[0].children[0].children[0].src = 'img/lava_sample.jpg'
                        newCommentHtml[0].children[1].children[0].textContent = data.data
                        $('#comment-list').prepend(newCommentHtml)
                    }
                },
                error: function (err) {
                    console.log('error')
                    Notifier.success('Add Comment', 'Failed')
                    console.error(err)
                    console.log(JSON.parse(err.responseText))
                }
            })
        }

        return {
            handleScroll: handleScroll,
            getCurrentDate: getCurrentDate,
            postComment: postComment
        }

    })()

    const Handlers = (function () {

        $(document).ready(function () {

            const searchElem: any = document.getElementById('search')
            const top = searchElem.offsetTop
            window.onscroll = function () { Methods.handleScroll(searchElem, top)}
        
            $('#search-button').on('click', function (event: any) {
                /// search?
            })

            $('.rabbit-hole-video-holder > video').on('click', function (event: any) {
                // Make this the main video
                console.log('clicked rabbuit hole vid')
                const rabbitHoleVideo = $(this)
                const clickedVideoTitle = rabbitHoleVideo.attr('title')
                const form = document.createElement('form')
                form.method = 'GET'
                form.action = '/home'
                const data = document.createElement('input')
                data.name = 'requestedVideo'
                data.value = clickedVideoTitle
                form.appendChild(data)
                document.body.appendChild(form)
                form.submit()
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
                const comment = $('#comment > span > textarea').val()
                const datePosted = Methods.getCurrentDate()
                // ajax
                const newCommentHtml: any = $('#templates > #user-comment-template').clone()
                const videoPostedOn: string = $('#main-video-holder > video').attr('title')
                newCommentHtml.attr('id', '')
                newCommentHtml[0].children[1].children[1].textContent = datePosted
                newCommentHtml[0].children[1].children[2].textContent = comment

                // add the comment to db and use the template in layout.blade to display in the UI
                Methods.postComment(comment, datePosted, videoPostedOn, newCommentHtml)
            })

        })
    })()

    return {
        Methods: Methods,
    }

})()