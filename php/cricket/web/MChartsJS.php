<?php
class MChartsJS {
	private static $js_loaded = false;

	private $canvas_id = "Noname";
	private $type = "scatter";
	private $labels_str = "";
	private $dataset_str_array = array();
	private $num_graphs = 0;
	private $min = -1;
	private $max = -1;
	private $step = -1;
	private $borderColors = [
								'rgba(255,0,0,1)',
								'rgba(150,64,0,1)',
								'rgba(050,128,0,1)',
								'rgba(150,128,0,1)',
								'rgba(150,64,128,1)',
								'rgba(050,128,128,1)',
								'rgba(150,128,128,1)',
								'rgba(255,0,255,1)',
								'rgba(150,64,255,1)',
								'rgba(050,128,255,1)',
								'rgba(150,128,255,1)',
								'rgba(255,99,132,1)',
								'rgba(54,162,235,1)',
								'rgba(75,192,192,1)',
								'rgba(153,102,255,1)',
								'rgba(255,206,86,1)',
								'rgba(255,99,132,1)',
								'rgba(54,162,235,1)',
								'rgba(75,192,192,1)',
								'rgba(153,102,255,1)',
								'rgba(255,206,86,1)',
								'rgba(255,99,132,1)',
								'rgba(54,162,235,1)',
								'rgba(75,192,192,1)',
								'rgba(153,102,255,1)',
								'rgba(255,206,86,1)',
								'rgba(255,159,64,1)'
							];
	private $backgroundColors = [
								'rgba(255,0,0,0.2)',
								'rgba(150,64,0,0.2)',
								'rgba(050,128,0,0.2)',
								'rgba(150,128,0,0.2)',
								'rgba(150,64,128,0.2)',
								'rgba(050,128,128,0.2)',
								'rgba(150,128,128,0.2)',
								'rgba(255,0,255,0.2)',
								'rgba(150,64,255,0.2)',
								'rgba(050,128,255,0.2)',
								'rgba(150,128,255,0.2)',
								'rgba(255,99,132,0.2)',
								'rgba(54,162,235,0.2)',
								'rgba(75,192,192,0.2)',
								'rgba(153,102,255,0.2)',
								'rgba(255,206,86,0.2)',
								'rgba(255,99,132,0.2)',
								'rgba(54,162,235,0.2)',
								'rgba(75,192,192,0.2)',
								'rgba(153,102,255,0.2)',
								'rgba(255,206,86,0.2)',
								'rgba(255,99,132,0.2)',
								'rgba(54,162,235,0.2)',
								'rgba(75,192,192,0.2)',
								'rgba(153,102,255,0.2)',
								'rgba(255,206,86,0.2)',
								'rgba(255,159,64,0.2)'
							];



	public function __construct($canvas_id="Noname", $type="scatter") {
			$this->canvas_id = $canvas_id;
			$this->type = $type;
	}


	private function write_js_code() {
		$use_min = $this->min - (int) $this->step;
		$use_max = $this->max + (int) $this->step;

		$js_text = <<<EOT
<script type="text/javascript">
	var ctx = document.getElementById("$this->canvas_id").getContext('2d');
	var $this->canvas_id = new Chart (ctx, {
		type: "$this->type",
		data: {
			labels: [ $this->labels_str ],
			datasets: [\n
EOT;
		$num_items = count($this->dataset_str_array);
		if ($num_items > 0) {
			$js_text .= "{\n" . $this->dataset_str_array[0] . " },\n";
			for ($i=1; $i<count($this->dataset_str_array); $i++) {
				$js_text .= "{\n" . $this->dataset_str_array[$i] . " },\n";
			}
		}
		$js_text .= <<<EOT
			],
		},
		options: {
	        scales: {
	            yAxes: [{
	                ticks: {
	                    suggestedMin: $use_min ,
	                    suggestedMax: $use_max,
	                }
	            }]
	        }
	    },
	});
</script>\n
EOT;
		print ($js_text);
	}

	private function add_graph_dataset($label, $x_array, $y_array) {
		if ($this->type == "scatter") {
			$this->add_scatter_graph_dataset($label, $x_array, $y_array);
		} elseif ($this->type == "line") {
			$this->add_line_graph_dataset($label, $x_array, $y_array);
		} elseif ($this->type == "bar") {
			$this->add_bar_graph_dataset($label, $x_array, $y_array);
		} else {
			assert(false,"add_graph_dataset: type: $this->type not implemented yet");
		}
	}

	private function auto_min_max($y_array) {
		$sum = 0;
		$num = count($y_array);
		if ($num > 0) {
			foreach ($y_array as $y_val) {
				$sum += $y_val;
				if ( ($this->min < 0) || ($y_val < $this->min) ) {
					$this->min = $y_val;
				}
				if ( ($this->max < 0) || ($y_val > $this->max) ) {
					$this->max = $y_val;
				}
			}
			$this->step = (int) $this->max / $num;
		}
	}

	private function add_scatter_graph_dataset($label, $x_array, $y_array) {
		$num_xy = count($x_array);	// use smaller of x_count or y_count
		if ($num_xy > count($y_array)) {
			$num_xy = count($y_array);
		}

		$one_dataset = "label: \"$label\",\n";
		$one_dataset .= "data: [\n";
		$one_dataset .= " { x:" . $x_array[0] .", y:" . $y_array[0] . " }\n";
		for ($i=1; $i<$num_xy; $i++) {
			$one_dataset .= ",{ x:" . $x_array[$i] . ", y:" . $y_array[$i] . " }\n";
		}
		$one_dataset .= "],\n";

		$one_dataset .= "borderColor: \"" . $this->borderColors[$this->num_graphs] . "\",\n";
		$one_dataset .= "backgroundColor: \"" . $this->backgroundColors[$this->num_graphs] . "\",\n";
		$one_dataset .= "showLine: true,\n";
		// $one_dataset .= "lineTension: 0\n";

		array_push($this->dataset_str_array, $one_dataset);
	}

	private function add_bar_graph_dataset($label, $x_array, $y_array) {
		$this->add_line_graph_dataset($label, $x_array, $y_array);
	}

	private function add_line_graph_dataset($label, $x_array, $y_array) {
		$num_x = count($x_array);
		if ($num_x > 0) {
			$this->labels_str = "\"" . $x_array[0] . "\"";
			for ($i=1; $i < count($x_array); $i++) {
				$this->labels_str .= ", \"" . $x_array[$i] . "\"";
			}

			$one_dataset = "label: \"$label\",\n";
			$one_dataset .= "data: [ ";
			if (count($y_array) > 0) {
				$one_dataset .= $y_array[0];
				for ($i=1; $i<count($y_array); $i++) {
					$one_dataset .= ", " . $y_array[$i];
				}
			}
			$one_dataset .= "],\n";

			$one_dataset .= "borderColor: \"" . $this->borderColors[$this->num_graphs] . "\",\n";
			$one_dataset .= "backgroundColor: \"" . $this->backgroundColors[$this->num_graphs] . "\",\n";
			$one_dataset .= "showLine: true,\n";
			$one_dataset .= "lineTension: 0\n";

			array_push($this->dataset_str_array, $one_dataset);
		}
	}

	// PUBLIC FUNCTIONS 
	public static function add_charts_js() {
		if (MChartsJS::$js_loaded == false) {
			print ("<script src=\"https://cdn.jsdelivr.net/npm/chart.js@2.8.0\"></script>\n");
			MChartsJS::$js_loaded = true;
		}
	}

	public function add_canvas($width = '200px') {
		print ("<table width=\"$width\"><tr><td><canvas id=\"" . $this->canvas_id . "\"></canvas></td></tr></table>\n");
	}

	public function clear_all_graphs() {
		$this->labels_str = "";
		$this->dataset_str_array = array();
		$this->num_graphs = 0;
	}

	public function set_min_max($min, $max) {
		if ($min < $this->min) $this->min = $min;
		if ($max > $this->max) $this->max = $max;
	}
	
	public function add_graph($label, $x_array, $y_array) {
		if (($this->min < 0) && ($this->max < 0)) {
			$this->auto_min_max($y_array);			
		}
		$this->add_graph_dataset($label,$x_array,$y_array);
		$this->num_graphs += 1;
		return ($this->num_graphs);
	}

	public function draw_all_graphs() {
		if ($this->num_graphs > 0) {
			$this->write_js_code();
		}
	}

	public function get_random_y_data($num_items, &$x_array, &$y_array) {
		$x_array = array();
		$y_array = array();
		for ($i=0; $i<=$num_items; $i++) {
			array_push($x_array,$i);
			array_push($y_array,rand(0,10));
		}
	}
}

// MChartsJS::add_charts_js();

// $mcharts = new MChartsJS("myChart1",'scatter');
// $mcharts = new MChartsJS("myChart1",'bar');
// $mcharts = new MChartsJS("myChart1",'line');
// $mcharts->add_canvas('30%');

// $graph_id = $mcharts->add_graph('label',[1,2,3,4,5],[3,2,1,2,3]);
// $graph_id = $mcharts->add_graph('label',[1,2,3,4,5],[1,2,3,2,1]);
// $graph_id = $mcharts->add_graph('label',[1,2,3,4,5],[1,3,1,3,1]);
// $mcharts->draw_all_graphs();


// $mcharts2 = new MChartsJS("myChart2");
// $mcharts2->add_canvas('30%');

// $graph_id = $mcharts2->add_graph('label',[1,2,3,4,5],[3,2,1,2,3]);
// $graph_id = $mcharts2->add_graph('label',[1,2,3,4,5],[1,2,3,2,1]);
// $mcharts2->draw_all_graphs();

?>
