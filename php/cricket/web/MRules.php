<?php
require_once 'MConfig.php';

class MRules {
	public function __construct($tournament = "") {
		$this->showRules($tournament);
	}

    public function showRules($tournament) {
		$undefined = true;
		if (strlen($tournament) > 0) {
			$mConfig = new MConfig();
			$mConfig->initvars($tournament);
			$l_main_url = $mConfig->main_url;
			$l_logo_url = $mConfig->logo_url;
			
			$undefined = false;
			if ($tournament == 'ipl2019') {
				self::showRules_ipl2019($l_main_url, $l_logo_url);
			} elseif ($tournament == 'odi_worldcup2019') {
				self::showRules_odi_worldcup2019($l_main_url, $l_logo_url);
			} elseif ($tournament == 'ipl2020') {
				self::showRules_ipl2019($l_main_url, $l_logo_url);
			} elseif ($tournament == 'ipl2021') {
				self::showRules_ipl2019($l_main_url, $l_logo_url);
			} else {
				$undefined = true;
			}
		}
		if ($undefined == true) {
			echo "No rules defined for tournament ($tournament) as yet<p>";
		}
    }

    private function showRules_odi_worldcup2019($l_main_url, $l_logo_url) {
        self::showRules_ipl2019($l_main_url, $l_logo_url);
    }

    private function showRules_ipl2019($l_main_url, $l_logo_url) {
        $rules = <<<EOT
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">

    <title>YaMaVi League Rules</title>
</head>
<body>
<div class="container">
    <h1><a href="$l_main_url">$l_logo_url</a>YaMaVi League Rules</h1>
   <h3>Rules are simple.</h3>
    <h4>1. Login</h4>
    <ol>
        <li>Setup your login just one time. Choose a simple pasword, not your bank's login password.</li>
        <li>Login using this password</li>
        <li>If you forget the password, contact me (Manoj Thakur). If you don't know how .... well you don't belong here! :-)</li>
    </ol>
    <h4>2. Place a new bet or Change an existing bet</h4>
    <ol>
        <li>You need to login to place a bet</li>
        <li>While you can see details for other users, you can only place a bet for yourself</li>
        <li>Select your game and the team you wanna bet on. Select "Place Bet"</li>
        <li>The bet date will be 'todays' date, If you update your bet at any time, the date will change to the new date. So *** BE CAREFUL ***</li>
        <li>If you bet 2 or more days before the game date, the win points are doubled (4 instead of 2)</li>
    </ol>
    <h4>3. Points</h4>
    You will earn points if the team you bet on, wins the game. Depending on the game date and bet date, <br/>
    - you will win <b>either 4 points</b> if bet date is 2 or more days before game date<br/>
    - <b>or 2 points</b> if the bet date is 0 or 1 day before the game date.<br/><br/>

    There will be one or more bonus rounds, where you can earn points in addition to the above win points.<br/>
    The <b>Bonus Info</b> link will reflect the details of this round, for a specific tournament.<p>
    
    <h4>4. Winner</h4>
    The winner is obviously the person with the most points at the end of the tournament.<p/>

    <h3>Issues/Question</h3>
    Just get in touch with me (Manoj Thakur). If you don't know how .... again, you don't belong here! :-)<p/>
</div>
</body>
</html>
EOT;
    print ($rules);
    }
}
?>
