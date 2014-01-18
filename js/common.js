var headerHeight = 400;
var collapsedHeaderHeight = 70;
var headerFixedThreshold = headerHeight - collapsedHeaderHeight;

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

// Menubar half-fixed
if (! $.browser.mobile) {
	$(window).scroll(function(e) {
		if($.cookie('collapseHeader') === 'y') {
			$('#header').css({
				position: 'fixed',
				top: -headerFixedThreshold
			});
		} else if($(window).scrollTop() > headerFixedThreshold) {
			$('#header').css({
				position: 'fixed',
				top: -headerFixedThreshold
			});
		} else {
			$('#header').css({
				position: 'absolute',
				top: 0
			});
		}
	});
}

$(document).ready(function() {
    // ScrollUp animation
	$("#scrollUp").click(function(event) {
		var sTop = 0;
		if(!$.browser.mobile && $.cookie('collapseHeader') != 'y') {
			sTop = headerFixedThreshold;
		}
		$('html, body').animate({scrollTop: sTop}, 300);
		event.preventDefault();
		return false;
	});

	// read menu state collapse state from cookie
	if($.cookie('collapseHeader') === 'y') {
        $('#header').css({
			position: 'fixed',
			top: -headerFixedThreshold
		});
		$('#content').css({
			top: collapsedHeaderHeight
		});
	}

	$('#csrfmiddlewaretoken').val(getCsrftoken());

	$(document.getElementById('loginButtonLink')).bind('click', function (event) {
		var loginButton = $('#loginButton');
		loginButton.toggleClass('open');
		event.preventDefault();
		event.originalEvent.loginWindow = true;
	});

	$(document.body).bind('click', function (event) {
		if (event.originalEvent.loginWindow) {
			// don't respond to the event when the click happens inside the login window
			return;
		}
		$('#loginButton').removeClass('open');
	});

	$('#loginWindow').bind('click', function (event) {
		if ($('#loginButton').hasClass('open')) {
			// flag that this event comes from clicking on the login window
			event.originalEvent.loginWindow = true;
		}
	});
});

function collapseHeader(img) {
	if($.cookie('collapseHeader') === 'y') {
		$.cookie('collapseHeader', 'n');
		img.src = 'img/up.png';

		$('#content').css({
			top: headerHeight
		});

		$('#header').css({
			height: headerHeight
		});

		if($(window).scrollTop() > headerFixedThreshold) {
			$('#header').css({
				position: 'fixed',
				top: -headerFixedThreshold
			});
		} else {
			$('#header').css({
				position: 'absolute',
				top: 0
			});
		}
	} else {
		$.cookie('collapseHeader', 'y');
		img.src = 'img/down.png';
        $('#header').css({
			position: 'fixed',
			top: -headerFixedThreshold
		});
		$('#content').css({
			top: collapsedHeaderHeight
		});
	}
}

// Implement rot13 for email obscurification
javascript:String.prototype.rot13 = function(s)
{
  return (s = (s) ? s : this).split('').map(function(_)
  {
    if (!_.match(/[A-Za-z]/)) return _;
    c = _.charCodeAt(0)>=96;
    k = (_.toLowerCase().charCodeAt(0) - 96 + 12) % 26 + 1;
    return String.fromCharCode(k + (c ? 96 : 64));
  }
  ).join('');
};
