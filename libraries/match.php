<?php 
#matchmaking
function inqueue($userid){
    global $mysqli;
    $sql="INSERT INTO gameQUEUE (usersinQ) 
          VALUES ($userid);";
    $st = $mysqli->prepare($sql);
	$st->execute();
    return[
        "status"=>"User is succesfully in queue for a game"
    ];
}

function findMatch(){
    global $mysqli;
    $sql="SELECT usersinQ FROM gameQUEUE ORDER BY timestamp ASC LIMIT 2";
    $result = mysqli_query($mysqli, $sql);
    if(mysqli_num_rows($result) == 2) {
        $user1 = mysqli_fetch_assoc($result)['userid'];
        $user2 = mysqli_fetch_assoc($result)['userid'];
        $gameid=creategame($user1, $user2);
        $stmt = $mysqli->prepare("DELETE FROM gameQUEUE WHERE userid = ? OR userid = ?");
        $stmt->bind_param("ii", $user1, $user2);
        $stmt->execute();
        return[
            "status"=>"Game Created! Entering Game.....",
            "gameid"=>$gameid['gameid']
        ];
    }
    return[
        "status"=>"Waiting for player..."
    ];
}
?>