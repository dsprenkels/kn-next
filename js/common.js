var headerHeight = 400;
var collapsedHeaderHeight = 70;
var headerFixedThreshold = headerHeight - collapsedHeaderHeight;

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

	function getCsrftoken() {
		var results = document.cookie.match(new RegExp('(^|; *)csrftoken=([^;]+)'));
		if (results) {
			return decodeURIComponent(jQuery.trim(results[2]));
		}
	}

	$(document.getElementById('loginButtonLink')).bind('click', function (event) {
		var loginButton = $('#loginButton');
		loginButton.toggleClass('open');
		event.preventDefault();
		event.originalEvent.loginWindow = true;

		if (!loginButton.hasClass('open')) {
			return;
		}

		// insert proper CSRF token

		var csrftoken = getCsrftoken();
		if (csrftoken) {
			// easy - the token has already been set
			$('#csrfmiddlewaretoken').val(csrftoken);
			$('#input-submit').prop('disabled', false);
		} else {
			// try to get the CSRF token with an Ajax request
			$.get('/accounts/login/')
				.done(function() {
					var csrftoken = getCsrftoken();
					if (csrftoken) {
						$('#csrfmiddlewaretoken').val(csrftoken);
						$('#input-submit').prop('disabled', false);
					} else {
						$('#lw-csrferror').removeClass('hidden');
						console.error('failed to load CSRF token - apparently wasn\'t set via Ajax');
					}
				})
				.fail(function() {
					$('#lw-csrferror').removeClass('invisible');
					console.error('failed to load CSRF token - could not do an Ajax request');
				});
		}
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
