<?php
class MConfig {
	public $main_url;
	public $logf;
	public $tournament_title;
	public $datadir;
	public $background_img;
	public $logo_url;
	public $schedule_url;
	public $squad_url;
	public $rules_url;
	public $page_heading;
	public $bet_from_date;
	public $bet_num_days;
	public $superadmins;
	public $show_home_away_wins;
	public $show_login_screen;
	public $spacer_img;
	public $view_only;
	public $yamavi_winner;

	public function __construct($tournament = "") {
		$this->initvars($tournament);
	}
	
	public function initvars($tournament) {
		$this->initvars_defaults();
		if ($tournament == 'ipl2019') {
			$this->initvars_ipl2019();
		} elseif ($tournament == 'odi_worldcup2019') {
			$this->initvars_odi_worldcup2019();
		} elseif ($tournament == 't20_worldcup2020') {
			$this->initvars_t20_worldcup2020();
		} elseif ($tournament == 'ipl2020') {
			$this->initvars_ipl2020();
		} elseif ($tournament == 'ipl2021') {
			$this->initvars_ipl2021();
		}
		$this->rewritevars();
	}
	
	// Always call this function at the END of initvars() function above.
	private function rewritevars() {
		$this->background_img=$this->datadir . '/img/bg.jpg';
		$this->rules_url = "<a href='./rules.php?tournament=" . $this->datadir . "' target='_blank'>League Rules</a>";
		$this->page_heading = "<h4><a href='" . $this->main_url . "'>" . $this->logo_url . $this->tournament_title . "</a></h4>";
		
		if (strlen(trim($this->schedule_url)) > 0) $this->page_heading .= $this->schedule_url . $this->spacer_img;
		if (strlen(trim($this->squad_url)) > 0) $this->page_heading .= $this->squad_url  . $this->spacer_img;
		if (strlen(trim($this->rules_url)) > 0) $this->page_heading .= $this->rules_url;
		$this->page_heading .= "<br>";
	}

	// Always call this function at the START of each initvars_xxx() function below.
	private function initvars_defaults() {
		$this->logo_url="<img src='img/YaMaVi_Logo_13_128w_48h.png'>";
		$this->spacer_img = "<img src='img/whitespace.png' height='1px' width='150px'>";

		$this->main_url = "./index.php";

		$this->logf = "./yamavi.log";

		$this->tournament_title = "__TOURNAMENT_TITLE__";
		$this->datadir='__DATADIR__';

		$this->background_img=$this->datadir . '/img/bg.jpg';

		$this->schedule_url = "Schedule (<a href='" . $this->datadir . "'/img/__SCHED_BY_DATE_IMAGE__' target='_blank'>by Date</a> / <a href='" . $this->datadir . "'/img/__SCHED_BY_VENUE_IMAGE__' target='_blank'>Venue</a>)";
		$this->squad_url = "<a href='__SQUAD_URL_CRICBUZZ__' target='_blank'>Players</a>";
		$this->rules_url = "<a href='./rules.php?tournament=" . $this->datadir . "' target='_blank'>League Rules</a>";

		$this->bet_from_date='__BET_START_DATE__ YYYY-MM-DD HH:MM:SS';
		$this->bet_num_days='__NUMBER_OF_DAYS_TO_END_OF_TOURNAMENT_FROM_BET_START_DATE__';

		$this->superadmins = ['nobody'];

		$this->show_home_away_wins = true;

		$this->show_login_screen = false;

		$this->view_only = false;
		$this->yamavi_winner = "Nobody_Yet";
	}

	private function initvars_ipl2019() {
		$this->main_url = "./index_ipl2019.php";
		
		$this->tournament_title = "Vivo IPL 2019";
		$this->datadir='ipl2019';

		$this->schedule_url = "";
		$this->squad_url = "";

		$this->bet_from_date='2019-03-21 00:00:00';
		$this->bet_num_days=53;

		$this->show_home_away_wins = true;

		$this->show_login_screen = false;

		$this->view_only = true;
		$this->yamavi_winner = "Yash Shah";
	}

	private function initvars_ipl2020() {
		$this->main_url = "./index_ipl2020.php";
		
		$this->tournament_title = "Vivo IPL 2020";
		$this->datadir='ipl2020';

		$this->schedule_url = "Schedule (<a href='https://www.iplt20.com/schedule' target='_blank'>by Date</a>";
		$this->squad_url = "<a href='https://www.iplt20.com/teams' target='_blank'>Players</a>";

		$this->bet_from_date='2020-09-13 00:00:00';
		$this->bet_num_days=70;

		$this->superadmins = ['manoj'];
		
		$this->show_home_away_wins = true;

		$this->show_login_screen = false;

		$this->view_only = true;
		$this->yamavi_winner = "Manoj Thakur";
	}

	private function initvars_ipl2021() {
		$this->main_url = "./index_ipl2021.php";
		
		$this->tournament_title = "Vivo IPL 2021";
		$this->datadir='ipl2021';

		$this->schedule_url = "Schedule (<a href='https://www.iplt20.com/matches/schedule/men' target='_blank'>by Date</a>";
		$this->squad_url = "<a href='https://www.iplt20.com/teams/men' target='_blank'>Players</a>";

		$this->bet_from_date='2021-04-01 00:00:00';
		$this->bet_num_days=70;

		$this->superadmins = ['manoj'];
		
		$this->show_home_away_wins = true;

		$this->show_login_screen = true;

		$this->view_only = false;
		$this->yamavi_winner = "";
	}

	private function initvars_odi_worldcup2019() {
		$this->main_url = "./index_odi_worldcup2019.php";
		
		$this->tournament_title = "ICC ODI Cricket World Cup 2019";
		$this->datadir='odi_worldcup2019';

		$this->schedule_url = "Schedule (<a href='" . $this->datadir . "'/img/sched1.jpg' target='_blank'>by Date</a> / <a href='" . $this->datadir . "'/img/sched2.png' target='_blank'>Venue</a>)";
		$this->squad_url = "<a href='https://www.cricbuzz.com/cricket-series/2697/icc-cricket-world-cup-2019/squads' target='_blank'>Players</a>";

		$this->bet_from_date='2019-05-1 00:00:00';
		$this->bet_num_days=76;

		$this->show_home_away_wins = false;

		$this->show_login_screen = false;

		$this->view_only = true;
		$this->yamavi_winner = "Yash";
	}


	private function initvars_t20_worldcup2020() {
		$this->main_url = "./index_t20_worldcup2020.php";
		
		$this->tournament_title = "ICC T20 Cricket World Cup 2020 in Australia";
		$this->datadir='t20_worldcup2020';

		$this->schedule_url = "Schedule (<a href='https://www.cricbuzz.com/cricket-series/2798/icc-mens-t20-world-cup-2020/matches' target='_blank'>By Date</a>)";
		$this->squad_url = "<a href='https://www.cricbuzz.com/cricket-series/2798/icc-mens-t20-world-cup-2020/squads' target='_blank'>Players</a>";

		$this->bet_from_date='2020-10-1 00:00:00';
		$this->bet_num_days=46;

		$this->superadmins = ['manoj'];
		
		$this->show_home_away_wins = false;

		$this->show_login_screen = true;

		$this->view_only = false;
		$this->yamavi_winner = "Nobody_Yet";
	}

}
?>
