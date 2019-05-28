<?php
	function check_user($username, $paswd) {
		global $mConfig;
		$cricket = new MCricket($mConfig->datadir);
    	$cricket->loadData();
		$login_result = $cricket->mUsers->check_user($username, $paswd);
		unset($cricket);
		return ($login_result);
	}

	// $login_result is either valid, invalid or newuser
	function check_login($post_array, &$session_array) {
		global $mConfig;
		$login_result = 'invalid';
		// Are we previously logged in already. If so, just check if we are logging out
		if (isset($session_array['login']) && ($session_array['login'] == 'success' )) {
			$login_result = 'valid';
			if (isset($post_array['maction']) && $post_array['maction'] == 'Logout') {
				$login_result = 'invalid';
				unset($session_array['user']);
				unset($session_array['paswd']);
				unset($session_array['login']);
			}
		}

		// We are not already logged in
		if ($login_result != 'valid') {
			// We had no password and we got newpaswd from user. Add it to db.
			if (isset($post_array['maction']) && $post_array['maction'] == 'New_Password') {
				if (isset($post_array['user']) && isset($post_array['newpaswd'])) {
					$cricket = new MCricket($mConfig->datadir);
			    	$cricket->loadData();
			    	$cricket->mUsers->add_paswd($post_array['user'], $post_array['newpaswd']);
			    	$cricket->mUsers->save($cricket->users_filepath);
			    	unset($cricket);
			    	print ("Login with new password<br>");
					$is_logged_in = false;
			    	$login_result = 'invalid';
			    }
			} // User is trying to login or first time login (to provide password) 
			elseif (isset($post_array['maction']) && $post_array['maction'] == 'Login') {
				if (isset($post_array['user'])) {
					$session_array['user'] = $post_array['user'];
					// $login_result is either valid, invalid or newuser
					if (isset($post_array['paswd']) == false) $post_array['paswd'] = 'na';
					$login_result = check_user($post_array['user'], $post_array['paswd']);
					if ($login_result == 'valid') {
						$session_array['login'] = 'success';
					}
				}
			}
		}
		return ($login_result);
	}

	function login_form() {
		global $mConfig;
		$main_url = $mConfig->main_url;
		$htmlText = <<<EOT
		<form method="post" action="$main_url">
			User: <input type="text" name="user"><br>
			Password: <input type="password" name="paswd"><br>
			<input type="submit" class="btn btn-success" name="maction" value="Login">
			<input type="submit" class="btn btn-dark" name="maction" value="Reset"><br>
		</form>
EOT;
		print ($htmlText);
	}

	function newpaswd_form($username) {
		global $mConfig;
		$main_url = $mConfig->main_url;
		$htmlText = <<<EOT
		New user. Enter a password you want to use<br>
		<form method="post" action="$main_url">
			<input type="hidden" name="user" value="$username">
			Password: <input type="text" name="newpaswd"><br>
			<input type="submit" class="btn btn-success" name="maction" value="New_Password">
		</form>
EOT;
		print ($htmlText);
	}

	function logout_form() {
		global $mConfig;
		$main_url = $mConfig->main_url;
		print ('<form method="post" action="' . $main_url . '">');
		print ('<input type="submit" class="btn btn-danger" name="maction" value="Logout">');
		print('</form>');
	}

    function is_superadmin($username, $userlist) {
    	if (in_array($username, $userlist)) {
    		return true;
    	} else {
    		return false;
    	}
    }

	$is_logged_in = false;
	$login_result = check_login($_POST, $_SESSION);
	if ($login_result == 'valid') {
		$is_logged_in = true;
	}

	if ($is_logged_in) {
		$user = $_SESSION['user'];
		$_SESSION['login'] = 'success';
	}

?>
