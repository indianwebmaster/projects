<!doctype html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">

    <style>
        body{
            background: url("img/ipl2019.jpg") no-repeat fixed center center;
        }

    </style>
    <title>IPL</title>
</head>
<body>
<div class="container">
    <table border="1" width="100%"><tr height="800em"><td align="left" valign="top">
<?php
	// Main Page
	require_once "MIpl.php";
	require_once "MFuncs.php";

	$year = 2019;
	if (isset($_POST["year"])) $year = intval($_POST["year"]);

	$do_reset = false;
	if (isset($_POST["reset"])) $do_reset = true;


	$admin_mode = false;
	if (isset($_GET["admin"]) || isset($_POST["admin"])) $admin_mode = true;

    $do_all_submit = false;
    if (isset($_POST["all_submit"])) $do_all_submit = true;

	$do_game_submit = false;
	if (isset($_POST["game_submit"])) $do_game_submit = true;

	$selected_game_id = 0;
	if (isset($_POST["games"])) $selected_game_id = intval($_POST["games"]);


	$do_user_submit = false;
	if (isset($_POST["user_submit"])) $do_user_submit = true;

	$selected_user_id = 0;
	if (isset($_POST["users"])) $selected_user_id = intval($_POST["users"]);


	$do_date_submit = false;
	if (isset($_POST["date_submit"])) $do_date_submit = true;

	$selected_date = "";
	if (isset($_POST["date"])) $selected_date = $_POST["date"];


	$do_team_submit = false;
	if (isset($_POST["team_submit"])) $do_team_submit = true;

	$selected_team_id = 0;
	if (isset($_POST["teams"])) $selected_team_id = intval($_POST["teams"]);


	$do_place_bet = false;
	if (isset($_POST["place_bet"])) $do_place_bet = true;

	$set_winner = false;
	if (isset($_POST["set_winner"])) $set_winner = true;

    $show_points_table = false;
    if (isset($_POST["points_table"])) $show_points_table = true;

    $show_upcoming_games = false;
    if (isset($_POST["upcoming_games"])) $show_upcoming_games = true;

    $show_stats = false;
    if (isset($_POST["stats"])) $show_stats = true;

    if ($do_reset == true) {
		// clear screen
		$selected_game_id = 0;
		$selected_user_id = 0;
		$selected_team_id = 0;
		$selected_date = "";
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

	<table width="100%">
		<form method="post" action="./main.php">
			<input type="hidden" name="year" value="2019">
			<tr>
				<td>Game</td>
				<td>User</td>
				<td>Team</td>
<?php if ($admin_mode == true) { ?>
				<td>Bet Date</td>
<?php } ?>
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
<?php if ($admin_mode == true) { ?>
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
<?php } ?>
				</td>
			</tr>
			<tr>
				<td>
                    <input type="submit" class="btn btn-primary" name="all_submit"   value="All Info">
                    <input type="submit" class="btn btn-primary" name="points_table" value="YaMaVi Points Table">
                    <input type="submit" class="btn btn-primary" name="upcoming_games" value="Upcoming games">
					<input type="submit" class="btn btn-success" name="game_submit"  value="Game Info">
                    <input type="submit" class="btn btn-success" name="date_submit"  value="Game on Date">
                </td>
                <td>
					<input type="submit" class="btn btn-info" name="user_submit" value="User Info">
                </td>
                <td>
					<input type="submit" class="btn btn-warning" name="team_submit" value="Games for Team">
				</td>
			</tr>
<?php if ($admin_mode == true) { ?>
			<tr>
				<td colspan="3">
                    <input type="hidden" name="admin">
					<input type="submit" name="set_winner" value="Set Winner">
                </td>
                <td>
                    <input type="submit" name="place_bet" value="Place bet">
				</td>
			</tr>
            <tr>
                <td>
                    <input type="submit" name="stats" value="Show Statistics">
                </td>
            </tr>
<?php } ?>
			<tr>
				<td colspan=4>
					<input type="submit" class="btn btn-dark" name="reset" value="Reset">
				</td>
			</tr>
		</form>
	</table>
<?php
    function view_all_info($ipl) {
        view_show_points_table($ipl);
        view_show_upcoming_games($ipl,5);
    }

    function view_show_points_table($ipl) {
        $first_pass = true;
        foreach ($ipl->get_points_table() as $win_points) {
            if ($first_pass == true) {
                echo "<table><tr><th colspan='2'>YaMaVi Points Table</th></tr>";
                echo "<tr><th>Player</th><th>Total Points</th></tr>";
                $first_pass = false;
            }
            echo "<tr><td>". $ipl->mUsers->get_short_name($win_points[0][1]) . "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td><td>" . $win_points[1] . "</td></tr>";
        }
        if ($first_pass == false) {
            echo "</table>";
        }
        echo "<hr/>";
    }

    function view_show_upcoming_games($ipl, $num_days) {
        $first_pass = true;
        foreach ($ipl->mGames->get_upcoming_games($num_days) as $game) {
            if ($first_pass == true) {
                echo "<table><tr valign='top'><th colspan='2'>Upcoming games in next $num_days days</th></tr>";
                echo "<tr valign='top'><th>Game Date</th><th>Home vs Away Team</th><th>Current Bets</th></tr>";
                $first_pass = false;
            }
            $bets_str = "";
            foreach ($ipl->get_bets_on_game($game[0]) as $one_bet) {
                $bets_str .= ($ipl->mUsers->get_short_name($one_bet[0]) . "-" . $one_bet[1] . "-" . $one_bet[2] . "<br>");
            }
            echo "<tr valign='top'><td>". $game[1] . "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td><td>" . $game[3] . " vs " . $game[4] . "</td><td>$bets_str</td></tr>";
        }
        if ($first_pass == false) {
            echo "</table>";
        }
        echo "<hr/>";
    }

    function view_games_on_date($ipl, $on_date) {
        $first_pass = true;
        foreach ($ipl->get_games_on_date($on_date) as $game) {
            if ($first_pass == true) {
                echo "<table><tr><th colspan='2'>Games on " . $game[1] . "</th></tr>";
                echo "<tr><th>Game Date</th><th>Home vs Away Team</th></tr>";
                $first_pass = false;
            }
            echo "<tr><td>". $game[1] . "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td><td>" . $game[3] . " vs " . $game[4] . "</td></tr>";
        }
        if ($first_pass == false) {
            echo "</table>";
        }
        echo "<hr/>";
    }

    function view_game_info($ipl, $game_id) {
        $game = $ipl->mGames->get_by_id($game_id);
        $first_pass = true;
        $completed_game = false;
        echo "<table><tr><th colspan='2'>" . $game[3] . " vs " . $game[4] . "</th></tr>";
        foreach ($ipl->get_winning_team_users($game_id) as $team_users) {
            if ($first_pass == true) {
                echo "<tr><th>Winning Team</th><th>Winning Users</th></tr>";
                $first_pass = false;
                $completed_game = true;
            }
            echo "<tr><td>" . $team_users[0] . "&nbsp;&nbsp;&nbsp;</td>";
            for ($i=1; $i<count($team_users); $i++){
                if ($i == 1) {
                    echo "<td>";
                }
                echo $team_users[$i][1] . " ";
            }
            if ($i > 1) {
                echo "</td>";
            } else {
                echo "<td>Nobody predicted the winner correctly</td>";
            }
            echo "</tr>";
        }
        echo "</table>";
        echo "<hr/>";
        if ($completed_game == false) {
            $first_pass = true;
            foreach ($ipl->get_bets_on_game($game_id) as $one_bet) {
                if ($first_pass == true) {
                    echo "<table><tr><th colspan='3'>Current Bets</th></tr>";
                    echo "<tr><th>Player</th><th>Team</th><th>Bet Date</th></tr>";
                    $first_pass = false;
                }
                echo "<tr><td>" . $one_bet[0] . "</td><td>" . $one_bet[1] . "</td><td>" . $one_bet[2] . "</td></tr>";
            }
            if ($first_pass == false) {
                echo "</table>";
                echo "<hr/>";
            }
        }
    }

    function view_user_info($ipl, $input_user_id) {
        $first_pass = true;
        $total_win_points = 0;
        $use_user = $ipl->mUsers->get_by_id($input_user_id);
        for ($i=$ipl->mGames->num_games; $i>=1; $i--) {
            $use_game = $ipl->mGames->arr[$i];
            foreach ($ipl->mGames->get_winning_usernames($use_game) as $winning_username) {
                if ($winning_username == $use_user[1]) {
                    if ($first_pass == true) {
                        echo "<table width=\"100%\"><tr><th colspan='4'>User Info for " . $use_user[1] . "</th></tr>";
                        echo "<tr><th colspan='4'>Games won</th></tr>";
                        echo "<tr><th>Game #</th><th>Game Date</th><th>Teams</th><th>Win Points</th></tr>";
                        $first_pass = false;
                    }
                    $home_team = $use_game[3];
                    $away_team = $use_game[4];
                    $winning_team = $use_game[6];
                    $game_date = $use_game[1] . "  " .  $use_game[2];
                    $win_points = intval($ipl->mBets->get_winning_points($use_game, $use_user));
                    $total_win_points += $win_points;
                    if (MFuncs::substring($home_team,$winning_team)==true) {
                        echo "<tr><td>$use_game[0]</td><td>$game_date</td><td>**<span id=\"winner\">$home_team</span> vs $away_team</td><td>$win_points</td></tr>";
                    } else {
                        echo "<tr><td>$use_game[0]</td><td>$game_date</td><td>$home_team vs **<span id=\"winner\">$away_team</span></td><td>$win_points</td></tr>";
                    }
                }
            }
        }
        if ($total_win_points > 0) {
            echo "<tr><td></td><td></td><td><b>Total Win Points --------------------------------</b></td><td><b>$total_win_points</b></td></tr>";
        }
        if ($first_pass == false) {
            echo "</table>";
        }

        $first_pass = true;
        for ($i=$ipl->mBets->num_bets; $i>=1; $i--) {
            $one_bet = $ipl->mBets->arr[$i];
            $num_cols = count($one_bet);
            for ($j = 2; $j < $num_cols; $j += 3) {
                $user = $one_bet[$j];
                if (MFuncs::substring($use_user[1],$user,true) == true) {
                    $game_id = $one_bet[1];
                    $team = $one_bet[$j + 1];
                    $bet_date = $one_bet[$j + 2];
                    $game = $ipl->mGames->get_by_id($game_id);
                    $home_team = $game[3];
                    $away_team = $game[4];
                    $game_date = $game[1] . "  " .  $game[2];
                    if ($first_pass == true) {
                        echo "<table width=\"100%\"><tr><th colspan='5'>Bets made</th></tr>";
                        echo "<tr><th>Game #</th><th>Game Date</th><th>Teams</th><th>Your Bet</th><th>On Date</th></tr>";
                        $first_pass = false;
                    }
                    echo "<tr><td>$game_id</td><td>$game_date</td><td>$home_team vs $away_team</td><td>$team</td><td>$bet_date</td></tr>";
                }
            }
        }
        if ($first_pass == false) {
            echo "</table>";
        }
        echo "<hr/>";
    }


    if ($do_all_submit == true) {
        view_all_info($ipl);
    }

    if ($show_points_table) {
        view_show_points_table($ipl);
    }

    if ($show_upcoming_games) {
        view_show_upcoming_games($ipl, 5);
    }

    if ($do_date_submit) {
        $cur_sel_game = $ipl->mGames->get_by_id($selected_game_id);
        $use_date = $cur_sel_game[1];
        view_games_on_date($ipl, $use_date);
    }

    if ($do_game_submit) {
        view_game_info($ipl, $selected_game_id);
	}

	if ($do_user_submit) {
//	    $ipl->show_user_info($selected_user_id);
	    view_user_info($ipl, $selected_user_id);
	}

	if ($do_team_submit) {
	    $ipl->show_team_info($selected_team_id);
        echo "<hr/>";
	}

	if($do_place_bet) {
		$use_user = $ipl->mUsers->get_by_id($selected_user_id); 
		$use_game = $ipl->mGames->get_by_id($selected_game_id); 
		$use_team = $ipl->mTeams->get_by_id($selected_team_id);

        if ($ipl->mBets->place_bet($use_game, $use_user, $use_team, $selected_date) == true) {
            $ipl->save();
            $ipl->loadData();
            echo "<h2>Bet Placed</h2>";
            view_game_info($ipl, $use_game[0]);
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
            view_game_info($ipl, $use_game[0]);
        } else {
            echo "<p><b>Invalid selection</b>. $err_string</p>";
        }
	}

?>
</td></tr></table>
</div>
</body>
<!-- Optional JavaScript -->
<!-- jQuery first, then Popper.js, then Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
</html>
