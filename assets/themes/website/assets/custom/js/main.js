function displayProgress(className) {
	let elem = document.getElementsByClassName(className);
	let totalElements = elem.length;

	for (let r = 0; r < totalElements; r++) {
		let current_elem = elem[r];
		let percentage = parseInt(current_elem.getAttribute('data-percentage'));

		let width = 1;
		if (percentage <= 100) {
			let id = setInterval(frame, 10);

			function frame() {
				if (width >= percentage) {
					current_elem.innerHTML =
						'<span class="tip"><span class="tiptext">' + percentage + '%</span></span>';

					clearInterval(id);
				} else {
					width++;
					current_elem.style.width = width + '%';
				}
			}
		}
	}
}
var Accordion = function () {

	var
		toggleItems,
		items;

	var _init = function () {
		toggleItems = document.querySelectorAll('.accordion_itemTitleWrap');
		toggleItems = Array.prototype.slice.call(toggleItems);
		items = document.querySelectorAll('.accordion_item');
		items = Array.prototype.slice.call(items);

		_addEventHandlers();
		TweenLite.set(items, {
			visibility: 'visible'
		});
		TweenMax.staggerFrom(items, 0.9, {
			opacity: 0,
			x: -100,
			ease: Power2.easeOut
		}, 0.3)
	}

	var _addEventHandlers = function () {
		toggleItems.forEach(function (element, index) {
			element.addEventListener('click', _toggleItem, false);
		});
	}

	var _toggleItem = function () {
		var parent = this.parentNode;
		var content = parent.children[1];
		if (!parent.classList.contains('is-active')) {
			parent.classList.add('is-active');
			TweenLite.set(content, {
				height: 'auto'
			})
			TweenLite.from(content, 0.6, {
				height: 0,
				immediateRender: false,
				ease: Back.easeOut
			})

		} else {
			parent.classList.remove('is-active');
			TweenLite.to(content, 0.3, {
				height: 0,
				immediateRender: false,
				ease: Power1.easeOut
			})
		}
	}

	return {
		init: _init
	}

}();

$(document).ready(function () {
	$('#myCarousel').carousel({
		interval: 4000
	});

	var clickEvent = false;
	$('#myCarousel').on('click', '.nav a', function () {
		clickEvent = true;
		$('.nav li').removeClass('active');
		$(this).parent().addClass('active');
	}).on('slid.bs.carousel', function (e) {
		if (!clickEvent) {
			var count = $('.nav').children().length - 1;
			var current = $('.nav li.active');
			current.removeClass('active').next().addClass('active');
			var id = parseInt(current.data('slide-to'));
			if (count == id) {
				$('.nav li').first().addClass('active');
			}
		}
		clickEvent = false;
	});

	displayProgress("total-progress");

	$("#schoolCarousel").owlCarousel({
		loop: true,
		margin: 10,
		responsiveClass: true,
		nav: true,
		responsive: {
			0: {
				items: 1
			},
			600: {
				items: 2
			},
			1000: {
				items: 3
			}
		}
	});

	$("#partnerCarousel").owlCarousel({
		loop: true,
		margin: 10,
		responsiveClass: true,
		nav: true,
		responsive: {
			0: {
				items: 1
			},
			600: {
				items: 3
			},
			1000: {
				items: 4
			}
		}
    });
    
	Accordion.init();
});