const Header = (function () {

    const Methods = (function () {

    })()

    const Handlers = (function () {

        $(document).ready(function () {

            $('header img.profile-picture').on('click', function (event) {
                $('header div.gear-dropdown').toggleClass('hide')
            })

        })

    })()

    return {
        Methods: Methods
    }

})()
