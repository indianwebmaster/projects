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
            background: url("<?= $mConfig->background_img ?>") no-repeat fixed center center;
        }

    </style>
        <title>YaMaVi League - <?= $mConfig->tournament_title ?> </title>
</head>
<body>
<div class="container">
<?php

    // Main Page
    require_once "MCricket.php";
    require_once "MFuncs.php";
    require_once "MChartsJS.php";

    require_once "inc_login.php";

    if ($is_logged_in == false  && $mConfig->view_only == false && $mConfig->show_login_screen) {
        if ($login_result == 'newuser') {
            newpaswd_form($_POST['user']);
        } else {
            login_form();
        }
    } else { // user is logged in
        if (($mConfig->view_only == false) && ($mConfig->show_login_screen)) $_SESSION["admin"] = true;
        if (isset($_SESSION['user'])) $login_user = $_SESSION['user'];
?>
    <table border="1" width="100%"><tr height="800em"><td align="left" valign="top">
<?php

    	MChartsJS::add_charts_js();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $get_post_array = $_POST;
        } else {
            $get_post_array = $_GET;
        }

    	$default_mode = true;
    	$do_reset = false;
    	if (isset($get_post_array["reset"])) $do_reset = true;

    	$admin_mode = false;
    	if (isset($get_post_array["admin"]) || isset($_POST["admin"]) || isset($_SESSION["admin"])) $admin_mode = true;

    	$fromlink = false;
    	if (isset($get_post_array["fromlink"]) || isset($_POST["fromlink"])) $fromlink = true;

        $do_all_info = false;
        if (isset($get_post_array["all_info"])) $do_all_info = true;

    	$do_game_info = false;
    	if (isset($get_post_array["game_info"])) $do_game_info = true;

    	$selected_game_id = -1;
    	if (isset($get_post_array["games"])) $selected_game_id = intval($get_post_array["games"]);


    	$do_user_info = false;
    	if (isset($get_post_array["user_info"])) $do_user_info = true;

    	$selected_user_id = 1;
    	if (isset($get_post_array["users"])) $selected_user_id = intval($get_post_array["users"]);


    	$do_date_info = false;
    	if (isset($get_post_array["date_info"])) $do_date_info = true;

    	$selected_date = "";
    	if (isset($get_post_array["date"])) $selected_date = $get_post_array["date"];


    	$do_team_info = false;
    	if (isset($get_post_array["team_info"])) $do_team_info = true;

    	$selected_team_id = 1;
    	if (isset($get_post_array["teams"])) $selected_team_id = intval($get_post_array["teams"]);


    	$do_place_bet = false;
    	if (isset($get_post_array["place_bet"])) $do_place_bet = true;

    	$set_winner = false;
    	if (isset($get_post_array["set_winner"])) $set_winner = true;

        $show_points_table = false;
        if (isset($get_post_array["points_table"])) $show_points_table = true;

        $show_tournament_points_table = false;
        if (isset($get_post_array["tournament_points_table"])) $show_tournament_points_table = true;

        $show_upcoming_games = false;
        if (isset($get_post_array["upcoming_games"])) $show_upcoming_games = true;

        $show_stats = false;
        if (isset($get_post_array["stats"])) $show_stats = true;
        
        $show_bonus = false;
        if (isset($get_post_array["bonus_info"])) $show_bonus = true;

        $show_graphs = false;
        if (isset($get_post_array["show_graphs"])) $show_graphs = true;

    	$cricket = new MCricket($mConfig->datadir);
    	$cricket->loadData();

        if ($do_reset == true) {
            // clear screen
            $selected_game_id = -1;
            $selected_user_id = 1;
            $selected_team_id = 1;
            $selected_date = "";
        }

        // This is to address if we have no 'login' screen
        if (isset($_SESSION['user']) == false) {
            $selected_user = $cricket->mUsers->arr[$selected_user_id];
            $first_last_name = explode(' ', $selected_user[1]);
            $login_user = strtolower($first_last_name[0]);
        }

        $dt_today = date('D M d');
        $dt = new DateTime($mConfig->bet_from_date, new DateTimeZone("America/New_York"));
        if  (strlen($selected_date) <= 0) {
            $selected_date = $dt_today;
        }

        if ($selected_game_id < 0) {
            $games_today = $cricket->mGames->get_by_date($selected_date);
            if (count($games_today) > 0) {
                $selected_game_id = $games_today[0][0];
            } else {
                $selected_game_id = 1;
            }
        }

    ?>

        <center><?= $mConfig->page_heading ?></center>
    	<table width="100%">
    		<form method="get" action="<?= $mConfig->main_url ?>">
    			<tr>
    				<td>Game</td>
    				<td>User</td>
    				<td>Team</td>
    <?php if ($admin_mode == true && is_superadmin($login_user, $mConfig->superadmins)) { ?>
    				<td>Bet Date</td>
    <?php } ?>
    			</tr>
    			<tr>
    				<td>
    					<select name="games">
    						<?php
    						$num_games = $cricket->mGames->num_games;
    						for ($i=$num_games; $i >= 1; $i--) {
    							$one_game = $cricket->mGames->arr[$i];

    							$game_id = $one_game[0];
    							$game_date = $one_game[1] . " " . $one_game[2]; 
    							$home_team = $one_game[3];
    							$away_team = $one_game[4];

                                $game_str = $cricket->mGames->get_game_date_string($one_game);

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
    						$num_users = $cricket->mUsers->num_users;
    						for ($i=1; $i <= $num_users; $i++) {
    							$one_user = $cricket->mUsers->arr[$i];

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
    						$num_teams = $cricket->mTeams->num_teams;
    						for ($i=1; $i <= $num_teams; $i++) {
    							$one_team = $cricket->mTeams->arr[$i];

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
    <?php if ($admin_mode == true && is_superadmin($login_user, $mConfig->superadmins)) { ?>
    					<select name="date">
    						<?php
    							for ($i=0; $i<$mConfig->bet_num_days; $i++) {
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
                        <input type="submit" class="btn btn-primary" name="tournament_points_table" value="Tournament Points">
                        <input type="submit" class="btn btn-primary" name="upcoming_games" value="Upcoming games">
                        <br/>
    					<input type="submit" class="btn btn-warning" name="game_info"  value="Game Info">
                        <input type="submit" class="btn btn-warning" name="date_info"  value="Game on Date">
                        <input type="submit" class="btn btn-danger" name="bonus_info"  value="Bonus Info">
                        <input type="submit" class="btn btn-danger" name="show_graphs"  value="Show Graphs">
                    </td>
                    <td>
    					<input type="submit" class="btn btn-warning" name="user_info" value="User Info"><br>
                    </td>
                    <td>
    					<input type="submit" class="btn btn-warning" name="team_info" value="Team Info">
    				</td>
    			</tr>
    <?php if ($admin_mode == true) { ?>
    			<tr>
    				<td colspan="2">
                        <input type="hidden" name="admin">
                        <?php if (($mConfig->view_only == false) && (is_superadmin($login_user, $mConfig->superadmins))) { ?>
    					   <input type="submit" class="btn btn-success" name="set_winner" value="Set Winner">
                        <?php } ?>
                        <?php if ($mConfig->view_only == false) { ?>
                        <input type="submit" class="btn btn-success" name="place_bet" value="Place bet">
                        <?php } ?>
                    </td>
                    <td>
                        <input type="submit" class="btn btn-dark" name="reset" value="Reset Page">
    				</td>
    			</tr>
            </form>
<!--
                <tr>
                    <td>
                        <input id="statsBtn" type="submit" name="stats" value="Show Statistics">
                    </td>
                </tr>
-->
    <?php } ?>
    			<tr>
                    <td colspan="2"></td>
    				<td>
                        <?php 
                            if (($mConfig->view_only == false) && ($mConfig->show_login_screen)) logout_form($user); 
                        ?>
                  </td>
                </tr>
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
        
        function write_log($string) {
			global $mConfig;
            $date_time_now = date("Y-m-d H:i:s");
            $log_string = $date_time_now . "| " . $string . "\n";

            file_put_contents ($mConfig->logf, $log_string, FILE_APPEND);
        }

        function make_user_link($cricket, $user) {
            global $admin_mode;
            if (count($user) > 0) {
        	   return build_url($cricket, $admin_mode, "user_info", $user, $user[1]); 
            } else {
                return("");
            }
        }
        
        function view_all_info($cricket) {
            view_show_points_table($cricket);
            echo "<table><tr><td>"; view_tournament_points_table($cricket); echo "</td><td>.......</td><td>"; view_home_away_wins($cricket); echo "</td></tr></table>";
            view_show_upcoming_games($cricket,5);
        }

        function graph_yamavi_points($cricket) {
        	$yamavi_graph = new MChartsJS("YaMaVi_Graph", "bar");
        	$yamavi_graph->add_canvas("100%");

            $points_table = $cricket->get_points_table();
            $win_percent_by_user = $cricket->get_win_percentage_by_user();
            $ui = 0;
            foreach ($points_table as $points_row) {
            	$user = $points_row[0];
            	$points = (int)$points_row[1];
                $points += $cricket->mUsers->get_add_points($user[1]);

        		$yamavi_graph->set_min_max(0,$points + 10);
            	$yamavi_graph->add_graph($user[1] . "(" . $win_percent_by_user[$ui] . "%)",[""], [$points]);
            	$ui++;
            }

            $yamavi_graph->draw_all_graphs();
        }

        function graph_yamavi_worm($cricket) {
        	$yamavi_worm = new MChartsJS("YaMaVi_Worm", "line");
        	$yamavi_worm->add_canvas("50%");

        	for ($ui=1; $ui<=$cricket->mUsers->num_users; $ui++) {
    	        $x_array = array(); 
    	        $y_array = array(); 
        		$user = $cricket->mUsers->arr[$ui];
        		$total_win_points = 0;
        		for ($gi=1; $gi<=$cricket->mGames->num_games; $gi++) {
        			$game = $cricket->mGames->arr[$gi];
        			if ($cricket->mGames->is_completed($game)) {
        				$total_win_points += $cricket->get_user_game_win_points($game, $user);
    	        		array_push($x_array, $gi);
    	        		array_push($y_array, $total_win_points);
        			}
        		}
                if ($cricket->mUsers->get_add_points($user[1]) > 0) {
                        $total_win_points += $cricket->mUsers->get_add_points($user[1]);
                        array_push($x_array, $gi);
                        array_push($y_array, $total_win_points);
                }
        		$yamavi_worm->add_graph($user[1],$x_array, $y_array);
        	}
            $yamavi_worm->draw_all_graphs();
        }

        function graph_yamavi_worm_for_user($cricket, $user_id) {
            $yamavi_worm = new MChartsJS("YaMaVi_Worm", "line");
            $yamavi_worm->add_canvas("50%");

            $x_array = array(); 
            $y_array = array(); 
            $user = $cricket->mUsers->arr[$user_id];
            $total_win_points = 0;
            for ($gi=1; $gi<=$cricket->mGames->num_games; $gi++) {
                $game = $cricket->mGames->arr[$gi];
                if ($cricket->mGames->is_completed($game)) {
                    $total_win_points += $cricket->get_user_game_win_points($game, $user);
                    array_push($x_array, $gi);
                    array_push($y_array, $total_win_points);
                }
            }
            if ($cricket->mUsers->get_add_points($user[1]) > 0) {
                    $total_win_points += $cricket->mUsers->get_add_points($user[1]);
                    array_push($x_array, $gi);
                    array_push($y_array, $total_win_points);
            }
            $yamavi_worm->add_graph($user[1],$x_array, $y_array);
            $yamavi_worm->draw_all_graphs();
        }

        function graph_teams_worm($cricket) {
        	$teams_worm = new MChartsJS("Teams_Worm", "line");
        	$teams_worm->add_canvas("80%");

        	for ($ti=1; $ti<=$cricket->mTeams->num_teams; $ti++) {
    	        $x_array = array(); 
    	        $y_array = array(); 
        		$team = $cricket->mTeams->arr[$ti];
        		$total_win_points = 0;
        		for ($gi=1; $gi<=$cricket->mGames->num_games; $gi++) {
        			$game = $cricket->mGames->arr[$gi];
        			if ( $cricket->mBets->valid_team($game, $team) && $cricket->mGames->is_completed($game) ) {
        				$winner_loser = $cricket->mGames->get_winner_loser_v2($gi);
        				if (MFuncs::substring($team[1],$winner_loser['winner']) || MFuncs::substring($winner_loser['winner'],$team[1])) {
        					$total_win_points += 2;
        				}
    	        		array_push($y_array, $total_win_points);
        			}
                    // DRAW NOBODY WON
                    if ( $cricket->mBets->valid_team($game, $team) && $gi == 49) {
                        $total_win_points += 1;
                        array_push($y_array, $total_win_points);
                    }
        		}
        		for ($i=1; $i < ($cricket->mGames->num_games / ($cricket->mTeams->num_teams/2)); $i++) {
        			array_push($x_array, $i);
        		}

        		$teams_worm->add_graph($team[1],$x_array, $y_array);
        	}
            $teams_worm->draw_all_graphs();
        }

        function graph_one_team_worm($cricket, $team_id) {
        	$team_worm = new MChartsJS("Teams_Worm", "line");
        	$team_worm->add_canvas("80%");

        	$team = $cricket->mTeams->arr[$team_id];
	        $x_array = array(); 
	        $y_array = array(); 
    		$total_win_points = 0;
    		for ($gi=1; $gi<=$cricket->mGames->num_games; $gi++) {
    			$game = $cricket->mGames->arr[$gi];
    			if ( $cricket->mBets->valid_team($game, $team) && $cricket->mGames->is_completed($game) ) {
    				$winner_loser = $cricket->mGames->get_winner_loser_v2($gi);
    				if (MFuncs::substring($team[1],$winner_loser['winner']) || MFuncs::substring($winner_loser['winner'],$team[1])) {
    					$total_win_points += 2;
    				}
	        		array_push($y_array, $total_win_points);
    			}
                // DRAW NOBODY WON
                if ($cricket->mBets->valid_team($game, $team) && $gi == 49) {
                    $total_win_points += 1;
                    array_push($y_array, $total_win_points);
                }
    		}
    		for ($i=1; $i < ($cricket->mGames->num_games / ($cricket->mTeams->num_teams/2)); $i++) {
    			array_push($x_array, $i);
    		}

    		$team_worm->add_graph($team[1],$x_array, $y_array);
            $team_worm->draw_all_graphs();
        }


        function view_show_points_table($cricket) {
            $user_games_won = array();
            $user_user_matches = array();
            $cricket->user_stats("Manoj Thakur", $user_games_won, $user_user_matches);

            $win_points_table = $cricket->get_points_table();
            $win_percent_by_user = $cricket->get_win_percentage_by_user();

            print("<table border=1><tr>");
            print("<th><h4>YaMaVi Points</h4></th><th>Total Points</th><th>Win %</th>");
            for ($i=1; $i<=$cricket->mUsers->num_users; $i++) {
                $one_user = $cricket->mUsers->arr[$i];
                print("<th>" . make_user_link($cricket, $one_user) . "</th>");
            }
            print("</tr>");

            $c_uidx = 0;
            foreach ($user_user_matches as $match_user) {
                $win_points = $win_points_table[$c_uidx][1];
                $c_user = $cricket->mUsers->get_by_id($c_uidx + 1);

                $win_points += $cricket->mUsers->get_add_points($c_user[1]);

                print("<tr><th>" . make_user_link($cricket,$c_user) . "</th>");
                print("<td>" . $win_points . "</td><td>". $win_percent_by_user[$c_uidx] ."%</td>");

                $w_user_id = 1;
                foreach ($match_user as $with_user_count) {
                    $w_user = $cricket->mUsers->get_by_id($w_user_id);
                    print("<td>$with_user_count</td>");
                    $w_user_id += 1;
                }
                print("</tr>");
                $c_uidx += 1;
            }
            print("</table>");
            echo "<hr/>";
        }

        function view_show_upcoming_games($cricket, $num_days) {
            $first_pass = true;
            foreach ($cricket->mGames->get_upcoming_games($num_days) as $game) {
                if ($first_pass == true) {
                    echo "<table><tr valign='top'><th colspan='2'>Upcoming games in next $num_days days</th></tr>";
                    echo "<tr valign='top'><th>Game Date</th><th>Home vs Away Team</th><th>Current Bets</th></tr>";
                    $first_pass = false;
                }
                $bets_str = "";
                foreach ($cricket->get_bets_on_game($game[0]) as $one_bet) {
                    $bets_str .= ($cricket->mUsers->get_short_name($one_bet[0]) . "-" . $one_bet[1] . "-" . $one_bet[2] . "<br>");
                }
                $game_str = $cricket->mGames->get_game_string($game);
                $game_link = make_game_link($cricket, $game);
                echo "<tr valign='top'><td>". $game[1] . "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td><td>" . $game_link . "</td><td>$bets_str</td></tr>";
            }
            if ($first_pass == false) {
                echo "</table>";
            }
            echo "<hr/>";
        }

        function view_games_on_date($cricket, $on_date) {
            $first_pass = true;
            foreach ($cricket->get_games_on_date($on_date) as $game) {
                if ($first_pass == true) {
                    echo "<table><tr><th colspan='2'>Games on " . $game[1] . "</th></tr>";
                    echo "<tr><th>Game Date</th><th>Home vs Away Team</th></tr>";
                    $first_pass = false;
                }
                $game_str = $cricket->mGames->get_game_string($game);
                echo "<tr><td>". $game[1] . "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td><td>" . $game_str . "</td></tr>";
            }
            if ($first_pass == false) {
                echo "</table>";
            }
            echo "<hr/>";
        }

        function view_game_info($cricket, $game_id) {
            $game = $cricket->mGames->get_by_id($game_id);
            $first_pass = true;
            $completed_game = false;
            $game_date = $game[1] . ' ' . $game[2];
            $game_str = $cricket->mGames->get_game_string($game);
            echo "<table><tr><th colspan='2'>" . $game_str . " (" . $game_date . ")</th></tr>";
            foreach ($cricket->get_winning_team_users($game_id) as $team_users) {
                if ($first_pass == true) {
                    echo "<tr><th>Winning Team</th><th>Winning Users</th></tr>";
                    $first_pass = false;
                    $completed_game = true;
                }
                $team = $cricket->mTeams->get_by_name($team_users[0]);
                $team_link = make_team_link($cricket, $team);
                echo "<tr><td>" . $team_link . "&nbsp;&nbsp;&nbsp;</td>";
                for ($i=1; $i<count($team_users); $i++){
                    if ($i == 1) {
                        echo "<td>";
                    }
                    $user_link = make_user_link($cricket,$team_users[$i]); 
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
                foreach ($cricket->get_bets_on_game($game_id) as $one_bet) {
                    if ($first_pass == true) {
                        echo "<table><tr><th colspan='3'>Current Bets</th></tr>";
                        echo "<tr><th>Player................</th><th>Team.....</th><th>Bet Date</th></tr>";
                        $first_pass = false;
                    }
                    $user = $cricket->mUsers->get_by_name($one_bet[0]);
                    $user_link = make_user_link($cricket, $user);
                    
                    $team = $cricket->mTeams->get_by_short_name($one_bet[1]);
                    $team_link = make_team_link($cricket, $team, true);
                    echo "<tr><td>" . $user_link . "</td><td>" . $team_link . "</td><td>" . $one_bet[2] . "</td></tr>";
                }
                if ($first_pass == false) {
                    echo "</table>";
                    echo "<hr/>";
                }
            }
        }
        
        function make_game_link($cricket, $game) {
        	$game_str = $cricket->mGames->get_game_string($game);
        	$game_link = build_url($cricket, false, "game_info", $game, $game_str); 
        	return ($game_link);
        }

        function make_team_link($cricket, $team, $short=false) {
        	if ($short) {
        		return build_url($cricket, false, "team_info", $team, $team[2]);
        	} else {
        		return build_url($cricket, false, "team_info", $team, $team[1]);
        	}
        }

        function view_user_info($cricket, $input_user_id) {
            $win_percent_by_user = $cricket->get_win_percentage_by_user();
            $use_user = $cricket->mUsers->get_by_id($input_user_id);
            $uidx = $use_user[0] - 1;
            
            $total_win_points = 0;
            $win_points = 0;
            $first_pass = true;
            for ($i=$cricket->mBets->num_bets; $i>=1; $i--) {
                $one_bet = $cricket->mBets->arr[$i];
                $num_cols = count($one_bet);
                for ($j = 2; $j < $num_cols; $j += 3) {
                    $username = $one_bet[$j];
                    if (MFuncs::substring($use_user[1],$username,true) == true) {
                        $game_id = $one_bet[1];
                        $team = $cricket->mTeams->get_by_name($one_bet[$j + 1]);
                        $bet_date = $one_bet[$j + 2];
                        $game = $cricket->mGames->get_by_id($game_id);
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
                        $win_points = $cricket->get_user_game_win_points($game, $use_user);
                        $total_win_points += $win_points;
                        $game_link = make_game_link($cricket, $game);
                        $team_link = make_team_link($cricket, $team, true);
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


        function view_tournament_points_table($cricket) {
            $first_pass = true;
            foreach ($cricket->get_sorted_tournament_points() as $point) {
                $team = $point[0];
                // DRAW NOBODY WON
                //if ($team[2] == 'RCB' || $team[2] == 'RR' ) $point[1] += 1;

                $team_link = make_team_link($cricket, $team);
                if ($first_pass) {
                    print("<table border=1><tr><th colspan=4><h4>Tournament Points table</h4></th><tr>");
                    print("<tr><th>Team</th><th>Points</th><th>Wins</th><th>Losses</th></tr>");
                    $first_pass = false;
                }
                print ("<tr><td>$team_link</td><td>$point[1]</td><td>$point[2]</td><td>$point[3]</td></tr>"); 
            }
            if ($first_pass == false) {
                print ("</table>");
            }
            echo "<hr/>";
        }
        
        function view_all_completed_games($cricket) {
            $first_pass = true;
            for ($i = $cricket->mGames->num_games; $i >= 1; $i--) {
                $game = $cricket->mGames->arr[$i];
                if ($cricket->mGames->is_completed($game)) {
                    if ($first_pass) {
                        print ("<table><tr><th colspan=3>All completed games</th></tr>");
                        print ("<tr><th>##..</th><th>Game Date......................</th><th>Home vs Away</th></tr>");
                        $first_pass = false;
                    }
                    $game_date = $game[1];
                    $home_team = $game[3];
                    $away_team = $game[4];
                    $winning_team = $game[7];
                    $game_str = $cricket->mGames->get_game_string($game);
                    $game_link = make_game_link($cricket, $game);
                    print("<tr><td>$i</td><td>$game_date</td><td>$game_link</td></tr>");
                }
            }
            if ($first_pass == false) {
                print("</table>");
            }
            echo "<hr/>";
        }
        
        function view_user_win_loss_streaks($cricket) {
        	$first_pass = true;
        	foreach ($cricket->highest_streak_by_user() as $user_win_loss_streak) {
        		if ($first_pass) {
    				print ("<table><tr><th colspan=3>Longest Win/Loss streak</th></tr>");
    				print ("<tr><th>Player ............... </th><th>Win Streak</th><th>Loss Streak</th></tr>");
        			$first_pass = false;
        		}
        		$user_link = make_user_link($cricket,$user_win_loss_streak[0]);
    //    		$user_link = make_link("&user_info=User Info&users=".$user_win_loss_streak[0][0], $user_win_loss_streak[0][1]);
        		print ("<tr><td>" . $user_link . "</td><td>" . $user_win_loss_streak[1] . "</td><td>" . $user_win_loss_streak[2] . "</td></tr>");
        	}
        	if ($first_pass == false) {
        		print ("</table>");
        	}
        	echo "<hr/>";		
        }
        
        function view_bonus($cricket) {
        	print ("<table border=1><tr><td colspan=2>");
        	print ("<h3>Bonus 1:</h3>Choose your <b>top four teams</b> that make the playoffs. The teams MUST be in the order of their rankings<br/>");
        	print ("Submit your choice over Whats'app <span class='bg-primary text-white'>by midnight Sun 18th Oct</span>. Make any changes before then - last entry before deadline will be used<br/>");
        	print ("..... .: Points = 2 -- If your team is in the top four AND in the rank you predicted<br/>");
        	print ("..... .: Points = 1 -- If your team is in the top four BUT NOT in the rank you predicted<br/>");
        	print ("..... .: Points = 0 -- If your team is NOT in the top 4<p/>");
            $first_pass = true;
            for ($i = 1; $i <= $cricket->mBonus->num_items; $i++) {
                if ($first_pass) {
                    print ("<table>");
                    print ("<tr><th>Player ............. </th><th>Team 1</th><th>Team 2</th><th>Team 3</th><th>Team 4</th><th>Last Submitted On</th></tr>");
                    $first_pass = false;
                }
                $bonus = $cricket->mBonus->arr[$i];
                $user = $cricket->mUsers->get_by_name($bonus[1]);
                $user_link = make_user_link($cricket,$user);
                $team_link = array();
                for ($j = 0; $j < 4; $j++) {
                    $team = $cricket->mTeams->get_by_short_name($bonus[$j + 2]);
                    array_push($team_link,make_team_link($cricket, $team, true));
                }
                print ("<tr><td>" . $user_link . "</td><td>" . $team_link[0] . "</td><td>" . $team_link[1] . "</td><td>" . $team_link[2] . "</td><td>" . $team_link[3] . "</td><td>" . $bonus[6] . "</tr>");
            }
        	if ($first_pass == false) {
        		print ("</table>");
        	}
        	
        	print ("</td></tr><tr><td>");
        	print ("<h3>Bonus 2</h3>Choose your <b>top two teams</b> to play in the finals. The teams MUST be in the order of their rankings WHEN THEY ENTERED THE PLAYOFFS<br/>");
        	print ("Submit your choice over Whats'app <span class='bg-primary text-white'>by midnight Sun 25th Oct</span>. Make any changes before then - last entry before deadline will be used<br/>");
        	print ("..... .: Points = 2 -- For each team in top two AND in predicted rank<br/>");
        	print ("..... .: Points = 1 -- For each team in top two BUT NOT in predicted rank<br/>");
        	print ("..... .: Points = 0 -- If your team is NOT in the top two<p/>");
        	print ("</td><td>");
        	print ("<h3>Bonus 3:</h3>Choose your <b>Winning team</b><br/>");
        	print ("Submit your choice over Whats'app <span class='bg-primary text-white'>by midnight Sun 25th Oct</span>. Make any changes before then - last entry before deadline will be used<br/>");
        	print ("..... .: Points = 4 -- Correct winner predicted<br/>");
        	print ("..... .: Points = 0 -- If your team is NOT the winning team<p/>");
        	print ("</td></tr>");
        	print ("<tr><td colspan=2>");
            $first_pass = true;
            for ($i = 1; $i <= $cricket->mBonus2->num_items; $i++) {
                if ($first_pass) {
                    print ("<table>");
                    print ("<tr><th>Player ____________ </th><th>Finals Team 1______</th><th>Finals Team 2______</th><th>Winning Team______</th><th>Last Submitted On</th></tr>");
                    $first_pass = false;
                }
                $bonus = $cricket->mBonus2->arr[$i];
                $user = $cricket->mUsers->get_by_name($bonus[1]);
                $user_link = make_user_link($cricket,$user);
                $team_link = array();
                for ($j = 0; $j < 3; $j++) {
                    $team = $cricket->mTeams->get_by_short_name($bonus[$j + 2]);
                    array_push($team_link,make_team_link($cricket, $team, true));
                }
                print ("<tr><td>" . $user_link . "</td><td>" . $team_link[0] . "</td><td>" . $team_link[1] . "</td><td>" . $team_link[2] . "</td><td>" . $bonus[6] . "</tr>");
            }
        	if ($first_pass == false) {
        		print ("</table>");
        	}
        	print ("</td></tr></table>");
        }

        function view_show_graphs($cricket) {
        	print ("<table width=\"80%\">");
        	print ("<tr><td>YaMaVi Worm"); graph_yamavi_worm($cricket); print("</td><td>YaMaVi Points"); graph_yamavi_points($cricket); print("</td></tr>");
        	print ("<tr><td colspan=2>Tournament Points Table"); graph_teams_worm($cricket); print("</td></tr>");
        	print ("</table>");
        }

        function view_home_away_wins($cricket, $one_team_id = -1) {
        	$home_away_wins = $cricket->mGames->get_home_away_wins();
        	$home_wins = $home_away_wins['home_wins'];
        	$away_wins = $home_away_wins['away_wins'];
        	$home_win_counts = array();
        	foreach ($home_wins as $home_team_name) {
        		$team = $cricket->mTeams->get_by_name($home_team_name);
    			if (!isset($home_win_counts[$home_team_name])) {
    				$home_win_counts[$home_team_name] = 0;
    			}
    			$home_win_counts[$home_team_name] += 1;
    		}
        	$away_win_counts = array();
        	foreach ($away_wins as $away_team_name) {
        		$team = $cricket->mTeams->get_by_name($away_team_name);
    			if (!isset($away_win_counts[$away_team_name])) {
    				$away_win_counts[$away_team_name] = 0;
    			}
    			$away_win_counts[$away_team_name] += 1;
    		}
    		
    		$first_pass = true;
    		for ($i=1; $i <= $cricket->mTeams->num_teams; $i++) {
    			if ($first_pass) {
    				$first_pass = false;
                    print ("<table border=1><tr><th colspan=3><h4>Home and Away wins</h4></th></tr>");
                    print ("<tr><th>Team</th><th>Home Wins.......</th><th>Away Wins</th></tr>");
                    $first_pass = false;
                }
    			$team = $cricket->mTeams->arr[$i];
    			
    			if (!isset($home_win_counts[$team[1]])) $home_win_counts[$team[1]] = 0;
    			if (!isset($away_win_counts[$team[1]])) $away_win_counts[$team[1]] = 0;

                $team_link = make_team_link($cricket, $team);
    			if (($one_team_id < 0) || ($one_team_id > 0 && $one_team_id == $team[0])) {
    				print ("<tr><td>" . $team_link . "</td><td>" . $home_win_counts[$team[1]] . "</td><td>" . $away_win_counts[$team[1]] . "</td></tr>");
    			}
            }
            if ($first_pass == false) {
            	print ("</table>");
            	echo "<hr/>";
            }
        }
        
        function build_url($cricket, $admin_mode, $submit, $arr, $link_text) {
			$base_url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'] . '?nop=';

            $admin_url = "";
        	if ($admin_mode) $admin_url = "&admin=";

			$url_cmd = "";
    		if ($submit == "game_info") $url_cmd = "&game_info=&games=";
    		if ($submit == "user_info") $url_cmd = "&user_info=&users=";
    		if ($submit == "team_info") $url_cmd = "&team_info=&teams=";
    		if ($submit == "date_info") $url_cmd = "&date_info=&games=";	// yes, use the date from game

    		$use_url = $base_url . $url_cmd . $arr[0] . $admin_url;
    		$link_href = "<a href=\"". $use_url . "\">" . $link_text . "</a>";
    		return ($link_href);
        }

        if ($do_all_info == true) {
            view_all_info($cricket);
            $default_mode = false;
        }

        if ($show_points_table) {
            view_show_points_table($cricket);
            print("<table width='100%'><tr><td>"); graph_yamavi_worm($cricket);print("</td>");
            print("<td>"); graph_yamavi_points($cricket);print("</td></tr></table>");
            $default_mode = false;
        }

        if ($show_tournament_points_table) {
            view_tournament_points_table($cricket);
            view_all_completed_games($cricket);
            $default_mode = false;
        }

        if ($show_upcoming_games) {
            view_show_upcoming_games($cricket, 5);
            $default_mode = false;
        }

        if ($do_date_info) {
            $cur_sel_game = $cricket->mGames->get_by_id($selected_game_id);
            $use_date = $cur_sel_game[1];
            view_games_on_date($cricket, $use_date);
            $default_mode = false;
        }

        if ($do_game_info) {
            view_game_info($cricket, $selected_game_id);
            $default_mode = false;
    	}

    	if ($do_user_info) {
    		$use_user = $cricket->mUsers->get_by_id($selected_user_id);
    		print("<h5>" . $use_user[1] . "  YaMaVi Points:  " . $cricket->get_user_points($use_user) . "</h5><hr/>");
    		print ('<table width="100%"><tr><td>'); view_user_win_loss_streaks($cricket); print ('</td>');
            print ('<td>'); graph_yamavi_worm_for_user($cricket,$selected_user_id); print('</td></tr></table>');

    		view_user_info($cricket, $selected_user_id);
            $default_mode = false;
    	}

    	if ($do_team_info) {
            echo "<table><tr><td>";
    	       $cricket->show_team_info($selected_team_id);
    	    echo "</td><td>";
    	    	graph_one_team_worm($cricket, $selected_team_id);
    	    echo "</td></tr></table>";
            if ($mConfig->show_home_away_wins) view_home_away_wins($cricket, $selected_team_id);

            $default_mode = false;
    	}

    	if($do_place_bet) {
    		$use_user = $cricket->mUsers->get_by_id($selected_user_id); 
    		$use_game = $cricket->mGames->get_by_id($selected_game_id); 
    		$use_team = $cricket->mTeams->get_by_id($selected_team_id);
            if (MFuncs::substring($use_user[1], $login_user, true) == false) {
                echo "<p><b>Invalid user.</b>You cannot place a bet for a another user</p>";
            } else {
                if ($cricket->mBets->place_bet($use_game, $use_user, $use_team, $selected_date) == true) {
                    $cricket->save();
                    $cricket->loadData();
                    echo "<h2>Bet Placed on $selected_date</h2>";
                    write_log($use_user[1] . " Bet On Team " . $use_team[2]);
                    view_game_info($cricket, $use_game[0]);
                } else {
                    echo "<p><b>Invalid selection.</b> Select one of the two teams playing in this game</p>";
                }
            }
            $default_mode = false;
    	}

    	if($set_winner) {
    		$use_game = $cricket->mGames->get_by_id($selected_game_id); 
    		$use_team = $cricket->mTeams->get_by_id($selected_team_id);
    		$err_string="";

    		if ($cricket->set_winner($use_game, $use_team, $err_string) == true) {
                $cricket->save();
                $cricket->loadData();
                echo "<h2>Winner Chosen</h2>";
                write_log($use_user[1] . " Set Winner as " . $use_team[2] . " for game " . $cricket->mGames->get_game_string($use_game));
                view_game_info($cricket, $use_game[0]);
            } else {
                echo "<p><b>Invalid selection</b>. $err_string</p>";
            }
            $default_mode = false;
    	}

        if ($show_stats) {
            $default_mode = false;
            graph_yamavi_points($cricket);
            graph_yamavi_worm($cricket);
            graph_teams_worm($cricket);
        }
        
        if ($show_bonus) {
            view_bonus($cricket);
            $default_mode = false;
        }
        
        if ($show_graphs) {
            view_show_graphs($cricket);
            $default_mode = false;
        }
        
        if ($fromlink) {
        	$default_mode = false;
        }
        
        if ($default_mode) {
			if ($mConfig->view_only == true) {
				print ("Tournament is complete.<h3>YaMaVi Winner:" . $mConfig->yamavi_winner . "</h3><br><br>");
			} else {
				view_game_info($cricket, $selected_game_id);
			}
            view_show_points_table($cricket);
        }

    ?>
    </td></tr></table>
    </div>
    <br><center><a href="<?= $mConfig->logf ?>" target="_blank">@</a></center>
    </body>
<?php
    }   // end user is logged in
?><!-- Optional JavaScript -->
<!-- jQuery first, then Popper.js, then Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
</html>
