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
        "status" => "INSERT_SUCCESS" ,
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
        "status"=>"DELETE_SUCCESS",
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

function getuserStats($userid){
    global $mysqli;
    $sql = "SELECT username, in_game_state, points FROM user WHERE Userid=? ";
    $st = $mysqli->prepare($sql);
    $st->bind_param("i", $userid);
    $st->execute();
    $result = $st->get_result();
    $row = $result->fetch_assoc();  
    return [
        "username" => $row['username'],
        "in_game_state" => $row['in_game_state'],
        "points"=>$row['points']
    ];
}

function getuserHistory($userid){
    #check if we are goint to do it
    
}

function checkCredentials($username, $password){
    global $mysqli;
    $sql="SELECT Userid, pword FROM user WHERE username=?";
    if($st=$mysqli->prepare($sql)){
        $st->bind_param("s",$username);
        $st->execute();
        $result=$st->get_result();
        $row=$result->fetch_assoc();
        if($row && $row['pword']===$password){
            $user=getuserStats($row["Userid"]);
            return [
                "status"=>"CHECK_SUCCESS",
                "result"=>true,
                "userid"=>$row["Userid"],
                "username" => $user['username'],
                "in_game_state" => $user['in_game_state'],
                "points"=>$user['points']
            ];
        }
    }
    return [
        "status"=>"CHECK_FAIL",
        "result"=>false
    ];
}
?>