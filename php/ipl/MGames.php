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

		public function save($data_filepath) {
			return $this->loadData->save($this->arr, $data_filepath);
		}
	}
?>