<?php
	require_once ("MLoadData.php");
	require_once ("MFuncs.php");

	class MUsers {
		public $arr = array();
		public $num_users = -1;
		public $header_string = "Name";
		private $loadData= null;

		public function __construct() {
			$this->loadData = new MLoadData();			
		}

		public function load($data_filepath) {
			$loadData = new MLoadData();
			$this->num_users = $loadData->load_data_file($data_filepath,"|",$this->arr,$this->header_string);
			return $this->num_users;
		}

		public function get_by_id($id) {
			return $this->loadData->get_by_id($id,$this->arr);
		}

        public function get_by_name($user_name) {
		    $match = array();
		    for ($i=1; $i <= $this->num_users; $i++) {
		        if (MFuncs::substring($this->arr[$i][1], $user_name) == true) {
		            $match = $this->arr[$i];
                }
            }
            return $match;
        }

        public function get_short_name($user_name) {
		    $str_items = explode(" ",$user_name);
		    return (trim($str_items[0]));
        }

        public function save($data_filepath) {
			return $this->loadData->save($this->arr, $data_filepath);
		}
	}
?>