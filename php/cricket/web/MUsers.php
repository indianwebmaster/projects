<?php
	require_once ("MLoadData.php");
	require_once ("MFuncs.php");

	class MUsers {
		public $arr = array();
		public $num_users = -1;
		public $header_string = "Name | passwd | add_points ";
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
		        if (MFuncs::substring($this->arr[$i][1], $user_name, true) == true) {
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

		function check_user($username, $paswd) {
			$retval = 'invalid';
			if (strlen(trim($username)) > 0) {
				$user = $this->get_by_name($username);
				if (count($user) > 0) {
					if ( isset($user[2]) && strlen(trim($user[2])) > 0) {
						if (strlen(trim($paswd)) > 0) {
							if (MFuncs::substring($user[2], $paswd)) {
								$retval = 'valid';
							} else {
								$retval = 'invalid';
								print ("Invalid password<br>");
							}
						} else {
								print ("Invalid password<br>");
						}
					} else {
						$retval = 'newuser';
					}
				} else {
					print ("Invalid user<br>");
				}
			}
			return ($retval);
		}

		function add_paswd($username, $paswd) {
			if (strlen(trim($username)) > 0 && strlen(trim($paswd)) > 0) {
				$user = $this->get_by_name($username);
				if (count($user) > 0) {
					if (isset($user[2])) {
						$this->arr[$user[0]][2] = $paswd;
					} else {
						array_push($this->arr[$user[0]], $paswd);
					}
				}
			}
		}

		function get_add_points($username) {
			$add_points = 0;
			if (strlen(trim($username)) > 0) {
				$user = $this->get_by_name($username);
				if (count($user) > 0) {
					if (isset($user[3])) {
						$add_points = (int) $this->arr[$user[0]][3];
					}
				}
			}
			return ($add_points);
		}
	}
?>