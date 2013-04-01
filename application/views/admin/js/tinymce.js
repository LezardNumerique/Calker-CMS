tinyMCE.init({
	mode : "exact",
	elements : "body",
	theme : "advanced",
	skin : "default",
	plugins : "safari,pagebreak,style,layer,table,advhr,advimage,advlink,inlinepopups,insertdatetime,xhtmlxtras,media,paste",
	language : LANG,
	theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,bullist,numlist,|,link,unlink,|,fontsizeselect,formatselect,|,undo,redo,image|,cleanup,removeformat,code,filemanager,pastetext,pasteword",
	theme_advanced_buttons2 : "",
	theme_advanced_buttons3 : "",
	theme_advanced_toolbar_location : "top",
	theme_advanced_toolbar_align : "center",
	theme_advanced_statusbar_location : "bottom",
	theme_advanced_resizing : false,
	relative_urls : false,
	remove_script_host : false,
	convert_urls : false,
	external_link_list_url : BASE_URI+ADMIN_FOLDER+"/pages/tinyPageList",
	//content_css : APPPATH+"views/"+ADMIN_THEME+"/css/tiny.css",
	width : "720",
	height : "400",
	//file_browser_callback : 'myFileBrowser',
	autosave_ask_before_unload : false
});

tinyMCE.init({
	mode : "exact",
	elements : "body_2",
	theme : "advanced",
	skin : "default",
	plugins : "safari,pagebreak,style,layer,table,advhr,advimage,advlink,inlinepopups,insertdatetime,xhtmlxtras,media,paste",
	language : LANG,
	theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,bullist,numlist,|,link,unlink,|,fontsizeselect,formatselect,|,undo,redo,image|,cleanup,removeformat,code,filemanager,pastetext,pasteword",
	theme_advanced_buttons2 : "",
	theme_advanced_buttons3 : "",
	theme_advanced_toolbar_location : "top",
	theme_advanced_toolbar_align : "center",
	theme_advanced_statusbar_location : "bottom",
	theme_advanced_resizing : false,
	relative_urls : false,
	remove_script_host : false,
	convert_urls : false,
	external_link_list_url : BASE_URI+ADMIN_FOLDER+"/pages/tinyPageList",
	//content_css : APPPATH+"views/"+ADMIN_THEME+"/css/tiny.css",
	width : "720",
	height : "400",
	//file_browser_callback : 'myFileBrowser',
	autosave_ask_before_unload : false
});

tinyMCE.init({
	mode : "exact",
	elements : "body_3",
	theme : "advanced",
	skin : "default",
	plugins : "safari,pagebreak,style,layer,table,advhr,advimage,advlink,inlinepopups,insertdatetime,xhtmlxtras,media,paste",
	language : LANG,
	theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,bullist,numlist,|,link,unlink,|,fontsizeselect,formatselect,|,undo,redo,image|,cleanup,removeformat,code,filemanager,pastetext,pasteword",
	theme_advanced_buttons2 : "",
	theme_advanced_buttons3 : "",
	theme_advanced_toolbar_location : "top",
	theme_advanced_toolbar_align : "center",
	theme_advanced_statusbar_location : "bottom",
	theme_advanced_resizing : false,
	relative_urls : false,
	remove_script_host : false,
	convert_urls : false,
	external_link_list_url : BASE_URI+ADMIN_FOLDER+"/pages/tinyPageList",
	//content_css : APPPATH+"views/"+ADMIN_THEME+"/css/tiny.css",
	width : "720",
	height : "400",
	//file_browser_callback : 'myFileBrowser',
	autosave_ask_before_unload : false
});

tinyMCE.init({
	mode : "exact",
	elements : "body_short",
	theme : "advanced",
	skin : "default"
});

/*
function myFileBrowser (field_name, url, type, win) {
	tinyMCE.activeEditor.windowManager.open({
		file : "admin/filemanager",
		title : 'My File Browser',
		width : 980,
		height : 600,
		resizable : "no",
		inline : "yes",
		close_previous : "no",
		popup_css : false
    	},
	{
		window : win,
		input : field_name
    	});
    return false;
}
*/
