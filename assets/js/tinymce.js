function init() {
	tinyMCEPopup.resizeToInnerSize();
}

function insertBWPRPcode() {

	var tagtext;
	var category_ddb = document.getElementById('bwprp_categories');
	var category     = category_ddb.value;
        var keywords_ddb = document.getElementById('bwprp_keyword');
        var keywords     = keywords_ddb.value;

	if( ( category == "" ) && ( keywords == "" ) )
            return;

        tagtext = "[bwprp cat='" + category + "' keywords='" + keywords + "'";

        window.tinyMCE.execInstanceCommand('content', 'mceInsertContent', false, tagtext + ']');
	tinyMCEPopup.editor.execCommand('mceRepaint');
	tinyMCEPopup.close();
	return;
}

