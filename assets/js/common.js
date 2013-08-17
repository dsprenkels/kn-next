// Menubar half-fixed
if (! $.browser.mobile) {
        $(window).scroll(function(e) {
            var pos = 400-70;
            if($(window).scrollTop() > pos) {
                $('#header').css({
                    position: 'fixed',
                    width: '100%',
                    top: -pos
                });
            } else {
                $('#header').css({
                    position: 'static',
                    top: pos
                });
            }
        });
}

// ScrollUp animation
$(document).ready( function() {
	$("#scrollUp").click(function(event) {
		var sTop = 0;
		if(!$.browser.mobile) {
			sTop = 400-70;
		}
		$('html, body').animate({scrollTop: sTop}, 300);
		event.preventDefault();
		return false;
	});
});

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
