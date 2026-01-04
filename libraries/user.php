<?php 
require_once "conn.php";
#user 
function createuser($username,$pword){
    global $mysqli;
    $userid=uniqid();
    $sql = "INSERT INTO user (Userid, username, pword) VALUES (?, ?, ?);";
    $st = $mysqli->prepare($sql);
    $st->bind_param("sss", $userid,$username, $pword);
    $st->execute();
    return [
        "status" => "success" ,
        "userid"=>$userid
    ];
}

function deleteuser($userid){
    global $mysqli;
    $sql = "DELETE FROM user WHERE Userid=?";
    $st = $mysqli->prepare($sql);
    $st->bind_param("s", $userid);
    $st->execute();
    return[
        "status"=>"user deleted",
        "deleted_user"=>$userid
    ];
}

function getIn_game_state($userid){
    global $mysqli;
    $sql = "SELECT in_game_state FROM user WHERE Userid=? ";
    $st = $mysqli->prepare($sql);
    $st->bind_param("i", $userid);
    $st->execute();
    $result = $st->get_result();
    $row = $result->fetch_assoc();  
    return [
        "in_game_state" => $row['in_game_state'] 
    ];
}

function getusername($userid){
    global $mysqli;
    $sql = "SELECT username FROM user WHERE Userid=? ";
    $st = $mysqli->prepare($sql);
    $st->bind_param("i", $userid);
    $st->execute();
    $result = $st->get_result();
    $row = $result->fetch_assoc();  
    return [
        "username" => $row['username'] 
    ];
}

function getpoints($userid){
    global $mysqli;
    $sql = "SELECT points FROM user WHERE Userid=? ";
    $st = $mysqli->prepare($sql);
    $st->bind_param("i", $userid);
    $st->execute();
    $result = $st->get_result();
    $row = $result->fetch_assoc();  
    return [
        "points" => $row['points'] 
    ];
}

function getUsersGameID($userid){
    global $mysqli;
    $sql = "SELECT gameid FROM user WHERE Userid=? ";
    $st = $mysqli->prepare($sql);
    $st->bind_param("i", $userid);
    $st->execute();
    $result = $st->get_result();
    $row = $result->fetch_assoc();  
    return [
        "gameid" => $row['gameid'] 
    ];   
}
?>