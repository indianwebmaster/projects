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
        $this->mBets->get_winning_users($use_game, $team, $winning_users);

        $col = 7;
        foreach ($winning_users as $winning_user) {
            if (count($use_game) < $col) {
                array_push($use_game, $winning_user);
            } else {
                $use_game[$col] = $winning_user;
            }
            $col++;
        }
		return true;
	}

	public function show_games_info($input_game_id) {
        $use_game = $this->mGames->get_by_id($input_game_id);
        echo "<h3>Game Info - " . $use_game[3] . " ----- vs ----- " . $use_game[4] . "</b></h3>";
        if ($use_game[5] == "Completed") {
            echo "<h4>Game Completed</h4>";
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
            echo "<h4>Current Bets</h4>";
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

    public function show_user_info($input_user_id) {
        $use_user = $this->mUsers->get_by_id($input_user_id);
        echo "<h3>User Info for $use_user[1]</h3>";
        $first_pass = true;
        for ($i=1; $i<=$this->mGames->num_games; $i++) {
            $use_game = $this->mGames->arr[$i];
            $num_cols = count($use_game);
            if ($num_cols > 7) {
                // Means we have winning user entries
                for ($j=7; $j < $num_cols; $j++) {
                    if ($use_user[1] == $use_game[$j]) {
                        if ($first_pass == true) {
                            echo "<h4>Games won</h4>";
                            echo "<table width=\"100%\"><tr><th>Game #</th><th>Game Date</th><th>Teams</th></tr>";
                            $first_pass = false;
                        }
                        $home_team = $use_game[3];
                        $away_team = $use_game[4];
                        $winning_team = $use_game[6];
                        $game_date = $use_game[1] . "  " .  $use_game[2];
                        if (MFuncs::substring($home_team,$winning_team)==true) {
                            echo "<tr><td>$use_game[0]</td><td>$game_date</td><td>**<span id=\"winner\">$home_team</span> vs $away_team</td></tr>";
                        } else {
                            echo "<tr><td>$use_game[0]</td><td>$game_date</td><td>$home_team vs **<span id=\"winner\">$away_team</span></td></tr>";
                        }
                    }
                }
            }
        }
        if ($first_pass == false) {
            echo "</table>";
        }

        $first_pass = true;
        for ($i=1; $i<=$this->mBets->num_bets; $i++) {
            $one_bet = $this->mBets->arr[$i];
            $num_cols = count($one_bet);
            for ($j = 2; $j < $num_cols; $j += 3) {
                $user = $one_bet[$j];
                if (MFuncs::substring($use_user[1],$user,true) == true) {
                    $game_id = $one_bet[1];
                    $team = $one_bet[$j + 1];
                    $bet_date = $one_bet[$j + 2];
                    $game = $this->mGames->get_by_id($game_id);
                    $home_team = $game[3];
                    $away_team = $game[4];
                    if ($first_pass == true) {
                        echo "<h4>Bets made</h4>";
                        echo "<table width=\"100%\"><tr><th>Game #</th><th>Teams</th><th>Your Bet</th><th>On Date</th></tr>";
                        $first_pass = false;
                    }
                    echo "<tr><td>$game_id</td><td>$home_team vs $away_team</td><td>$team</td><td>$bet_date</td></tr>";
                }
            }
        }
        if ($first_pass == false) {
            echo "</table>";
        }
    }

    public function show_team_info($input_team_id) {
        $use_team = $this->mTeams->get_by_id($input_team_id);
        echo "<h3>Team Info for $use_team[1]</h3>";
        $first_pass = true;
        for ($i=1; $i<=$this->mGames->num_games; $i++) {
            $home_team = $this->mGames->arr[$i][3];
            $away_team = $this->mGames->arr[$i][4];
            $game_date = $this->mGames->arr[$i][1] . "  " .  $this->mGames->arr[$i][2];
            if ((MFuncs::substring($home_team,$use_team[1]) == true) || (MFuncs::substring($away_team,$use_team[1]) == true)) {
                if ($first_pass == true) {
                    echo "<table width=\"100%\"><tr><th>Game Date</th><th>Home Team</th><th></th><th>Away Team</th></tr>";
                    $first_pass = false;
                }
                echo "<tr><td>$game_date</td><td>$home_team</td><td>vs</td><td>$away_team</td></tr>";
            }
        }
        if ($first_pass == false) {
            echo "</table>";
        }
    }

    public function show_games_on_date($input_date) {
        echo "<h3>Games on $input_date</h3>";
        $first_pass = true;
        for ($i=1; $i<=$this->mGames->num_games; $i++) {
            $home_team = $this->mGames->arr[$i][3];
            $away_team = $this->mGames->arr[$i][4];
            $game_date = $this->mGames->arr[$i][1] . "  " .  $this->mGames->arr[$i][2];
            if (MFuncs::substring($game_date, $input_date) == true) {
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