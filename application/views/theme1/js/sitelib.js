var calker = {};

jQuery(function($) {

	/**
	 * This initializes all JS goodness
	 */
	calker.init = function() {

		// Add the close link to all boxes with the closable class
		$(".notice_closable").append('<a href="#" class="notice_close">Fermer</a>');
		$(".alerte_closable").append('<a href="#" class="alerte_close">Fermer</a>');

		//Auto close
		function automateClose()
		{
			$('.notice_close').fadeTo(200, 0); // This is a hack so that the close link fades out in IE
			$('p.notice').fadeTo(200, 0);
			$('p.notice').slideUp(400);

			$('.alerte_close').fadeTo(200, 0); // This is a hack so that the close link fades out in IE
			$('p.alerte').fadeTo(200, 0);
			$('p.alerte').slideUp(400);
		}

		// Ajax notice
		$('.ajax_notice').hide();
		$('.ajax_alerte').hide();

		// Close the notifications when the close link is clicked
		$("a.notice_close").live('click', function () {
			$('.notice_close').fadeTo(200, 0); // This is a hack so that the close link fades out in IE
			$('.notice_close').parent().fadeTo(200, 0);
			$('.notice_close').parent().slideUp(400);
			return false;
		});
		$("a.alerte_close").live('click', function () {
			$('.alerte_close').fadeTo(200, 0); // This is a hack so that the close link fades out in IE
			$('.alerte_close').parent().fadeTo(200, 0);
			$('.alerte_close').parent().slideUp(400);
			return false;
		});

		// Fade in the notifications
		$(".notice").fadeIn("slow");

		// Fade in the alerts
		$(".alerte").fadeIn("slow");

		// Autocomple == off
		//$("input:text").attr('autocomplete', 'off');

		// Uri autocomplete
		form = $('.uri_autocomplete');
		$('input[name="title"]', form).keyup(function(){
			$.post(BASE_URI + ADMIN_FOLDER + '/urlTitle', { title : $(this).val() }, function(slug){
				$('input[name="uri"]', form).val( slug );
			});
		});

		// Target => uri
		$('.target').change(function() {
			var str = "";
			$(".target option:selected").each(function () {
				if($(this).val() != 0)
				{
					str += $(this).val() + " ";
				}
			});
			$("#uri").val(str);

		});

	}

	$(document).ready(function() {
		calker.init();
	});

});

//---------------- Top link
jQuery.fn.topLink = function(settings) {
	settings = jQuery.extend({
			min: 1,
			fadeSpeed: 200,
			ieOffset: 50
	}, settings);
	return this.each(function() {
		var el = $(this);
		el.css('display','none');
		$(window).scroll(function() {
			if(!jQuery.support.hrefNormalized) {
				el.css({
					'top': $(window).scrollTop() + $(window).height() - settings.ieOffset
				});
			}
			if($(window).scrollTop() >= settings.min)
			{
				el.fadeIn(settings.fadeSpeed);
			}
			else
			{
				el.fadeOut(settings.fadeSpeed);
			}
		});
	});
};
$(document).ready(function() {
	$('#top_link a').live('click', function(event){
		event.preventDefault();
		$('html,body').animate({scrollTop: 0}, 'slow');
	});
});
// rendons à prishablepress ce qui appartient à César, tout le crédit de cette méthode revient à @ http://perishablepress.com/press/2008/12/16/unobtrusive-javascript-remove-link-focus-dotted-border-outlines/
if(document.all)
for(var i in document.links)
document.links[i].onfocus = document.links[i].blur;

function confirmDelete()
{
	text = 'Désirez-vous vraiment effacer cette donnée ??';
	var agree=confirm(text);
	if (agree)
		return true ;
	else
		return false ;
}