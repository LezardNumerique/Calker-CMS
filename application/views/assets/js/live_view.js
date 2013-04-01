	$(function() {
		$('a.tooltip').tooltip({
			track: true,
			delay: 0,
			fixPNG: true,
			showURL: false,
			showBody: " - ",
			top: -35,
			left: 5
		});
	});
	function tinymceRemove ()
	{
		if ((tinyMCE != undefined) && (tinyMCE.activeEditor != undefined))
		{
			/*
			tinyMCE.activeEditor.onRemove.add(function(ed) {
				console.debug('Editor was removed: ' + ed.id);
			});
			console.debug('start removing: ' + tinyMCE.activeEditor.id);
			*/
			try {
				tinyMCE.activeEditor.remove();
			} catch (e) {
				//console.debug(e);
			}
		}
	}
	function tinymceConfig ()
	{
		return tinyMCE.init({
			mode : "textareas",
			theme : "advanced",
			skin : "cirkuit",
			plugins : "safari,pagebreak,style,layer,table,advhr,advimage,advlink,inlinepopups,insertdatetime,xhtmlxtras,media,paste",
			language : LANG,
			theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,bullist,numlist,|,link,unlink,|,fontsizeselect,formatselect,|,undo,redo,image|,cleanup,removeformat,code,filemanager,pastetext,pasteword",
			theme_advanced_buttons2 : "",
			theme_advanced_buttons3 : "",
			theme_advanced_toolbar_location : "top",
			theme_advanced_toolbar_align : "left",
			theme_advanced_statusbar_location : "bottom",
			theme_advanced_resizing : true,
			relative_urls : false,
			remove_script_host : false,
			convert_urls : false,
			external_link_list_url : BASE_URI+ADMIN_FOLDER+"/pages/tinyPageList",
			extended_valid_elements: "style[*]",
			height : "300",
			setup : function(ed) {
				ed.onInit.add(function(ed) {
					tinyMCE.execCommand('mceRepaint');
				});
			}
		});
	}
	function dialog()
	{
		$(document).ready(function() {
			$('.dialog').click(function() {
				var uri = $(this).attr('href');
				var title = $(this).attr('data-title');
				var id = $(this).attr('data-id');
				$("#dialog").load(uri, function() {
					$(this).dialog({
						title:title,
						width:1020,
						modal: true,
						resize: false,
						position: 'auto',
						show: 'fade',
						hide: 'fade',
						overlay: {
							backgroundColor: '#000',
							opacity: 0.5
						},
						buttons: {
							Fermer: function() {
								$(this).html('');
								tinymceRemove();
								$(this).dialog('close');
							},
							Enregistrer: function() {
								$('#form_live_view').submit();
							}
						},
						close: function(event, ui)
						{
							$(this).html('');
							tinymceRemove();
							$(this).dialog('close');
						},
						open: function(event, ui) {
							tinymceConfig();
							calker.init();
						}
					});
				});
				return false;
			});
		});
	}
	dialog();