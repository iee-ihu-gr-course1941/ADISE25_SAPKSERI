<?php 
#STATUS FILE
require_once "libraries/user.php";
require_once "libraries/game.php";

$token = $_GET['token'];
$tokenized=getIDbyToken($token);
$userid = $tokenized['userid']; 
$gameid = $tokenized['gameid'];
$board = getboard($gameid);
$turninfo = getturn($gameid);
$mycards = getcardsofplayer($userid);
$points = getgamepoints($userid);
echo json_encode([
    'board' => $board,
    'my_cards' => $mycards,
    'is_my_turn' => ($turninfo['turn'] == $userid),
    'points' => $points
]);
?>