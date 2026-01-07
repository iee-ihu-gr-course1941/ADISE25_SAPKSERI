<?php 
#matchmaking
function inqueue($userid){
    global $mysqli;
    $sql="INSERT INTO gameQUEUE (userinQ) 
          VALUES (?);";
    $st = $mysqli->prepare($sql);
    $st->bind_param("s", $userid);
	$st->execute();
    $match=findMatch();
    return[
        "status"=>"User is succesfully in queue for a game",
        "status2"=>$match["status"],
        "gameid"=>$match["gameid"]
    ];
}

function findMatch(){
    global $mysqli;
    $sql="SELECT userinQ FROM gameQUEUE ORDER BY timestamp ASC LIMIT 2";
    $result = mysqli_query($mysqli, $sql);
    if(mysqli_num_rows($result) == 2) {
        $user1 = mysqli_fetch_assoc($result)['userinQ'];
        $user2 = mysqli_fetch_assoc($result)['userinQ'];
        $gameid=creategame($user1, $user2);
        $stmt = $mysqli->prepare("DELETE FROM gameQUEUE WHERE userinQ = ? OR userinQ = ?");
        $stmt->bind_param("ss", $user1, $user2);
        $stmt->execute();
        return[
            "status"=>"Game Created! Entering Game.....",
            "gameid"=>$gameid['gameid']
        ];
    }
    return[
        "status"=>"Waiting for player...",
        "gameid"=>NULL
    ];
}
?>