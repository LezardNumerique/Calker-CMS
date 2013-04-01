var calker = {};

jQuery(function($) {

	/**
	 * This initializes all JS goodness
	 */
	calker.init = function() {

		// Add the close link to all boxes with the closable class
		$(".notice_closable").append('<a href="#" class="notice_close">Fermer</a>');
		$(".alerte_closable").append('<a href="#" class="alerte_close">Fermer</a>');

		// Ajax notice
		$('.ajax_notice').hide();
		$('.ajax_alerte').hide();

		// Close the notifications when the close link is clicked
		$("a.notice_close").live('click', function () {
			$('.notice_closable').fadeOut("slow"); // This is a hack so that the close link fades out in IE
			return false;
		});
		$("a.alerte_close").live('click', function () {
			$('.alerte_closable').fadeOut(200, 0); // This is a hack so that the close link fades out in IE
			return false;
		});

		// Fade in the notifications
		$(".notice").fadeIn("slow");

		// Fade in the alerts
		$(".alerte").fadeIn("slow");

		// Translations
		create_translations();
		attr_translations();

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

		// Uri autocomplete
		form = $('.uri_autocomplete');
		$('input[name="title"]', form).keyup(function(){
			$.post(BASE_URI + ADMIN_FOLDER + '/urlTitle', { tokencsrf: CSRF, title : $(this).val() }, function(slug){
				$('input[name="uri"]', form).val( slug );
			});
		});

		// Autocomple == off
		$("input:text").attr('autocomplete', 'off');

		// Settings show/hide
		$(".showhide_settings").click(function () {
			$(".account_content").slideToggle("fast");
			$(this).toggleClass("active");
			return false;
		});

	}

	$(document).ready(function() {
		calker.init();
	});

});

function confirmDelete(irreversible)
{
	if(irreversible == 1)
	{
		text = 'Désirez-vous vraiment effacer cette donnée ?? Opération Irréversible !!!!';
	}
	else {
		text = 'Désirez-vous vraiment effacer cette donnée ??';
	}
	var agree=confirm(text);
	if (agree)
		return true ;
	else
		return false ;
}

function create_translations()
{
	$("#create_translation").click(function() {

		var rows = $(":input").serialize();
		var dataString = rows;

		$.ajax({
			data: dataString,
			type: "POST",
			url: BASE_URI+ADMIN_FOLDER+'/translations/create',
			cache: false,
			success: function(data){
				$('#ajax_result_translations').html(data);
			}

		});

		return false;

	});
}

function attr_translations()
{

	$('#attr_1a').keyup(function(event) {
		if (event.keyCode == '13') {
			event.preventDefault();
		}
		var value_id = $(this).val();
		$("#1a").attr('name', value_id);

	});

}

function delete_translations(key)
{
	var agree = confirm('Désirez-vous vraiment effacer cette donnée ??');
	if (agree)
	{
		var rows = $(":input").serialize();
		var dataString = rows;

		$.ajax({
			data: dataString,
			type: "POST",
			url: BASE_URI+ADMIN_FOLDER+'/translations/delete/'+key,
			cache: false,
			success: function(data){
				$('#ajax_result_translations').html(data);
			}
		});

	}
	return false;
}