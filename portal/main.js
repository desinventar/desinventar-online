$(function() {
	//$("#desinventar_content").html('This is the content');
	$("#desinventar_content").load('http://192.168.0.13/di8-devel/');
	//$("#desinventar_content").load('http://192.168.0.13/di8-devel/?r=BOL-1250695036-bolivia_gran_chaco');
	$("#searchdbquery").change(function() {
		var Value = $(this).val();
		$("#searchdbresult").load(
			'/di8-devel/',
			{ cmd: 'searchdb',
			  searchdbquery: Value }
		);
	})
	.change();
});
