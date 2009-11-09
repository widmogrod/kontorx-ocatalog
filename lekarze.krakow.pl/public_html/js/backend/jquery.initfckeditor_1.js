$(document).ready(function() {
	var content = $('textarea[name=content]');
	var description = $('textarea[name=description]');
	if (content.length > 0) {
		var oFCKeditor = new FCKeditor('content') ;
		oFCKeditor.BasePath	= '/js/fckeditor/' ;
		oFCKeditor.Config['SkinPath'] = '/js/fckeditor/editor/skins/office2003/';
		oFCKeditor.Config['DefaultLanguage'] = 'pl';
		oFCKeditor.Config['BodyClass'] = 'editor';
		oFCKeditor.ToolbarSet = 'My';
		oFCKeditor.ReplaceTextarea() ;

		// ustawienie klasy editor
		$('#content___Frame').addClass('editor');
	}
	if (description.length > 0) {
		var oFCKeditor = new FCKeditor('description') ;
		oFCKeditor.BasePath	= '/js/fckeditor/' ;
		oFCKeditor.Config['SkinPath'] = '/js/fckeditor/editor/skins/office2003/';
		oFCKeditor.Config['DefaultLanguage'] = 'pl';
		oFCKeditor.Config['BodyClass'] = 'editor';
		oFCKeditor.ToolbarSet = 'Basic';
		oFCKeditor.ReplaceTextarea() ;

		// ustawienie klasy editor
		$('#description___Frame').addClass('editor-medium');
	}
});