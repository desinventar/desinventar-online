<script type="text/javascript">
	$(function() {
		$("#searchdbquery").change(function() {
			var Value = $(this).val();
			$("#searchdbresult").load(
				'{-$request_uri-}',
				{ cmd: 'searchdb',
				  searchdbquery: Value }
			);
		});
	});
</script>
<input type="text" id="searchdbquery" size="20">
