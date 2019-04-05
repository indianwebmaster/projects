<?php
require_once "MUsers.php";
require_once "MTeams.php";
require_once "MGames.php";
require_once "MBets.php";

class MIpl {
	private $year = 2019;
	private $users_filepath = "users.dat";
	private $teams_filepath = "teams.dat";
	private $games_filepath = "games.dat";
	private $bets_filepath = "bets.dat";
	public $mUsers = null;
	public $mTeams = null;
	public $mGames = null;
	public $mBets = null;

	public function __construct($year) {
		$this->year = $year;
		$this->users_filepath = "ipl" . $year . DIRECTORY_SEPARATOR  . "users.dat";
		$this->teams_filepath = "ipl" . $year . DIRECTORY_SEPARATOR  . "teams.dat";
		$this->games_filepath = "ipl" . $year . DIRECTORY_SEPARATOR  . "games.dat";
		$this->bets_filepath = "ipl" . $year . DIRECTORY_SEPARATOR  . "bets.dat";
	}

	public function loadData() {
		$this->mUsers = new MUsers();
		$this->mTeams = new MTeams();
		$this->mGames = new MGames();
		$this->mBets = new MBets();
		$this->mUsers->load($this->users_filepath);
		$this->mTeams->load($this->teams_filepath);
		$this->mGames->load($this->games_filepath);
		$this->mBets->load($this->bets_filepath);
	}

	public function save() {
		$this->mUsers->save($this->users_filepath);
		$this->mTeams->save($this->teams_filepath);
		$this->mGames->save($this->games_filepath);
		$this->mBets->save($this->bets_filepath);
	}

	public function set_winner($game, $team, &$err_string) {
        // Are teams specified are valid playing in this game?
        if ($this->mBets->valid_team($game, $team) == false) {
            // No, just return
            $err_string = "Choose a team from the two playing this game";
            return false;
        }
        if ($this->mGames->is_a_future_game($game) == true) {
            // Yes, just return
            $err_string = "This game is in the future";
            return false;
        }
        $use_game = &$this->mGames->arr[$game[0]];
        $use_game[5] = "Completed";
        if (count($use_game) < 6) {
            array_push($use_game, $team[1]);
        } else {
            $use_game[6] = $team[1];
        }
        $winning_users = array();
        $winning_points = array();
        $this->mBets->get_winning_users($use_game, $team, $winning_users, $winning_points);
        $col = 7;
        foreach ($winning_users as $winning_user) {
            if (count($use_game) < $col) {
                array_push($use_game, $winning_user);
            } else {
                $use_game[$col] = $winning_user;
            }
            $col++;
        }
        $num_cols = count($use_game);
        while ($col < $num_cols) {
            array_pop($use_game);   // remove any old winning users
            $col++;
        }
		return true;
	}

    public function show_games_info($input_game_id) {
        $use_game = $this->mGames->get_by_id($input_game_id);
        $game_str = $this->mGames->get_game_string($use_game);
        echo "<b>" . $game_str . "</b><br>";
        if ($use_game[5] == "Completed") {
            echo "<b>Game Completed</b><br>";
            $num_cols = count($use_game);
            if ($num_cols > 6) echo "Winning Team: $use_game[6]<br>";
            if ($num_cols > 7) {
                echo "Winning Users: ";
                for ($i=7; $i<count($use_game); $i++) {
                    echo "$use_game[$i]  ";
                }
                echo "<br>";
            } else {
                echo "Nobody predicted the winner correctly<br>";
            }
        } else {
            echo "<b>Current Bets</b>";
            $num_bets = $this->mBets->num_bets;
            for ($i=1; $i<=$num_bets; $i++) {
                $one_bet = $this->mBets->arr[$i];
                if ($one_bet[1] == $input_game_id) {
                    $num_cols = count($one_bet);
                    for ($j=2; $j<$num_cols; $j += 3) {
                        $bet_str = $one_bet[$j] . " bet " . $one_bet[$j+1] . " on " . $one_bet[$j+2];
                        echo "$bet_str<br>";
                    }
                }
            }
        }
    }

	public function get_winning_team_users($game_id)
    {
        $winning_details = array();
        $idx = 0;
        $game = $this->mGames->arr[$game_id];
        if ($game[5] == "Completed") {
            array_push($winning_details, array());
            $num_cols = count($game);
            if ($num_cols > 6) {
                array_push($winning_details[$idx], $game[6]);   // Winning team
                for ($i = 7; $i < $num_cols; $i++) {
                    $user = $this->mUsers->get_by_name($game[$i]);
                    array_push($winning_details[$idx], $user);
                }
            }
            $idx++;
        }
        return($winning_details);
    }

    public function show_team_info($input_team_id) {
        $use_team = $this->mTeams->get_by_id($input_team_id);
        $first_pass = true;
        for ($i=1; $i<=$this->mGames->num_games; $i++) {
            $game = $this->mGames->arr[$i];

            $home_team = $game[3];
            $away_team = $game[4];
            $game_date = $game[1] . "  " .  $game[2];
            if ((MFuncs::substring($home_team,$use_team[1]) == true) || (MFuncs::substring($away_team,$use_team[1]) == true)) {
                if ($first_pass == true) {
                    echo "<table width=\"100%\"><tr><th colspan='2'>Team Info for " . $use_team[1] . "</th></tr>";
                    echo "<tr><th>Game Date</th><th>Home Team vs Away Team</th></tr>";
                    $first_pass = false;
                }
                $game_str = $this->mGames->get_game_string($game);
                echo "<tr><td>$game_date</td><td>$game_str</td></tr>";
            }
        }
        if ($first_pass == false) {
            echo "</table>";
        }
    }

    public function get_games_on_date($input_date) {
	    $games_on_date = array();
        for ($i=1; $i<=$this->mGames->num_games; $i++) {
            $game = $this->mGames->arr[$i];
            if (MFuncs::substring($game[1], $input_date) == true) {
                array_push($games_on_date,$game);
            }
        }
        return $games_on_date;
    }

    public function get_win_points_table() {
	    $points_table = array();
	    for ($uid=1; $uid <= $this->mUsers->num_users; $uid++) {
            $n_user = $uid - 1;
	        $one_user = $this->mUsers->arr[$uid];
            $first_pass = true;
            for ($gid = 1; $gid <= $this->mGames->num_games; $gid++) {
                $one_game = $this->mGames->arr[$gid];
                $winning_points = array();
                $winning_users = array();
                $num_cols = count($one_game);
                if ($num_cols > 5 && $one_game[5] == "Completed") {
                    if ($num_cols > 6) {
                        $winning_team = $this->mTeams->get_by_name($one_game[6]);
                        $this->mBets->get_winning_users($one_game, $winning_team, $winning_users, $winning_points);
                    }
                }
                if (count($winning_users) > 0) {
                    $i = 0;
                    foreach ($winning_users as $win_user) {
                        if ($win_user == $one_user[1]) {
                            if ($first_pass == true) {
                                array_push($points_table,array());
                                array_push($points_table[$n_user], $one_user);
                                $first_pass = false;
                            }
                            array_push($points_table[$n_user], $winning_points[$i]);
                        }
                        $i++;
                    }
                }
            }
            $n_user++;
        }
	    return ($points_table);
    }

    public function user_stats($username, &$user_games_won, &$user_user_matches) {
        $user_games_won = array_fill(0, $this->mUsers->num_users, array_fill(0,1,"Replace_With_mUser"));
        for ($i=1; $i <= $this->mGames->num_games; $i++) {
            $game = $this->mGames->arr[$i];
            if ($game[5] == "Completed") {
                $num_cols = count($game);
                for ($j=7; $j<$num_cols; $j++) {
                    $user = $this->mUsers->get_by_name($game[$j]);
                    $user_games_won[$user[0] - 1][0] = intval($user[0]);
                    array_push($user_games_won[$user[0] - 1], $game[0]);
                }
            }
        }

        $user_user_matches = array_fill(0, $this->mUsers->num_users, array_fill(0, $this->mUsers->num_users, 0));
        foreach ($user_games_won as $compare_user) {
            $cidx = $compare_user[0]-1;
            foreach ($user_games_won as $with_user) {
                $widx = $with_user[0]-1;
                $cn_cols = count($compare_user);
                $wn_cols = count($with_user);
                for ($ci=1; $ci<$cn_cols; $ci++) {
                    for ($wi=1; $wi<$wn_cols; $wi++) {
                        if ($compare_user[$ci] == $with_user[$wi]) {
                            $user_user_matches[$cidx][$widx] += 1;
                        }
                    }
                }
            }
        }
    }

    public function get_points_table() {
        $points_table = $this->get_win_points_table();
        $win_points_table = array();
        $idx = 0;
        foreach ($points_table as $one_points_row) {
            $total_points = 0;
            $user = $one_points_row[0];
            $n_cols = count($one_points_row);
            for ($i = 1; $i < $n_cols; $i++) {
                $total_points += intval($one_points_row[$i]);
            }
            array_push($win_points_table,array());
            array_push($win_points_table[$idx],$user);
            array_push($win_points_table[$idx],$total_points);
            $idx++;
        }
        return ($win_points_table);
    }
    public function get_bets_on_game($game_id, $short_name=true) {
        $bets_on_game = array();
        $idx = 0;
        for ($i=1; $i <= $this->mBets->num_bets; $i++) {
            $one_bet = $this->mBets->arr[$i];
            if ($one_bet[1] == $game_id) {
                $num_cols = count($one_bet);
                for ($j=2; $j < $num_cols; $j +=3) {
                    $bet_team = $this->mTeams->get_by_name($one_bet[$j + 1]);
                    array_push($bets_on_game,array());
                    array_push($bets_on_game[$idx],$one_bet[$j]);
                    if ($short_name == true) {
                        array_push($bets_on_game[$idx], $bet_team[2]);
                    } else {
                        array_push($bets_on_game[$idx], $bet_team[1]);
                    }
                    array_push($bets_on_game[$idx],$one_bet[$j+2]);
                    $idx++;
                }
            }
        }
        return ($bets_on_game);
    }

    public function get_ipl_points() {
        $ipl_points = array_fill(0, $this->mTeams->num_teams, array_fill(0,3,0));
        $winners_losers = $this->mGames->get_winners_losers();
        foreach ($winners_losers as $game_winner_loser) {
            $winning_team = $this->mTeams->get_by_name($game_winner_loser[1]);
            $losing_team = $this->mTeams->get_by_name($game_winner_loser[2]);
            $w_idx = $winning_team[0] - 1;
            $l_idx = $losing_team[0] - 1;
            
            $ipl_points[$w_idx][0] += 1;
            $ipl_points[$w_idx][1] += 2;

            $ipl_points[$l_idx][2] += 1;
        }
        return ($ipl_points);
    }

    // num wins by user / num completed games
    public function get_win_percentage_by_user() {
        $num_games_completed = 0;
        $num_user_wins = array_fill(0, $this->mUsers->num_users, 0);
        $win_percent_by_user = array_fill(0, $this->mUsers->num_users, 0);
        for ($i = 1; $i <= $this->mGames->num_games; $i++) {
            $game = $this->mGames->arr[$i];
            if ($this->mGames->is_completed($game)) {
                $num_games_completed += 1;
                $winning_usernames = $this->mGames->get_winning_usernames($game);
                foreach ($winning_usernames as $winning_username) {
                    $user = $this->mUsers->get_by_name($winning_username);
                    $uidx = $user[0] - 1;
                    $num_user_wins[$uidx] += 1;
                }
            }
        }
        $uidx = 0;
        foreach ($num_user_wins as $one_user_wins) {
            $win_percent = ($one_user_wins * 100) / $num_games_completed;
            $win_percent_by_user[$uidx] = number_format($win_percent, 2);
            $uidx++;
        }
        return ($win_percent_by_user);
    }

    public function get_user_game_win_points($game, $user) {
    	$win_points = 0;
    	foreach ($this->mGames->get_winning_usernames($game) as $winning_username) {
    		if ($winning_username == $user[1]) {
    			$win_points = intval($this->mBets->get_winning_points($game, $user));
    		}
    	}
    	return ($win_points);
    }
    
    public function highest_streak_by_user() {
    	$user_highest_lowest = array_fill(0, $this->mUsers->num_users, array_fill(0, 3, ""));
    	for ($ui=1; $ui <= $this->mUsers->num_users; $ui++) {
    		$uidx = ($ui - 1);
    		$highest_winning_streak = 0;
    		$highest_losing_streak = 0;
    		$winning_streak = 0;
    		$losing_streak = 0;
    		$last_win_points = -1;
    		$first_pass = true;
    		$user = $this->mUsers->arr[$ui];
    		for ($gi=1; $gi <= $this->mGames->num_games; $gi++) {
    			$game = $this->mGames->arr[$gi];
    			if ($this->mGames->is_completed($game)) {
					$win_points = $this->get_user_game_win_points($game, $user);
					if ($win_points == 0) {
						$losing_streak += 1;
					} else {
						$winning_streak += 1;
					}
					
					if ($winning_streak > $highest_winning_streak) $highest_winning_streak = $winning_streak;
					if ($losing_streak > $highest_losing_streak) $highest_losing_streak = $losing_streak;

					$game_str = $this->mGames->get_game_string($game);

					if (($first_pass == true) || ($win_points == $last_win_points)) {
						$first_pass = false;
					} else {
						$winning_streak = 1;
						$losing_streak = 1;
					}
					$last_win_points = $win_points;
    			}
    		}
			if ($winning_streak > $highest_winning_streak) $highest_winning_streak = $winning_streak;
			if ($losing_streak > $highest_losing_streak) $highest_losing_streak = $losing_streak;
			
			$user_highest_lowest[$uidx][0] = $user;
			$user_highest_lowest[$uidx][1] = $highest_winning_streak;
			$user_highest_lowest[$uidx][2] = $highest_losing_streak;
    	}
    	return ($user_highest_lowest);
    }
}
/*
$mIpl = new MIpl(2019);
$mIpl->loadData();
$mIpl->mBets->place_bet($mIpl->mGames->arr[2], $mIpl->mUsers->arr[1], $mIpl->mTeams->arr[2], "4/21/2019");
$mIpl->mBets->save("ipl2019\\saved_bets.dat");
$mIpl->set_winner($mIpl->mGames->arr[2],$mIpl->mTeams->arr[2]);
$mIpl->mGames->save("ipl2019\\saved_games.dat");
*/
?>
