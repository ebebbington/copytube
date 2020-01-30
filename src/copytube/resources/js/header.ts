const Header = (function () {
    const Methods = (function () {

    })()
    const Handlers = (function () {
        $(document).ready(function () {
            $('header i.gear').on('click', function (event) {
                $('header > i.gear + div').toggleClass('hide')
            })
        })
    })()
    return {
        Methods: Methods
    }
})()