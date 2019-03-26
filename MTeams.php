<?php
	require_once ("MLoadData.php");

	class MTeams {
		public $arr = array();
		public $num_teams = -1;
		public $header_string = "Team | ShortName | Captain";
		private $loadData= null;

		public function __construct() {
			$this->loadData = new MLoadData();			
		}

		public function load($data_filepath) {
			$loadData = new MLoadData();
			$this->num_teams = $loadData->load_data_file($data_filepath,"|",$this->arr,$this->header_string);
			return $this->num_teams;
		}

		public function get_by_id($id) {
			return $this->loadData->get_by_id($id,$this->arr);
		}

		public function save($data_filepath) {
			return $this->loadData->save($this->arr, $data_filepath);
		}
	}
?>