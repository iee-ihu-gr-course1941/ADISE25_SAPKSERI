<?php 
#matchmaking
function inqueue($userid){
    $match=findMatch($userid);
    return[
        "status"=>$match["status"],
        "gameid"=>$match["gameid"]
    ];
}

function insert_user_in_game($userid,$cards,$gameid){
    global $mysqli;
    $in_game_state=1;
    $sql = "UPDATE user SET in_game_state=?, gamecards=?, gameid=? WHERE Userid=?;";
    $st = $mysqli->prepare($sql);
    $st->bind_param("isss", $in_game_state,$cards,$gameid, $userid);
    $st->execute();
    $stmt = $mysqli->prepare("UPDATE game SET p2=? WHERE gameid=?;");
    $stmt->bind_param("ss", $userid,$gameid);
    $stmt->execute();
}

function delete_user_fromQ($gameid){
    global $mysqli;
    $stmt = $mysqli->prepare("DELETE FROM gameQUEUE WHERE gameid = ?");
    $stmt->bind_param("s", $gameid);
    $stmt->execute();
}

function findMatch($userid){
    global $mysqli;
    $sql="SELECT userinQ,player2cards,gameid FROM gameQUEUE ORDER BY timestamp ASC LIMIT 1";
    $result = mysqli_query($mysqli, $sql);
    if(mysqli_num_rows($result) == 1) {
        $row=mysqli_fetch_assoc($result);
        $cards= $row['player2cards'];
        $gameid=$row['gameid'];
        insert_user_in_game($userid,$cards,$gameid);
        delete_user_fromQ($gameid);
        return[
            "status"=>"GAME_JOINED",
            "gameid"=>$gameid
        ];
    }else{
        $gameid=creategame($userid, "1");
        $sql = "INSERT INTO gameQUEUE (userinQ, player2cards,gameid) VALUES (?, ?, ?);";
        $st = $mysqli->prepare($sql);
        $st->bind_param("sss", $userid,$gameid["player2cards"],$gameid["gameid"]);
        $st->execute();
        return[
            "status"=>"GAME_CREATED",
            "gameid"=>$gameid["gameid"],
            "player2cards"=>$gameid["player2cards"]
        ];
    }
}
?>