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

        return {
            handleScroll: handleScroll
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

            $('.rabbit-holde-video-holder > video').on('click', function (event: any) {
                // Make this the main video
            })

            $('#comment textarea').on('keyup', function (event: any) {
                const comment = event.target.value
                const length = comment.length
                $('#comment > span > p').text(length)
            })

            $('#comment > button').on('click', function (event: any) {
                const comment = $('#comment > span > textarea').val()
                const datePosted = ''
                // ajax
                const newComment: any = $('#templates > #user-comment-template').clone()
                newComment.attr('id', '')
                newComment.children[0].children.src = 'path to users image'
                newComment.children[1].children[0].text('Users username')
                newComment.children[1].children[1].text('date posted')
                newComment.children[1].children[2].text(comment)

                // add the comment to db and use the template in layout.blade to display in the UI
            })

        })
    })()

    return {
        Methods: Methods,
    }

})()