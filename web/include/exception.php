<?php
function global_exception_handler($exception) {
	ob_clean();
	echo "<h1>Exception</h1>\n";
	echo $exception->getMessage() . "\n";
	exit();
}

