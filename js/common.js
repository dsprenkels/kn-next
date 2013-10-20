var headerHeight = 400;
var collapsedHeaderHeight = 70;
var headerFixedThreshold = headerHeight - collapsedHeaderHeight;

// Menubar half-fixed
if (! $.browser.mobile) {
	$(window).scroll(function(e) {
		if($.cookie('collapseHeader') != 'y') {
			if($(window).scrollTop() > headerFixedThreshold) {
				$('#header').css({
					position: 'fixed',
					width: '100%',
					top: -headerFixedThreshold
				});
			} else {
				$('#header').css({
					position: 'absolute',
					top: 0
				});
			}
		} else {
			$('#header').css({
				position: 'fixed',
				top: 0
			});
		}
	});
}

$(document).ready( function() {
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
	if($.cookie('collapseHeader') == 'y') {
        $('#header').css({
			position: 'fixed',
			height: collapsedHeaderHeight
        });
		$('#content').css({
			top: collapsedHeaderHeight
		});
	}
});

function collapseHeader(img) {
	if($.cookie('collapseHeader') == 'y') {
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
				width: '100%',
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
			height: collapsedHeaderHeight
        });
		$('#content').css({
			top: collapsedHeaderHeight
		});
	}
}

function showLoginWindow() {
	var doShowWindow = function () {
		$("#loginWindow").dialog({
			draggable: false,
			height: 325,
			width: 325,
			modal: true,
			position: {
				my: 'right top',
				at: 'right bottom',
				of: $('#navigator')
			},
			resizable: false,
			title: 'Inloggen voor leden'
		});
	}
	// Check if we are on the right hostname, then fetch a cookie with
	// a CSRF-token if not redirect to the standard login page.
	// If we have a CSRF-token, show the login window
	if (!$.browser.mobile && window.location.hostname.match(/(^|\.)(karpenoktem\.(nl|com)|kn\.cx)$/i)) {
		if (!$.cookie('csrftoken')) {
			$.get('/accounts/login/', {}, doShowWindow); // XXX dit is nog niet niet getest!
			return;
		} else {
			doShowWindow();
		}
	} else {
		window.location.href = 'https://karpenoktem.nl/accounts/login/';
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
