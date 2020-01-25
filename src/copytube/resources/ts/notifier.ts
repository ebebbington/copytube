const Notifier = (function () {

    const classNames = [
        'success',
        'warning',
        'error'
    ]

    const FADE_OUT_DELAY = 4000

    function success (title: string, message: string) {
        if (title && message) {
            $('#notifier-container').css('display', 'block')
            $('#notifier-container').removeClass('error warning')
            $('#notifier-container').addClass('success')
            $('#notifier-container > p#notifier-title').text(title)
            $('#notifier-container > p#notifier-message').text(message)
            setTimeout(() => {
                $('#notifier-container').removeClass('error warning success')
            }, FADE_OUT_DELAY)
        }
    }
    function warning (title: string, message: string) {
        if (title && message) {
            $('#notifier-container').css('display', 'block')
            $('#notifier-container').removeClass('error success')
            $('#notifier-container').addClass('warning')
            $('#notifier-container > p#notifier-title').text(title)
            $('#notifier-container > p#notifier-message').text(message)
            setTimeout(() => {
                $('#notifier-container').removeClass('error warning success')
            }, FADE_OUT_DELAY)
        }
    }
    function error (title: string, message: string) {
        if (title && error) {
            $('#notifier-container').css('display', 'block')
            $('#notifier-container').removeClass('success warning')
            $('#notifier-container').addClass('error')
            $('#notifier-container > p#notifier-title').text(title)
            $('#notifier-container > p#notifier-message').text(message)
            setTimeout(() => {
                $('#notifier-container').removeClass('error warning success')
            }, FADE_OUT_DELAY)
        }
    }
    return {
        success: success,
        warning: warning,
        error: error
    }
})()