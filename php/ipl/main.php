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
	require_once "bonus.php";

	$year = 2019;
	if (isset($_GET["year"])) $year = intval($_GET["year"]);

	$default_mode = true;
	$do_reset = false;
	if (isset($_GET["reset"])) $do_reset = true;

	$admin_mode = false;
	if (isset($_GET["admin"]) || isset($_POST["admin"])) $admin_mode = true;

	$fromlink = false;
	if (isset($_GET["fromlink"]) || isset($_POST["fromlink"])) $fromlink = true;

    $do_all_info = false;
    if (isset($_GET["all_info"])) $do_all_info = true;

	$do_game_info = false;
	if (isset($_GET["game_info"])) $do_game_info = true;

	$selected_game_id = 0;
	if (isset($_GET["games"])) $selected_game_id = intval($_GET["games"]);


	$do_user_info = false;
	if (isset($_GET["user_info"])) $do_user_info = true;

	$selected_user_id = 0;
	if (isset($_GET["users"])) $selected_user_id = intval($_GET["users"]);


	$do_date_info = false;
	if (isset($_GET["date_info"])) $do_date_info = true;

	$selected_date = "";
	if (isset($_GET["date"])) $selected_date = $_GET["date"];


	$do_team_info = false;
	if (isset($_GET["team_info"])) $do_team_info = true;

	$selected_team_id = 0;
	if (isset($_GET["teams"])) $selected_team_id = intval($_GET["teams"]);


	$do_place_bet = false;
	if (isset($_GET["place_bet"])) $do_place_bet = true;

	$set_winner = false;
	if (isset($_GET["set_winner"])) $set_winner = true;

    $show_points_table = false;
    if (isset($_GET["points_table"])) $show_points_table = true;

    $show_ipl_points_table = false;
    if (isset($_GET["ipl_points_table"])) $show_ipl_points_table = true;

    $show_upcoming_games = false;
    if (isset($_GET["upcoming_games"])) $show_upcoming_games = true;

    $show_stats = false;
    if (isset($_GET["stats"])) $show_stats = true;
    
    $show_bonus = false;
    if (isset($_GET["bonus_info"])) $show_bonus = true;

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
		<form method="get" action="./main.php">
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
						for ($i=$num_games; $i >= i; $i--) {
							$one_game = $ipl->mGames->arr[$i];

							$game_id = $one_game[0];
							$game_date = $one_game[1] . " " . $one_game[2]; 
							$home_team = $one_game[3];
							$away_team = $one_game[4];

                            $game_str = $ipl->mGames->get_game_date_string($one_game);

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
                    <input type="submit" class="btn btn-primary" name="all_info"   value="All Info">
                    <input type="submit" class="btn btn-primary" name="points_table" value="YaMaVi Points">
                    <input type="submit" class="btn btn-primary" name="ipl_points_table" value="IPL Points">
                    <input type="submit" class="btn btn-primary" name="upcoming_games" value="Upcoming games">
                    <br/>
					<input type="submit" class="btn btn-success" name="game_info"  value="Game Info">
                    <input type="submit" class="btn btn-success" name="date_info"  value="Game on Date">
                    <input type="submit" class="btn btn-danger" name="bonus_info"  value="Bonus Info">
                </td>
                <td>
					<input type="submit" class="btn btn-info" name="user_info" value="User Info">
                </td>
                <td>
					<input type="submit" class="btn btn-warning" name="team_info" value="Team Info">
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
                    <input id="statsBtn" type="submit" name="stats" value="Show Statistics">
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
/* ***** DEPRECATED BY build_url() *****
    function make_link($add_param, $link_text) {
    	$this_url = $_SERVER['REQUEST_URI'];
		$base_url = strtok($this_url, "&");
		for ($i=0; $i < 4; $i++) {
			$next = strtok("&");
			$base_url .= "&" . $next;
		}
    	$new_url = $base_url . $add_param;
    	$link_href = "<a href=\"". $new_url . "\">" . $link_text . "</a>";
    	return ($link_href);
    }
***** DEPRECATED BY build_url() **** */
    
    function make_user_link($ipl, $user) {
    	return build_url($ipl, false, "user_info", $user, $user[1]); 
    }
    
    function view_all_info($ipl) {
        view_show_points_table($ipl);
        echo "<table><tr><td>"; view_ipl_points_table($ipl); echo "</td><td>.......</td><td>"; view_home_away_wins($ipl); echo "</td></tr></table>";
        view_show_upcoming_games($ipl,5);
    }

    function view_show_points_table($ipl) {
        $user_games_won = array();
        $user_user_matches = array();
        $ipl->user_stats("Manoj Thakur", $user_games_won, $user_user_matches);

        $win_points_table = $ipl->get_points_table();
        $win_percent_by_user = $ipl->get_win_percentage_by_user();

        print("<table border=1><tr>");
        print("<th><h4>YaMaVi Points</h4></th><th>Total Points</th><th>Win %</th>");
        for ($i=1; $i<=$ipl->mUsers->num_users; $i++) {
            $one_user = $ipl->mUsers->arr[$i];
            print("<th>" . make_user_link($ipl, $one_user) . "</th>");
        }
        print("</tr>");

        $c_uidx = 0;
        foreach ($user_user_matches as $match_user) {
            $win_points = $win_points_table[$c_uidx][1];
            $c_user = $ipl->mUsers->get_by_id($c_uidx + 1);

            print("<tr><th>" . make_user_link($ipl,$c_user) . "</th>");
            print("<td>$win_points</td><td>". $win_percent_by_user[$c_uidx] ."%</td>");

            $w_user_id = 1;
            foreach ($match_user as $with_user_count) {
                $w_user = $ipl->mUsers->get_by_id($w_user_id);
                print("<td>$with_user_count</td>");
                $w_user_id += 1;
            }
            print("</tr>");
            $c_uidx += 1;
        }
        print("</table>");
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
            $game_str = $ipl->mGames->get_game_string($game);
            $game_link = make_game_link($ipl, $game);
            echo "<tr valign='top'><td>". $game[1] . "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td><td>" . $game_link . "</td><td>$bets_str</td></tr>";
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
            $game_str = $ipl->mGames->get_game_string($game);
            echo "<tr><td>". $game[1] . "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td><td>" . $game_str . "</td></tr>";
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
        $game_date = $game[1] . ' ' . $game[2];
        $game_str = $ipl->mGames->get_game_string($game);
        echo "<table><tr><th colspan='2'>" . $game_str . " (" . $game_date . ")</th></tr>";
        foreach ($ipl->get_winning_team_users($game_id) as $team_users) {
            if ($first_pass == true) {
                echo "<tr><th>Winning Team</th><th>Winning Users</th></tr>";
                $first_pass = false;
                $completed_game = true;
            }
            $team = $ipl->mTeams->get_by_name($team_users[0]);
            $team_link = make_team_link($ipl, $team);
            echo "<tr><td>" . $team_link . "&nbsp;&nbsp;&nbsp;</td>";
            for ($i=1; $i<count($team_users); $i++){
                if ($i == 1) {
                    echo "<td>";
                }
                $user_link = make_user_link($ipl,$team_users[$i]); 
                echo $user_link . " ";
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
                    echo "<tr><th>Player................</th><th>Team.....</th><th>Bet Date</th></tr>";
                    $first_pass = false;
                }
                $user = $ipl->mUsers->get_by_name($one_bet[0]);
                $user_link = make_user_link($ipl, $user);
                
                $team = $ipl->mTeams->get_by_short_name($one_bet[1]);
                $team_link = make_team_link($ipl, $team, true);
                echo "<tr><td>" . $user_link . "</td><td>" . $team_link . "</td><td>" . $one_bet[2] . "</td></tr>";
            }
            if ($first_pass == false) {
                echo "</table>";
                echo "<hr/>";
            }
        }
    }
    
    function make_game_link($ipl, $game) {
    	$game_str = $ipl->mGames->get_game_string($game);
    	$game_link = build_url($ipl, false, "game_info", $game, $game_str); 
    	return ($game_link);
    }

    function make_team_link($ipl, $team, $short=false) {
    	if ($short) {
    		return build_url($ipl, false, "team_info", $team, $team[2]);
    	} else {
    		return build_url($ipl, false, "team_info", $team, $team[1]);
    	}
    }

    function view_user_info($ipl, $input_user_id) {
        $win_percent_by_user = $ipl->get_win_percentage_by_user();
        $use_user = $ipl->mUsers->get_by_id($input_user_id);
        $uidx = $use_user[0] - 1;
        
        $total_win_points = 0;
        $win_points = 0;
        $first_pass = true;
        for ($i=$ipl->mBets->num_bets; $i>=1; $i--) {
            $one_bet = $ipl->mBets->arr[$i];
            $num_cols = count($one_bet);
            for ($j = 2; $j < $num_cols; $j += 3) {
                $username = $one_bet[$j];
                if (MFuncs::substring($use_user[1],$username,true) == true) {
                    $game_id = $one_bet[1];
                    $team = $ipl->mTeams->get_by_name($one_bet[$j + 1]);
                    $bet_date = $one_bet[$j + 2];
                    $game = $ipl->mGames->get_by_id($game_id);
                    $home_team = $game[3];
                    $away_team = $game[4];
                    $game_date = $game[1] . "  " .  $game[2];
                    if ($first_pass == true) {
                        // echo "<table width=\"100%\"><tr><th colspan='6'>User Info for " . $use_user[1] . "</th></tr>";
                        echo "<table width=\"100%\">";
                        echo "<tr><th colspan='6'>Bets Made & Games Won (Win Percentage = " . $win_percent_by_user[$uidx] . "%)</th></tr>";
                        echo "<tr><th>##..</th><th>Game Date</th><th>Home vs Away Team</th><th>Bet on Team</th><th>Bet Date</th><th>Win Points</th></tr>";
                        $first_pass = false;
                    }
                    $win_points = $ipl->get_user_game_win_points($game, $use_user);
                    $total_win_points += $win_points;
                    $game_link = make_game_link($ipl, $game);
                    $team_link = make_team_link($ipl, $team, true);
                    echo "<tr><td>$game_id</td><td>$game_date</td><td>$game_link</td><td>$team_link</td><td>$bet_date</td><td>$win_points</td></tr>";
                }
            }
        }
        if ($first_pass == false) {
            echo "<tr><th>--</th><th>--</th><th>*** Total Points ***</th><th>--</th><th>--</th><th>$total_win_points</th></tr>";
            echo "</table>";
        }
        echo "<hr/>";
    }


    function view_ipl_points_table($ipl) {
        $team_id = 0;
        $first_pass = true;
        foreach ($ipl->get_ipl_points() as $point) {
            $team_id += 1;
            $team = $ipl->mTeams->arr[$team_id];
            $team_link = make_team_link($ipl, $team);
            if ($first_pass) {
                print("<table border=1><tr><th colspan=4><h4>IPL Points table</h4></th><tr>");
                print("<tr><th>Team</th><th>Points</th><th>Wins</th><th>Losses</th></tr>");
                $first_pass = false;
            }
            print ("<tr><td>$team_link</td><td>$point[1]</td><td>$point[0]</td><td>$point[2]</td></tr>"); 
        }
        if ($first_pass == false) {
            print ("</table>");
        }
        echo "<hr/>";
    }
    
    function view_all_completed_games($ipl) {
        $first_pass = true;
        for ($i = $ipl->mGames->num_games; $i >= 1; $i--) {
            $game = $ipl->mGames->arr[$i];
            if ($ipl->mGames->is_completed($game)) {
                if ($first_pass) {
                    print ("<table><tr><th colspan=3>All completed games</th></tr>");
                    print ("<tr><th>##..</th><th>Game Date......................</th><th>Home vs Away</th></tr>");
                    $first_pass = false;
                }
                $game_date = $game[1];
                $home_team = $game[3];
                $away_team = $game[4];
                $winning_team = $game[6];
                $game_str = $ipl->mGames->get_game_string($game);
                $game_link = make_game_link($ipl, $game);
                print("<tr><td>$i</td><td>$game_date</td><td>$game_link</td></tr>");
            }
        }
        if ($first_pass == false) {
            print("</table>");
        }
        echo "<hr/>";
    }
    
    function view_user_win_loss_streaks($ipl) {
    	$first_pass = true;
    	foreach ($ipl->highest_streak_by_user() as $user_win_loss_streak) {
    		if ($first_pass) {
				print ("<table><tr><th colspan=3>Longest Win/Loss streak</th></tr>");
				print ("<tr><th>Player ............... </th><th>Win Streak</th><th>Loss Streak</th></tr>");
    			$first_pass = false;
    		}
    		$user_link = make_user_link($ipl,$user_win_loss_streak[0]);
//    		$user_link = make_link("&user_info=User Info&users=".$user_win_loss_streak[0][0], $user_win_loss_streak[0][1]);
    		print ("<tr><td>" . $user_link . "</td><td>" . $user_win_loss_streak[1] . "</td><td>" . $user_win_loss_streak[2] . "</td></tr>");
    	}
    	if ($first_pass == false) {
    		print ("</table>");
    	}
    	echo "<hr/>";		
    }
    
    function view_bonus($ipl) {
    	print ("<h3>Bonus 1:</h3>Choose your <b>top four teams</b> of the IPL table that make the playoffs. The teams MUST be in the order of their rankings<br/>");
    	print ("Submit your choice over Whats'app by midnight Sun 14th Apr. Make any changes before then - last entry before deadline will be used<br/>");
    	print ("..... .: Points = 2 -- If your team is in the top four AND in the rank you predicted<br/>");
    	print ("..... .: Points = 1 -- If your team is in the top four BUT NOT in the rank you predicted<br/>");
    	print ("..... .: Points = 0 -- If your team is NOT in the top 4<p/>");
        $first_pass = true;
        for ($i = 1; $i <= $ipl->mBonus->num_items; $i++) {
            if ($first_pass) {
                print ("<table><tr><th colspan=4>Bonus: Current Teams Selected for each user</th></tr>");
                print ("<tr><th>Player ............. </th><th>Team 1</th><th>Team 2</th><th>Team 3</th><th>Team 4</th><th>Last Submitted On</th></tr>");
                $first_pass = false;
            }
            $bonus = $ipl->mBonus->arr[$i];
            $user = $ipl->mUsers->get_by_name($bonus[1]);
            $user_link = make_user_link($ipl,$user);
            $team_link = array();
            for ($j = 0; $j < 4; $j++) {
                $team = $ipl->mTeams->get_by_short_name($bonus[$j + 2]);
                array_push($team_link,make_team_link($ipl, $team, true));
            }
            print ("<tr><td>" . $user_link . "</td><td>" . $team_link[0] . "</td><td>" . $team_link[1] . "</td><td>" . $team_link[2] . "</td><td>" . $team_link[3] . "</td><td>" . $bonus[6] . "</tr>");
        }
    	if ($first_pass == false) {
    		print ("</table>");
    	}
    	echo "<hr/>";		
    }

    function view_home_away_wins($ipl, $one_team_id = -1) {
    	$home_away_wins = $ipl->mGames->get_home_away_wins();
    	$home_wins = $home_away_wins['home_wins'];
    	$away_wins = $home_away_wins['away_wins'];
    	$home_win_counts = array();
    	foreach ($home_wins as $home_team_name) {
    		$team = $ipl->mTeams->get_by_name($home_team_name);
			if (!isset($home_win_counts[$home_team_name])) {
				$home_win_counts[$home_team_name] = 0;
			}
			$home_win_counts[$home_team_name] += 1;
		}
    	$away_win_counts = array();
    	foreach ($away_wins as $away_team_name) {
    		$team = $ipl->mTeams->get_by_name($away_team_name);
			if (!isset($away_win_counts[$away_team_name])) {
				$away_win_counts[$away_team_name] = 0;
			}
			$away_win_counts[$away_team_name] += 1;
		}
		
		$first_pass = true;
		for ($i=1; $i <= $ipl->mTeams->num_teams; $i++) {
			if ($first_pass) {
				$first_pass = false;
                print ("<table border=1><tr><th colspan=3><h4>Home and Away wins</h4></th></tr>");
                print ("<tr><th>Team</th><th>Home Wins.......</th><th>Away Wins</th></tr>");
                $first_pass = false;
            }
			$team = $ipl->mTeams->arr[$i];
			
			if (!isset($home_win_counts[$team[1]])) $home_win_counts[$team[1]] = 0;
			if (!isset($away_win_counts[$team[1]])) $away_win_counts[$team[1]] = 0;

            $team_link = make_team_link($ipl, $team);
			if (($one_team_id < 0) || ($one_team_id > 0 && $one_team_id == $team[0])) {
				print ("<tr><td>" . $team_link . "</td><td>" . $home_win_counts[$team[1]] . "</td><td>" . $away_win_counts[$team[1]] . "</td></tr>");
			}
        }
        if ($first_pass == false) {
        	print ("</table>");
        	echo "<hr/>";
        }
    }
    
    function build_url($ipl, $admin_mode, $submit, $arr, $link_text) {
    	$this_url = $_SERVER['REQUEST_URI'];
		$use_url = $this_url;
		$url_cmd = "";
		$base_url = strtok($this_url, "&");
		$admin_url = "";
		$fromlink_url = "&fromlink=";
		
    	if ($admin_mode) $admin_url = "&admin=";

		if ($submit == "game_info") $url_cmd = "&game_info=&games=";
		if ($submit == "user_info") $url_cmd = "&user_info=&users=";
		if ($submit == "team_info") $url_cmd = "&team_info=&teams=";
		if ($submit == "date_info") $url_cmd = "&date_info=&games=";	// yes, use the date from game

		$use_url = $base_url . $fromlink_url . $url_cmd . $arr[0] . $admin_url;
		$link_href = "<a href=\"". $use_url . "\">" . $link_text . "</a>";
		return ($link_href);
    }

    if ($do_all_info == true) {
        view_all_info($ipl);
        $default_mode = false;
    }

    if ($show_points_table) {
        view_show_points_table($ipl);
        $default_mode = false;
    }

    if ($show_ipl_points_table) {
        view_ipl_points_table($ipl);
        view_all_completed_games($ipl);
        $default_mode = false;
    }

    if ($show_upcoming_games) {
        view_show_upcoming_games($ipl, 5);
        $default_mode = false;
    }

    if ($do_date_info) {
        $cur_sel_game = $ipl->mGames->get_by_id($selected_game_id);
        $use_date = $cur_sel_game[1];
        view_games_on_date($ipl, $use_date);
        $default_mode = false;
    }

    if ($do_game_info) {
        view_game_info($ipl, $selected_game_id);
        $default_mode = false;
	}

	if ($do_user_info) {
		$use_user = $ipl->mUsers->get_by_id($selected_user_id);
		print("<h5>" . $use_user[1] . "  YaMaVi Points:  " . $ipl->get_user_points($use_user) . "</h5><hr/>");
		view_user_win_loss_streaks($ipl);
		view_user_info($ipl, $selected_user_id);
        $default_mode = false;
	}

	if ($do_team_info) {
	    $ipl->show_team_info($selected_team_id);
        echo "<hr/>";
	    view_home_away_wins($ipl, $selected_team_id);
        $default_mode = false;
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
        $default_mode = false;
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
        $default_mode = false;
	}

    if ($show_stats) {
        $default_mode = false;
        view_home_away_wins($ipl);
    }
    
    if ($show_bonus) {
        view_bonus($ipl);
        $default_mode = false;
    }
    
    if ($fromlink) {
    	$default_mode = false;
    }
    
    if ($default_mode) {
        view_game_info($ipl, $selected_game_id);
        view_show_points_table($ipl);
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
