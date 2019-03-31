<?php
	require_once ("MLoadData.php");
	require_once ("MFuncs.php");

	class MGames {
		public $arr = array();
		public $num_games = -1;
		public $header_string = "Date | Time (NJ) | Home Team | Away Team | Result | Winning Team | Winning User(s) |";
		private $loadData= null;

		public function __construct() {
			$this->loadData = new MLoadData();			
		}

		public function load($data_filepath) {
			$this->num_games = $this->loadData->load_data_file($data_filepath,"|",$this->arr,$this->header_string);
			return $this->num_games;
		}

		public function get_by_id($id) {
			return $this->loadData->get_by_id($id,$this->arr);
		}

		// $dt as string starting with format 'D M d' (e.g. "Tue Mar 21")
		public function get_by_date($dt) {
		    $match = array();
		    for ($i=1; $i<=$this->num_games; $i++) {
		        if (MFuncs::substring($this->arr[$i][1],$dt) == true) {
		            array_push($match, $this->arr[$i]);
                }
            }
		    return ($match);
        }

        public function is_a_future_game($game) {
		    $game_dt = strtotime($game[1]);
		    $today_dt = time();
            if ($today_dt < $game_dt) {
                return true;    // Means a future date
            }
            return false;
        }

        public function get_winning_usernames($game) {
            $winning_usernames=array();
            $num_cols = count($game);
            for ($i=7; $i<$num_cols; $i++) {
                array_push($winning_usernames, $game[$i]);
            }
		    return ($winning_usernames);
        }

        public function get_upcoming_games($num_days) {
		    $upcoming_games = array();
            $today_dt = time();
            for ($i=1; $i<=$this->num_games; $i++) {
                $game = $this->arr[$i];
                $game_dt = strtotime($game[1]);
                if ( ( ($game_dt - $today_dt) > 0) &&  ( ($game_dt - $today_dt) <= ($num_days * 24 * 3600)) ) {
                    array_push($upcoming_games,$game);
                }
            }
            return ($upcoming_games);
        }

        public function get_winner_loser($game_id) {
        	$winner_loser = array_fill(0,2,"");
        	$game = $this->get_by_id($game_id);
        	if ($game[5] == "Completed") {
	        	$home_team = $game[3];
	        	$away_team = $game[4];
	        	$winning_team = $game[6];
        		if (MFuncs::substring($home_team,$winning_team)) {
        			$winner_loser[0] = $home_team;
        			$winner_loser[1] = $away_team;
        		} else {
        			$winner_loser[0] = $away_team;
        			$winner_loser[1] = $home_team;
        		}
        	}
        }

        public function get_winners_losers() {
        	$winners_losers = array();
        	$idx = 0;
        	for ($i=1; $i <= $this->num_games; $i++) {
        		$game = $this->arr[$i];
	        	if ($game[5] == "Completed") {
		        	$home_team = $game[3];
		        	$away_team = $game[4];
		        	$winning_team = $game[6];
		        	array_push($winners_losers, array_fill(0,3,""));
	        		$winners_losers[$idx][0] = $game[0];
	        		if (MFuncs::substring($home_team,$winning_team)) {
	        			$winners_losers[$idx][1] = $home_team;
	        			$winners_losers[$idx][2] = $away_team;
	        		} else {
	        			$winners_losers[$idx][1] = $away_team;
	        			$winners_losers[$idx][2] = $home_team;
	        		}
					$idx++;
	        	}
        	}
        	return ($winners_losers);
        }

        public function is_completed($game) {
        	if ($game[5] == "Completed") return true;
        	return false;
        }

        public function get_game_string($game) {
            $game_str = $game[3] . " vs " . $game[4];
            if ($this->is_completed($game)) {
                if ( (MFuncs::substring($game[3], $game[6])) || (MFuncs::substring($game[6], $game[3])) ) {
                    $game_str = "**" . $game[3] . " vs " . $game[4];
                } else {
                    $game_str = $game[3] . " vs **" . $game[4];
                }
            }
            return ($game_str);
        }
        
		public function save($data_filepath) {
			return $this->loadData->save($this->arr, $data_filepath);
		}
	}
?>
