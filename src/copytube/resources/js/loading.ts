const Loading = function (isLoading: boolean, elemToHalt?: any) {
    const overlayElement = document.getElementById('overlay-container')
    const loadingElement = document.getElementById('loading-container')
    if (isLoading) {
        overlayElement.style.visibility = 'visible'
        loadingElement.style.visibility = 'visible'
        if (elemToHalt)
            elemToHalt.disabled = true
        $('.loading-circles').css('animation', 'pulse 2s infinite')
    }

    if (!isLoading) {
        overlayElement.style.visibility = 'hidden'
        loadingElement.style.visibility = 'hidden'
        if (elemToHalt)
            elemToHalt.disabled = false
        $('.loading-circles').css('animation', '')
    }
}


export default Loading
