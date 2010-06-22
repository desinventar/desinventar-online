function onReadyDatabaseCreate() {
	jQuery('#divDBImport-Control').swfupload({
		upload_url: 'index.php',
		post_params: {cmd : 'fileupload'},
		file_size_limit : "10240",
		file_types : "*.*",
		file_types_description : "All Files",
		file_upload_limit : "1",
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
		jQuery(this).swfupload('startUpload');
	})
	.bind('uploadSuccess', function(event, file, serverData) {
		var data = eval('(' + serverData + ')');
		//alert(data.Status);
	})
	.bind('uploadComplete', function(event, file) {
		// upload has completed, lets try the next one in the queue
		jQuery(this).swfupload('startUpload');
	});
}
