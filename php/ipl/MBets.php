<?php
	require_once ("MLoadData.php");

	class MBets {
		public $arr = array();
		public $num_bets = -1;
		public $header_string = "game_id | user1 | bet team | bet date | user2 | bet team | bet date";
		private $loadData= null;

		public function __construct() {
			$this->loadData = new MLoadData();			
		}

		public function load($data_filepath) {
			$loadData = new MLoadData();
			$this->num_bets = $loadData->load_data_file($data_filepath,"|",$this->arr,$this->header_string);
			return $this->num_bets;
		}

		public function get_by_id($id) {
			return $this->loadData->get_by_id($id,$this->arr);
		}

		public function save($data_filepath) {
			return $this->loadData->save($this->arr, $data_filepath);
		}

		public function valid_team($game, $team) {
			$retval = false;
			if (strpos($game[3],$team[1]) !== false || strpos($game[4],$team[1]) !== false ) {
				$retval = true;
			}
			return $retval;
		}

		public function place_bet($game, $user, $team, $date) {

			// Are teams specified are valid playing in this game?
			if ($this->valid_team($game, $team) == false) {
				// No, just return
				return false;
			}

			// Then check if bet is already present
			$use_bet = array();

			for ($i=1; $i<=$this->num_bets; $i++) {
				// Do we have any bets for this game
				if ($this->arr[$i][1] == $game[0]) {
					$use_bet = &$this->arr[$i];
				}
			}
			$found_bet = false;
			if (count($use_bet) == 0) {
				// Means there are no previous bets on this game
				$this->num_bets++;

				array_push($this->arr, array());

				$use_bet = &$this->arr[$this->num_bets];
				array_push($use_bet,$this->num_bets);
				array_push($use_bet,$game[0]);
				// Will add the user bet details below in if ($found_bet) section
			} else {
				// Means we have a bet on this game. Has this user bet before?
				$num_cols = count($use_bet);
				for ($i=2; $i < $num_cols; $i += 3) {
					$bet_user = $use_bet[$i];
					if ($bet_user == $user[1]) {
						// Means the user has already bet before.
						// Just update the date and team
						$found_bet = true;

						$use_bet[$i + 1] = $team[1];
						$use_bet[$i + 2] = $date;
					}
				}
			}
			if ($found_bet == false) {
				// Means we have bets on this game, but not from this user
				array_push($use_bet,$user[1]);
				array_push($use_bet,$team[1]);
				array_push($use_bet,$date);
			}
			return true;
		}

		public function get_winning_users($game, $winning_team, &$winning_users) {
			$use_bet = array();
			for ($i=1; $i<$this->num_bets; $i++) {
				if ($this->arr[$i][1] == $game[0]) {
					$use_bet = $this->arr[$i];
					break;
				}
			}
			$num_cols = count($use_bet);
			for ($i=2; $i < $num_cols; $i += 3) {
				$bet_team = $use_bet[$i + 1];
				if ($bet_team == $winning_team[1]) {
					$bet_user = $use_bet[$i];
					array_push($winning_users, $bet_user);
				}
			}
		}
	}
?>