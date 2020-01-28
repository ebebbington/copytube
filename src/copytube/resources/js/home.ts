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
        })
    })()
    return {
        Methods: Methods,
    }
})()