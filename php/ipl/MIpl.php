<?php
require_once "MUsers.php";
require_once "MTeams.php";
require_once "MGames.php";
require_once "MBets.php";

class MIpl {
	private $year = 2019;
	private $users_filepath = "users.dat";
	private $teams_filepath = "teams.dat";
	private $games_filepath = "games.dat";
	private $bets_filepath = "bets.dat";

	public $mUsers = null;
	public $mTeams = null;
	public $mGames = null;
	public $mBets = null;

	public function __construct($year) {
		$this->year = $year;
		$this->users_filepath = "ipl" . $year . DIRECTORY_SEPARATOR  . "users.dat";
		$this->teams_filepath = "ipl" . $year . DIRECTORY_SEPARATOR  . "teams.dat";
		$this->games_filepath = "ipl" . $year . DIRECTORY_SEPARATOR  . "games.dat";
		$this->bets_filepath = "ipl" . $year . DIRECTORY_SEPARATOR  . "bets.dat";
	}

	public function loadData() {
		$this->mUsers = new MUsers();
		$this->mTeams = new MTeams();
		$this->mGames = new MGames();
		$this->mBets = new MBets();

		$this->mUsers->load($this->users_filepath);
		$this->mTeams->load($this->teams_filepath);
		$this->mGames->load($this->games_filepath);
		$this->mBets->load($this->bets_filepath);
	}

	public function save() {
		$this->mUsers->save($this->users_filepath);
		$this->mTeams->save($this->teams_filepath);
		$this->mGames->save($this->games_filepath);
		$this->mBets->save($this->bets_filepath);
	}

	public function set_winner($game, $team) {
		if ($this->mBets->valid_team($game, $team)) {
			$use_game = &$this->mGames->arr[$game[0]];

			$use_game[5] = "Completed";
			if (count($use_game) < 6) {
				array_push($use_game, $team[1]);
			} else {
				$use_game[6] = $team[1];
			}

			$winning_users = array();
			$this->mBets->get_winning_users($use_game, $team, $winning_users);

			$col = 7;
			foreach ($winning_users as $winning_user) {
				if (count($use_game) < $col) {
					array_push($use_game, $winning_user);
				} else {
					$use_game[$col] = $winning_user;
				}
				$col++;
			}
		}
	}
}
/*
$mIpl = new MIpl(2019);
$mIpl->loadData();

$mIpl->mBets->place_bet($mIpl->mGames->arr[2], $mIpl->mUsers->arr[1], $mIpl->mTeams->arr[2], "4/21/2019");
$mIpl->mBets->save("ipl2019\\saved_bets.dat");

$mIpl->set_winner($mIpl->mGames->arr[2],$mIpl->mTeams->arr[2]);
$mIpl->mGames->save("ipl2019\\saved_games.dat");
*/
?>