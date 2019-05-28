<?php
    require_once 'MRules.php';
	$tournament = "";
	if (isset($_GET['tournament']) && (strlen(trim($_GET['tournament'])) > 0)) {
		$tournament = $_GET['tournament'];
	}
	$mRules = new MRules($tournament);
?>