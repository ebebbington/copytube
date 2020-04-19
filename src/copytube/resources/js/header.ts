import Loading from "./loading";

const Header = (function () {

    const Methods = (function () {

    })()

    const Handlers = (function () {

        $(document).ready(function () {

            $('header img.profile-picture').on('click', function (event) {
                $('header div.gear-dropdown').toggleClass('hide')
            })

            $('#delete-account-trigger').on('click', function () {
                const confirmation = confirm('Are you sure?')
                if (confirmation) {
                    Loading(true)
                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        url: '/user',
                        method: 'DELETE',
                        success: function () {
                            window.location.href = '/register'
                        }
                    })
                }
            })

        })

    })()

    return {
        Methods: Methods
    }

})()
