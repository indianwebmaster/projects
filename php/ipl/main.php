<html><head><title>IPL</title></head>
<body>
<?php
	// Main Page
	require_once "MIpl.php";
	require_once "MFuncs.php";

	$year = 2019;
	if (isset($_GET["year"])) $year = intval($_GET["year"]);

	$do_reset = false;
	if (isset($_GET["reset"])) $do_reset = true;


    $do_all_submit = false;
    if (isset($_GET["all_submit"])) $do_all_submit = true;

	$do_game_submit = false;
	if (isset($_GET["game_submit"])) $do_game_submit = true;

	$selected_game_id = 0;
	if (isset($_GET["games"])) $selected_game_id = intval($_GET["games"]);


	$do_user_submit = false;
	if (isset($_GET["user_submit"])) $do_user_submit = true;

	$selected_user_id = 0;
	if (isset($_GET["users"])) $selected_user_id = intval($_GET["users"]);


	$do_date_submit = false;
	if (isset($_GET["date_submit"])) $do_date_submit = true;

	$selected_date = "";
	if (isset($_GET["date"])) $selected_date = $_GET["date"];


	$do_team_submit = false;
	if (isset($_GET["team_submit"])) $do_team_submit = true;

	$selected_team_id = 0;
	if (isset($_GET["teams"])) $selected_team_id = intval($_GET["teams"]);


	$do_place_bet = false;
	if (isset($_GET["place_bet"])) $do_place_bet = true;

	$set_winner = false;
	if (isset($_GET["set_winner"])) $set_winner = true;


	if ($do_reset == true) {
		// clear screen
		$selected_game_id = 0;
		$selected_user_id = 0;
		$selected_team_id = 0;
		$selected_date = "";
	}

    if ($do_all_submit== true) {
        // clear screen
        $do_date_submit = true;
        $do_game_submit = true;
        $do_team_submit = true;
        $do_user_submit = true;
    }

	$ipl = new MIpl($year);
	$ipl->loadData();

    $dt_today = date('D M d');
    $dt = new DateTime("2019-03-21 00:00:00", new DateTimeZone("America/New_York"));
    if (strlen($selected_date) <= 0) {
        $selected_date = $dt_today;
    }

    if ($selected_game_id == 0) {
        $games_today = $ipl->mGames->get_by_date($selected_date);
        if (count($games_today) > 0) {
            $selected_game_id = $games_today[0][0];
        }
    }

?>

	<table>
		<form method="get" action="./main.php">
			<input type="hidden" name="year" value="2019">
			<tr>
				<td>Game</td>
				<td>User</td>
				<td>Team</td>
				<td>Bet Date</td>
			</tr>
			<tr>
				<td>
					<select name="games">
						<?php
						$num_games = $ipl->mGames->num_games;
						for ($i=1; $i <= $num_games; $i++) {
							$one_game = $ipl->mGames->arr[$i];

							$game_id = $one_game[0];
							$game_date = $one_game[1] . " " . $one_game[2]; 
							$home_team = $one_game[3];
							$away_team = $one_game[4];

							$game_str = "$game_date $home_team vs $away_team";

							if ($game_id == $selected_game_id) {
								echo "<option value=\"$game_id\" selected>$game_str</option>";
							} else {
								echo "<option value=\"$game_id\">$game_str</option>";
							}
						}
						?>
					</select>
				</td>
				<td>
					<select name="users">
						<?php
						$num_users = $ipl->mUsers->num_users;
						for ($i=1; $i <= $num_users; $i++) {
							$one_user = $ipl->mUsers->arr[$i];

							$user_id = $one_user[0];
							$name = $one_user[1];

							$user_str = "$name";

							if ($user_id == $selected_user_id) {
								echo "<option value=\"$user_id\" selected>$user_str</option>";
							} else {
								echo "<option value=\"$user_id\">$user_str</option>";
							}
						}
						?>
					</select>
				</td>
				<td>
					<select name="teams">
						<?php
						$num_teams = $ipl->mTeams->num_teams;
						for ($i=1; $i <= $num_teams; $i++) {
							$one_team = $ipl->mTeams->arr[$i];

							$team_id = $one_team[0];
							$name = $one_team[1];

							$team_str = "$name";

							if ($team_id == $selected_team_id) {
								echo "<option value=\"$team_id\" selected>$team_str</option>";
							} else {
								echo "<option value=\"$team_id\">$team_str</option>";
							}
						}
						?>
					</select>
				</td>
				<td>
					<select name="date">
						<?php
							for ($i=0; $i<53; $i++) {
								$fmt_dt = $dt->format("D M d");
								if ($fmt_dt == $selected_date) {
									echo "<option value=\"$fmt_dt\" selected>$fmt_dt</option>";
								} else {
									echo "<option value=\"$fmt_dt\">$fmt_dt</option>";
								}
								$dt->modify("+24 hours");
							}
						?>
					</select>
				</td>
			</tr>
			<tr>
				<td colspan=4>
                    <input type="submit" name="all_submit" value="All Info">
					<input type="submit" name="game_submit" value="Games Info">
					<input type="submit" name="user_submit" value="Users Info">
					<input type="submit" name="team_submit" value="Games for Team">
					<input type="submit" name="date_submit" value="Game on Date">
				</td>
			</tr>
			<tr>
				<td colspan=4>
					<input type="submit" name="place_bet" value="Place bet">
					<input type="submit" name="set_winner" value="Set Winner">
				</td>
			</tr>
			<tr>
				<td colspan=4>
					<input type="submit" name="reset" value="Reset">
				</td>
			</tr>
		</form>
	</table>
<?php
    if ($do_date_submit) {
        $game = $ipl->mGames->get_by_id($selected_game_id);
        $use_date = $game[1];
        $ipl->show_games_on_date($use_date);
    }

    if ($do_game_submit) {
	    $ipl->show_games_info($selected_game_id);
	}

	if ($do_user_submit) {
	    $ipl->show_user_info($selected_user_id);
	}

	if ($do_team_submit) {
	    $ipl->show_team_info($selected_team_id);
	}

	if($do_place_bet) {
		$use_user = $ipl->mUsers->get_by_id($selected_user_id); 
		$use_game = $ipl->mGames->get_by_id($selected_game_id); 
		$use_team = $ipl->mTeams->get_by_id($selected_team_id);

        if ($ipl->mBets->place_bet($use_game, $use_user, $use_team, $selected_date) == true) {
            $ipl->save();
            $ipl->loadData();
            echo "<h2>Bet Placed</h2>";
            $ipl->show_games_info($use_game[0]);
            $ipl->show_user_info($use_user[0]);
        } else {
            echo "<p><b>Invalid selection.</b> Select one of the two teams playing in this game</p>";
        }
	}

	if($set_winner) {
		$use_game = $ipl->mGames->get_by_id($selected_game_id); 
		$use_team = $ipl->mTeams->get_by_id($selected_team_id);
		$err_string="";

		if ($ipl->set_winner($use_game, $use_team, $err_string) == true) {
            $ipl->save();
            $ipl->loadData();
            echo "<h2>Winner Chosen</h2>";
            $ipl->show_games_info($use_game[0]);
        } else {
            echo "<p><b>Invalid selection</b>. $err_string</p>";
        }
	}
?>
</body>
</html>