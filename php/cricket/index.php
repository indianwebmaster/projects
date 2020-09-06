<?php
	$url_dirname = dirname($_SERVER['PHP_SELF']);
	if (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on')) {
		$http_mode = 'https';
	} else {
		$http_mode = 'http';
	}
	$url_base = $http_mode . '://' . $_SERVER['HTTP_HOST'] . $url_dirname;

	$URL = $url_base . '/yamavi.php';
	header ("Location: $URL");
	die();
?>
