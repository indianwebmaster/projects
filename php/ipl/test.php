<?php
	require_once("MUsers.php");
	require_once("MTeams.php");
	require_once("MGames.php");
	require_once("MBets.php");

	$Users = new MUsers();
	if ($Users->load("ipl2019\\users.dat") > 0) {
		for ($i=0; $i<=$Users->num_users; $i++) {
			$num_cols = count($Users->arr[$i]);
			for ($j=0; $j<$num_cols; $j++) {
				print($Users->arr[$i][$j] . "  ");
			}
			print ("<br>");
		}
	}


	$Teams = new MTeams();
	if ($Teams->load("ipl2019\\teams.dat") > 0) {
		for ($i=0; $i<=$Teams->num_teams; $i++) {
			$num_cols = count($Teams->arr[$i]);
			for ($j=0; $j<$num_cols; $j++) {
				print($Teams->arr[$i][$j] . "  ");
			}
			print ("<br>");
		}
	}


	$Games = new MGames();
	if ($Games->load("ipl2019\\games.dat") > 0) {
		for ($i=0; $i<=$Games->num_games; $i++) {
			$num_cols = count($Games->arr[$i]);
			for ($j=0; $j<$num_cols; $j++) {
				print($Games->arr[$i][$j] . "  ");
			}
			print ("<br>");
		}
	}


	$Bets = new MBets();
	if ($Bets->load("ipl2019\\bets.dat") > 0) {
		for ($i=0; $i<=$Bets->num_bets; $i++) {
			$num_cols = count($Bets->arr[$i]);
			for ($j=0; $j<$num_cols; $j++) {
				print($Bets->arr[$i][$j] . "  ");
			}
			print ("<br>");
		}
	}


	$user = $Users->get_by_id(2);
	print_r($user);print("<p>");

	$team = $Teams->get_by_id(3);
	print_r($team);print("<p>");

	$game = $Games->get_by_id(4);
	print_r($game);print("<p>");

	$bet = $Bets->get_by_id(1);
	print_r($bet);print("<p>");

	$Users->save("ipl2019\\saved_users.dat");
	$Teams->save("ipl2019\\saved_teams.dat");
	$Games->save("ipl2019\\saved_games.dat");
	$Bets->save("ipl2019\\saved_bets.dat");
?>