<script type="text/javascript">
	$(function() {
		$("#searchdbquery").change(function() {
			var Value = $(this).val();
			$("#searchdbresult").load(
				'/di8-devel/',
				{ cmd: 'searchdb',
				  searchdbquery: Value }
			);
		});
	});
</script>
<input type="text" id="searchdbquery" size="20">
