document.addEventListener('DOMContentLoaded', function() {
	let elem = document.getElementsByClassName('nav-button');

	Array.from(elem).forEach(element => {
		element.addEventListener('click', () => {
            let nav = document.getElementById('nav-mobile');
            nav.classList.toggle('hidden');

            if(nav.classList.contains('hidden')) {
                nav.classList.replace('opacity-100', 'opacity-0');
                nav.classList.replace('scale-100', 'scale-95');
                nav.classList.replace('duration-200', 'duration-100');
                nav.classList.replace('ease-out', 'ease-in');
            } else {
                nav.classList.replace('opacity-0', 'opacity-100');
                nav.classList.replace('scale-95', 'scale-100');
                nav.classList.replace('duration-100', 'duration-200');
                nav.classList.replace('ease-in', 'ease-out');
            }
		});
    });

    let nav_item = document.getElementsByClassName('main-nav__item');

    Array.from(nav_item).forEach(item => {
        item.addEventListener('click', () => {
            eventFire(document.getElementById('nav-close'), 'click');
        });
    });

    function eventFire(el, etype){
        if (el.fireEvent) {
            el.fireEvent('on' + etype);
        } else {
            var evObj = document.createEvent('Events');
            evObj.initEvent(etype, true, false);
            el.dispatchEvent(evObj);
        }
    }
});
