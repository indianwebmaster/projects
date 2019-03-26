<?php
	require_once ("MLoadData.php");

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

		public function save($data_filepath) {
			return $this->loadData->save($this->arr, $data_filepath);
		}
	}
?>