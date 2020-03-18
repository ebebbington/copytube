let intervalId: any = 0

const Loading = function (isLoading: boolean, elemToHalt?: any) {
    const overlayElement = document.getElementById('overlay-container')
    const loadingElement = document.getElementById('loading-container')
    const ellipsesMessageElement = document.querySelector('#loading-container > p')
console.log(intervalId)
    if (isLoading) {
        overlayElement.style.visibility = 'visible'
        loadingElement.style.visibility = 'visible'

        if (elemToHalt)
            elemToHalt.disabled = true
        ellipsesMessageElement.textContent = '.'
        let ellipses = '.'
        intervalId = setInterval(async () => {
            ellipsesMessageElement.textContent = ellipses
            if (ellipses === '...') ellipses = ''
            ellipses += '.'
        }, 1000)
    }

    if (!isLoading) {
        overlayElement.style.visibility = 'hidden'
        loadingElement.style.visibility = 'hidden'
        ellipsesMessageElement.textContent = '.'
        clearInterval(intervalId)
        if (elemToHalt)
            elemToHalt.disabled = false
    }
}


export default Loading
