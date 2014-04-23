var headerFixedThreshold = 330;
var headerCollapsed;

function getCsrftoken() {
	if ($.cookie('csrftoken')) {
		return $.cookie('csrftoken');
	} else {
		// http://stackoverflow.com/a/12502559/559350
		// generate 32 pseudo-random characters
		var csrftoken = '';
		for (var i=0; i<4; i++) {
			csrftoken += Math.random().toString(36).slice(2, 10);
		}
		// emulate default Django behavior
		// https://github.com/django/django/blob/master/django/middleware/csrf.py#L182
		$.cookie('csrftoken', csrftoken, {path: '/', expires: 7*52})
		return csrftoken;
	}
}

function isMobile() {
	// use matchMedia if available
	return 'ontouchstart' in document.documentElement && (window.matchMedia ? window.matchMedia('(max-device-width: 800px)') : true);
}

$(document).ready(function() {
	$(document.body).removeClass('header-collapsed');
	if (sessionStorage['visited']) {
		window.scroll(0, 330);
	} else {
		$(document.body).addClass('firstView');
	}
	sessionStorage['visited'] = 'true';

	// Menubar half-fixed
	function fixHeader(e) {
		var shouldBeCollapsed = $(window).scrollTop() > headerFixedThreshold;
		// don't touch the DOM if that's not needed!
		if (shouldBeCollapsed !== headerCollapsed) {
			headerCollapsed = shouldBeCollapsed;
			if (shouldBeCollapsed) {
				$('#header').addClass('collapsed');
			} else {
				$('#header').removeClass('collapsed');
			}
		}
	}
	if (!isMobile()) {
		fixHeader();
		$(window).scroll(fixHeader);
	}

	// ScrollUp animation
	$("#scrollUp").click(function(event) {
		var sTop = 0;
		if(!isMobile() && !headerCollapsed) {
			sTop = headerFixedThreshold;
		}
		$('html, body').animate({scrollTop: sTop}, 300);
		event.preventDefault();
		return false;
	});

	if (!isMobile()) {
		$('#csrfmiddlewaretoken').val(getCsrftoken());

		$(document.getElementById('loginButtonLink')).bind('click', function (event) {
			var loginButton = $('#loginButton');
			loginButton.toggleClass('open');
			event.preventDefault();
			event.stopPropagation();
		});

		$(document.body).bind('click', function (event) {
			$('#loginButton').removeClass('open');
		});

		$('#loginWindow').bind('click', function (event) {
			event.stopPropagation();
		});
	}
});

// Implement rot13 for email obscurification
function rot13 (s)
{
	return jQuery.map(s.split(''), function(_)
	{
		if (!_.match(/[A-Za-z]/)) return _;
		c = _.charCodeAt(0)>=96;
		k = (_.toLowerCase().charCodeAt(0) - 96 + 12) % 26 + 1;
		return String.fromCharCode(k + (c ? 96 : 64));
	}
	).join('');
}

function unobfuscateEmail() {
	var emails = document.querySelectorAll('.email.obfuscated');
	for (var i=0; i<emails.length; i++) {
		var email = emails[i];
		var email_link = document.createElement('a');
		if (email.textContent) {
			email_link.textContent = rot13(email.textContent);
		} else { /* IE8 */
			email_link.innerText = rot13(email.innerText);
		}
		email_link.href = 'mailto:' + (email_link.textContent || email_link.innerText);
		email_link.setAttribute('class', 'email');
		email.parentNode.insertBefore(email_link, email);
		email.parentNode.removeChild(email);
	}
}
if (document.addEventListener) {
	document.addEventListener('DOMContentLoaded', unobfuscateEmail);
} else { /* IE8 */
	window.attachEvent('onload', unobfuscateEmail);
}
