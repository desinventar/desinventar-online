function onReadyDatabaseCreate() {
	// Load Images in bb buttons
	jQuery('.bb').each(function(index, Element) {
		jQuery(this).css('background','url(' + jQuery(this).attr('img') + ')');
	});
	
	// Create a SWFUpload instance and attach events...
	jQuery('#divDBImport-Control').swfupload({
		upload_url: 'index.php',
		post_params: {cmd : 'fileupload'},
		file_size_limit : "204800",
		file_types : "*.*",
		file_types_description : "All Files",
		file_upload_limit : "0",
		//file_post_name: 'file',
		flash_url: 'external/swfupload/swfupload.swf',
		button_image_url : 'external/swfupload/XPButtonUploadText_61x22.png',
		button_width : 61,
		button_height : 22,
		button_placeholder_id : 'button',
		debug: false,
		custom_settings : {something : "here"}
	})
	.bind('fileQueued', function(event, file) {
		// start the upload since it's queued
		jQuery('#progressMark').css('width', '0px');
		jQuery('#btnDBImportCancel').attr('file_id', file.id);
		jQuery(this).swfupload('startUpload');
	})
	.bind('uploadProgress', function(event, file, bytesLoaded) {
		// Show Progress
		var percentage = Math.round((bytesLoaded/file.size)*100);
		jQuery('#progressMark').css('width', percentage + '%');
	})
	.bind('uploadSuccess', function(event, file, serverData) {
		var data = eval('(' + serverData + ')');
	})
	.bind('uploadComplete', function(event, file) {
		// upload has completed, lets try the next one in the queue
		jQuery(this).swfupload('startUpload');
	});
	
	jQuery('#btnDBImportCancel').click(function() {
		var swfu = jQuery.swfupload.getInstance('#divDBImport-Control');
		swfu.cancelUpload(jQuery(this).attr('file_id'));
		jQuery('#progressMark').css('width', '0px');
	});
}
