<html><head><title>IPL</title></head>
<body>
<?php
	// Main Page
	require_once "MIpl.php";

	$year = 2019;
	if (isset($_GET["year"])) $year = intval($_GET["year"]);

	$do_reset = false;
	if (isset($_GET["reset"])) $do_reset = true;


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

	$dt = new DateTime("2019-03-21 00:00:00", new DateTimeZone("America/New_York"));
	$selected_date = $dt->format("D M d");
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
		$selected_date = $dt->format("D M d");
	}

	$ipl = new MIpl($year);
	$ipl->loadData();
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
	if ($do_game_submit) {
		$use_game = $ipl->mGames->get_by_id($selected_game_id);
		if ($use_game[5] == "Completed") {
			echo "Game Completed<br>";
			$num_cols = count($use_game);
			if ($num_cols > 6) echo "Winning Team: $use_game[6]<br>";
			if ($num_cols > 7) {
				echo "Winning Users: ";
				for ($i=7; $i<count($use_game); $i++) {
					echo "$use_game[$i]  ";
				}
				echo "<br>";
			}
		} else {
			$num_bets = $ipl->mBets->num_bets;
			for ($i=1; $i<=$num_bets; $i++) {
				$one_bet = $ipl->mBets->arr[$i];
				if ($one_bet[1] == $selected_game_id) {
					$num_cols = count($one_bet);
					for ($j=2; $j<$num_cols; $j += 3) {
						$bet_str = $one_bet[$j] . " bet " . $one_bet[$j+1] . " on " . $one_bet[$j+2];
						echo "$bet_str<br>";
					}
				}
			}
		}
	}

	if ($do_user_submit) {
		$use_user = $ipl->mUsers->get_by_id($selected_user_id);
		$first_pass = true;
		for ($i=1; $i<$ipl->mGames->num_games; $i++) {
			$num_cols = count($ipl->mGames->arr[$i]);
			if ($num_cols > 7) {
				// Means we have winning user entries
				for ($j=7; $j < $num_cols; $j++) {
					if ($use_user[1] == $ipl->mGames->arr[$i][$j]) {
						if ($first_pass == true) {
							echo "Games won by $use_user[1]<p/>";
							$first_pass = false;
						}
						$home_team = $ipl->mGames->arr[$i][3];
						$away_team = $ipl->mGames->arr[$i][4];
						$winning_team = $ipl->mGames->arr[$i][6];
						$game_date = $ipl->mGames->arr[$i][1] . "  " .  $ipl->mGames->arr[$i][2];
						if (strpos($home_team,$winning_team)!==false) {
							$winning_game = "**<span id=\"winner\">$home_team</span> vs $away_team on $game_date";
						} else {
							$winning_game = "$home_team vs **<span id=\"winner\">$away_team</span> on $game_date";
						}
						echo "$winning_game<br>";
					}
				}
			}
		}
	}

	if ($do_team_submit) {
		$use_team = $ipl->mTeams->get_by_id($selected_team_id);
		$first_pass = true;
		for ($i=1; $i<$ipl->mGames->num_games; $i++) {
			$home_team = $ipl->mGames->arr[$i][3];
			$away_team = $ipl->mGames->arr[$i][4];
			$game_date = $ipl->mGames->arr[$i][1] . "  " .  $ipl->mGames->arr[$i][2];
			if ((strpos($home_team,$use_team[1])!==false) || (strpos($away_team,$use_team[1]) !== false)) {
				if ($first_pass == true) {
					echo "<table width=\"50%\">";
					$first_pass = false;
				}
				echo "<tr><td>$game_date</td><td>$home_team</td><td>vs</td><td>$away_team</td></tr>";
			}
		}
		if ($first_pass == false) {
			echo "</table>";
		}
	}

	if ($do_date_submit) {
		$first_pass = true;
		for ($i=1; $i<$ipl->mGames->num_games; $i++) {
			$home_team = $ipl->mGames->arr[$i][3];
			$away_team = $ipl->mGames->arr[$i][4];
			$game_date = $ipl->mGames->arr[$i][1] . "  " .  $ipl->mGames->arr[$i][2];
			if (strpos(trim($game_date), trim($selected_date)) !== false) {
				if ($first_pass == true) {
					echo "<table width=\"50%\">";
					$first_pass = false;
				}
				echo "<tr><td>$game_date</td><td>$home_team</td><td>vs</td><td>$away_team</td></tr>";
			}
		}
		if ($first_pass == false) {
			echo "</table>";
		}
	}

	if($do_place_bet) {
		$use_user = $ipl->mUsers->get_by_id($selected_user_id); 
		$use_game = $ipl->mGames->get_by_id($selected_game_id); 
		$use_team = $ipl->mTeams->get_by_id($selected_team_id); 

		$ipl->mBets->place_bet($use_game, $use_user, $use_team, $selected_date);
		$ipl->save();
		echo "Bet Placed. Select game with Games Info for details";
	}

	if($set_winner) {
		$use_game = $ipl->mGames->get_by_id($selected_game_id); 
		$use_team = $ipl->mTeams->get_by_id($selected_team_id); 

		$ipl->set_winner($use_game, $use_team);
		$ipl->save();
		echo "Winner Chosen<br>";
	}
?>
</body>
</html>