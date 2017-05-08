$(function() {
	$(".title").click(function(){
		if ($(this).hasClass('spread')) {
			$(this).removeClass('spread');
			$($(this).next()).slideDown('fast');
		} else {
			$(this).addClass('spread');
			$($(this).next()).slideUp('fast');
		}
	});
});