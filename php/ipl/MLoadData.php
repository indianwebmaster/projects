<?php
	class MLoadData
	{
		public $max_num_cols = 0;
		public $num_entries = 0;

		private function add_header(&$data_array, $header_string, $delim) {
			if (strlen(trim($header_string)) > 0) {
				array_push($data_array[0],"id");
				$tok = strtok($header_string,$delim);
				while ($tok !== false) {
					array_push($data_array[0],trim($tok));
					$tok = strtok($delim);
				}
			}
		}

		// Note, row 0 will be left empty to be filled by header by the caller.
		public function load_data_file($filepath, $delim, &$data_array, $header_string = "") {
			$num_entries = 0;
			$fh = fopen($filepath,"r");
			if ($fh) {
				while (!feof($fh)) {
					$line = trim(fgets($fh));
					if (strlen($line) > 0 && $line[0] != '#') {
						$num_entries++;

						$tok_array = array();
						$tok = strtok($line, "|");
						$num_cols = 0;
						while ($tok !== false) {
							if ($num_cols == 0) {
								array_push($tok_array, $num_entries);
								$num_cols++;
							}
							array_push($tok_array, trim($tok));
							$num_cols++;
							$tok = strtok("|");
						}
						if ($num_cols > 0) {
							// Note, row 0 will be left empty to be filled by header by the caller.
							if ($num_entries == 1) {
								$data_array = array();
								array_push($data_array,array());
							}
							array_push($data_array,array());
							for ($i=0; $i<$num_cols; $i++) {
								array_push($data_array[$num_entries],$tok_array[$i]);
							}

							if ($num_cols > $this->max_num_cols) {
								$this->max_num_cols = $num_cols;
							}
						}
					}
				}
				fclose($fh);

				$this->num_entries = $num_entries;
				if ($num_entries == 0) {
					$data_array[0] = array();
				}
				$this->add_header($data_array, $header_string, $delim);
			}
			return($this->num_entries);
		}

		public function get_by_id($id, $data_array) {
			$match = null;
			$num_entries = count($data_array);
			for ($i=1; $i<$num_entries; $i++) {
				if ($id == $data_array[$i][0]) {
					$match = $data_array[$i];
				}
			}
			return $match;
		}

		public function save($data_array, $filepath, $overwrite = true) {
			$num_entries = count($data_array);
			if ($num_entries > 0) {
				$fp = fopen($filepath,"w+");
				if ($fp) {
					for ($row=0; $row < $num_entries; $row++) {
						$row_string = "";
						if ($row == 0) {
							$row_string = "# ";
						}
						$one_row = $data_array[$row];
						$num_cols = count($one_row);
						for ($col=1; $col < $num_cols; $col++) {
							$row_string .= ($one_row[$col] . " | ");
						}
						if ($num_cols > 0) {
							$row_string .= "\n";
							fwrite($fp, $row_string);
						}
					}
					fclose($fp);
				}
			}
		}
	}

/*
	$loadData = new MLoadData();

	$users = array();
	$loadData->load_data_file("ipl2019\\users.dat", "|", $users);
	// print_r($users); print ("<p>");

	$num_users = count($users);
	for ($i=0; $i<$num_users; $i++) {
		$num_cols = count($users[$i]);
		for ($j=0; $j<$num_cols; $j++) {
			print($users[$i][$j] . "<br>");
		}
	}

	$games = array();
	$loadData->load_data_file("ipl2019\\games.dat", "|", $games);
	// print_r($games); print ("<p>");

	$num_games = count($games);
	for ($i=0; $i<$num_games; $i++) {
		$num_cols = count($games[$i]);
		for ($j=0; $j<$num_cols; $j++) {
			print($games[$i][$j] . "  ");
		}
		print ("<br>");
	}
*/
?>