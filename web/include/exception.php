<?php
function global_exception_handler($exception) {
	echo $exception->getMessage() . "\n";
	exit();
}

