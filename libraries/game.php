<?php 
require_once "conn.php"; 
require "user.php";

#CREATE/RESET/END/UPDATE    

function creategame($usr1, $usr2){
    global $mysqli;
    $cardshsare=sharecards();
    $p1cards=$cardshsare["p1cards"];
    $p2cards=$cardshsare["p2cards"];
    $deckofcards=$cardshsare["deckofcards"];
    $boardcards=$cardshsare["boardofcards"];
    $userid1=$usr1;
    $userid2=$usr2;
    $in_game_state=1;
    $gameid = uniqid();
    $sql = "INSERT INTO game (gameid, p1, p2, turn, deckofcards, boardofcards) VALUES (?, ?, ?, ?, ?, ?);";
    $st = $mysqli->prepare($sql);
    $st->bind_param("ssssss", $gameid,$usr1, $usr2, $usr1, $deckofcards, $boardcards);
    $st->execute();
    $sql = "UPDATE user SET in_game_state=?, gamecards=?, gameid=? WHERE Userid=?;";
    $st = $mysqli->prepare($sql);
    $st->bind_param("isss",$in_game_state,$p1cards,$gameid, $userid1);
    $st->execute();
    return[
        "gameid"=>$gameid,
        "player2cards"=>$p2cards
    ];
}

function resetgame($gameid, $usr1, $usr2){
    endgame($gameid);
    return[
        "gameid"=>creategame($usr1,$usr2),
        "status"=>"RESET_SUCCESS"
    ];
}

function finishgame($gameid){
    global $mysqli;
    $gamedata=getgame($gameid);
    $user1=$gamedata['p1'];
    $user2=$gamedata['p2'];
    $points1=getgamepoints($user1)["gamepoints"];
    $points2=getgamepoints($user2)["gamepoints"];
    if($points1>$points2){
        $winner=$user1;
    }elseif($points2>$points1){
        $winner=$user2;
    }else{$winner="TIE";}
    $pointsOAL=getpoints($winner)["points"]+20;
    $sql = "UPDATE user SET points = ? WHERE Userid = ?;";
    $st = $mysqli->prepare($sql);
    $st->bind_param("is", $pointsOAL, $winner);
    $st->execute();
    $log_id=uniqid();
    $sql = "INSERT INTO log_game_file (log_id, game_id, user_1_id, user_1_score, user_2_id, user_2_score, winner) VALUES (?, ?, ?, ?, ?, ?, ?);";
    $st = $mysqli->prepare($sql);
    $st->bind_param("sssisis", $log_id, $gameid, $user1, $points1, $user2, $points2, $winner);
    $st->execute();
    return [
        "status" => "UPDATE_SUCCESS" ,
        "winner"=>$winner
    ];
}

function endgame($gameid){
    #(did it but check again)kanoume delete to game kai ta xartia kai allazoume to state toy kathe paixti
    global $mysqli;
    $sql = "DELETE FROM game WHERE gameid=?";
    $st = $mysqli->prepare($sql);
    $st->bind_param("s", $gameid);
    $st->execute();
    return[
        "status"=>"DELETE_SUCCESS"
    ];
}

function updategame($gameid, $cards, $turn, $boardcards, $gamepoints,$userid,$cardsgathered){
    global $mysqli;
    $sql="UPDATE game SET turn=?, boardofcards=? WHERE gameid=?";
    $st = $mysqli->prepare($sql);
    $st->bind_param("sss", $turn,$boardcards,$gameid);
	$st->execute();
    $sql="UPDATE user SET gamecards=?, gamepoints=?, cardsgathered=? WHERE gameid=? AND Userid=?";
    $st = $mysqli->prepare($sql);
    $st->bind_param("siiss", $cards,$gamepoints,$cardsgathered,$gameid,$userid);
	$st->execute();
    return[
        "status"=>"UPDATE_SUCCESS"
    ];
}

function updategamepoints($userid,$gamepoints){
    global $mysqli;
    $sql="UPDATE user SET gamepoints=? WHERE Userid=?";
    $st = $mysqli->prepare($sql);
    $st->bind_param("is", $gamepoints,$userid);
	$st->execute();
    return[
        "status"=>"UPDATE_SUCCESS"
    ];
}

#GETTERS
function getgame($gameid){
    global $mysqli;
    $sql = "SELECT * FROM game WHERE gameid=?";
    $st = $mysqli->prepare($sql);
    $st->bind_param("s", $gameid);
    $st->execute();
    $result = $st->get_result();
    $row = $result->fetch_assoc();
    return [
        "gameid" => $row['gameid'],
        "p1" => $row['p1'],
        "p2" => $row['p2'],
        "turn" => $row['turn'],
        "deckofcards" => $row['deckofcards'],
        "boardofcards" => $row['boardofcards']
    ];
}

function getWholegame($gameid){
    #des to meta, not important
}

function getsinglecard($userid,$cardNumber){
    $cards = getcardsofplayer($userid)["cards"];
    $cardsArray = json_decode($cards, true);
    $card = $cardsArray[$cardNumber];
    return[
        "card"=>$card
    ];
}

function getcardsofplayer($userid){
    global $mysqli;
    $sql = "SELECT gamecards FROM user WHERE Userid=? ";
    $st = $mysqli->prepare($sql);
    $st->bind_param("i", $userid);
    $st->execute();
    $result = $st->get_result();
    $row = $result->fetch_assoc(); 
    $cards=json_decode($row['gamecards'], true);
    $number= count($cards);
    return [
        "cards" => $row['gamecards'],
        "numberofcards"=>$number
    ];
}

function getboard($gameid){
    global $mysqli;
    $sql = "SELECT boardofcards FROM game WHERE gameid=? ";
    $st = $mysqli->prepare($sql);
    $st->bind_param("i", $gameid);
    $st->execute();
    $result = $st->get_result();
    $row = $result->fetch_assoc();  
    return [
        "board" => $row['boardofcards'] 
    ];
}

function getturn($gameid){
    global $mysqli;
    $sql = "SELECT turn FROM game WHERE gameid=? ";
    $st = $mysqli->prepare($sql);
    $st->bind_param("i", $gameid);
    $st->execute();
    $result = $st->get_result();
    $row = $result->fetch_assoc();  
    return [
        "turn" => $row['turn'] 
    ];
}

function getgameID($usr1, $usr2){
    global $mysqli;
    $sql = "SELECT gameid FROM game WHERE p1=? AND p2=?";
    $st = $mysqli->prepare($sql);
    $st->bind_param("ii", $usr1, $usr2);
    $st->execute();
    $result = $st->get_result();
    $row = $result->fetch_assoc();  
    return [
        "gameid" => $row['gameid'] 
    ];
}

function getgamepoints($userid){
    global $mysqli;
    $sql = "SELECT gamepoints FROM user WHERE Userid=?";
    $st = $mysqli->prepare($sql);
    $st->bind_param("i", $userid);
    $st->execute();
    $result = $st->get_result();
    $row = $result->fetch_assoc();  
    return [
        "gamepoints" => $row['gamepoints'] 
    ];
}

function getcardsgathered($userid){
    global $mysqli;
    $sql = "SELECT cardsgathered FROM user WHERE Userid=?";
    $st = $mysqli->prepare($sql);
    $st->bind_param("i", $userid);
    $st->execute();
    $result = $st->get_result();
    $row = $result->fetch_assoc();  
    return [
        "cardsgathered" => $row['cardsgathered'] 
    ];
}

#GAME MECHANICS
function check($card, $cardonboard){
    if(!empty($cardonboard) && !empty($card)){    
        if($card['value']==11 || $card['suit']==$cardonboard['suit'] || $card['value']==$cardonboard['value']){
            return true;
        }
    }
    return false;
}

function check2($gameid){
    $gamedata=getgame($gameid);
    $carddata1=getcardsofplayer($gamedata["p1"]);
    $pointcards1=json_decode($carddata1["numberofcards"],true);
    $carddata2=getcardsofplayer($gamedata["p2"]);
    $pointcards2=json_decode($carddata2["numberofcards"],true);
    if(($pointcards1==0) && ($pointcards2==0)){
        return true;
    }else {
        return false;
    }
}

function sharecards(){
    $p1cards=[];
    $p2cards=[];
    $deckofcards=[];
    $boardcards=[];
    $deck=file_get_contents('deck.json');
    $deck=json_decode($deck, true);
    shuffle($deck['deck']);
    for($i=0; $i<=11;$i++){
        if(($i%2)==0){
            array_push($p1cards, array_pop($deck['deck']));
        }else{
            array_push($p2cards, array_pop($deck['deck']));
        }
    }
    for($i=12;$i<=15;$i++){
        array_push($boardcards, array_pop($deck['deck']));
    }
    for($i=16;$i<=51;$i++){
        array_push($deckofcards, array_pop($deck['deck']));
    }
    $p1cards=json_encode($p1cards);
    $p2cards=json_encode($p2cards);
    $boardcards=json_encode($boardcards);
    $deckofcards=json_encode($deckofcards);
    return [
        "p1cards"=>$p1cards,
        "p2cards"=>$p2cards,
        "deckofcards"=>$deckofcards,
        "boardofcards"=>$boardcards
    ];
}

function throwcard($gameid, $cardNumber,$token){
    global $mysqli;
    $action="ERROR";
    $turn=getturn($gameid)["turn"];
    $tokenized=getIDbyToken($token);
    if(!($tokenized["userid"]===$turn && $tokenized["gameid"]===$gameid)){
        return[
            "status"=>"CHEATER",
            "action"=>$action
        ];
    }
    $boardcards=getboard($gameid)["board"];
    $boardcards=json_decode($boardcards,true);
    $game=getgame($gameid);
    $deck=$game["deckofcards"];
    $deck=json_decode($deck, true);
    $p1=$game["p1"];
    $p2=$game["p2"];
    $pcards=getcardsofplayer($turn)["cards"];
    $pcards=json_decode($pcards, true);
    $gamepoints = getgamepoints($turn)["gamepoints"];
    $card=getsinglecard($turn,$cardNumber);
    $card=$card['card'];
    $cardsgathered=getcardsgathered($turn)["cardsgathered"];
    $status="PLAYING";
    #really important
    if(!empty($boardcards)){
        $cardonboard=$boardcards[0];
    }else{
        $cardonboard=[];
    }
    $pcardsE=$pcards;
    for ($i=0; $i<count($pcardsE);$i++){
        if($card==$pcardsE[$i]){
            array_splice($pcardsE, $i, 1);;
            break;
        }
    }
    if (check($card, $cardonboard)){
        if(count($boardcards)==1){
            if($card['suit']==$cardonboard['suit'] || $card['value']==$cardonboard['value']){
                $action="DRY";
                if($card["value"]==11){
                    #20 points 
                    if($boardcards[0]["value"]==11){
                        $action="DRY_20";
                        $cardsgathered=$cardsgathered+2;
                        $gamepoints=$gamepoints+20;
                    }
                }else{
                    #10 points plz
                    $action="DRY_10";
                    $cardsgathered=$cardsgathered+2;
                    $gamepoints=$gamepoints+10;
                }
                $boardcards=[];
            }else{
                    $action="GAINED_CARDS";
                    array_unshift($boardcards, $card);
                    $gamepoints=$gamepoints+calculatepoints($boardcards)["points"];
                    $cardsgathered=$cardsgathered+count($boardcards);
                    $boardcards=[];
            }
        }else{
            #pairnoume ta fila! prepei na bgaloyme to xarti apo ta fulla mas
            $action="GAINED_CARDS";
            array_unshift($boardcards, $card);
            $gamepoints=$gamepoints+calculatepoints($boardcards)["points"];
            $cardsgathered=$cardsgathered+count($boardcards);
            $boardcards=[];
        }
    }else{
        $action="NO_ACTION";
        array_unshift($boardcards, $card);
    }
    $boardcards=json_encode($boardcards);
    $pcards=json_encode($pcardsE);
    if($p1==$turn){
        updategame($gameid, $pcards, $p2, $boardcards, $gamepoints, $p1,$cardsgathered);
    }elseif($p2==$turn){
        updategame($gameid, $pcards, $p1, $boardcards, $gamepoints, $p2,$cardsgathered);
    }
    if((count($pcardsE)==0)){
        if(check2($gameid)){
            if(count($deck)==0){
                $action="GAINED_CARDS";
                $status="GAME_OVER";
                $boardcards=json_decode($boardcards,true);
                $gamepoints=$gamepoints+calculatepoints($boardcards)["points"];
                $cardsgathered=$cardsgathered+count($boardcards);
                calcplus3($gameid);
                $boardcards=[];
                $boardcards=json_encode($boardcards);
                if($p1==$turn){
                    updategame($gameid, $pcards, $p2, $boardcards, $gamepoints, $p1,$cardsgathered);
                }elseif($p2==$turn){
                    updategame($gameid, $pcards, $p1, $boardcards, $gamepoints, $p2,$cardsgathered);
                }
                finishgame($gameid);
            }else{
                $status="ROUND_ENDED";
                RoundShareCards($gameid);
            }
        }
    }
    return[
        "status"=>$status,
        "Card_we_played"=>$card,
        "On_top_of"=>$cardonboard,
        "action"=>$action
    ];
}

function calculatepoints($boardcards){
    $points=0;
    foreach($boardcards as $board){
        if($board["value"]==10 || $board["value"]==11 || $board["value"]==12 || $board["value"]==13){
            $points=$points+1;
        }elseif($board["value"]==2  && $board["suit"]=="spades"){
            $points=$points+1;
        }elseif($board["value"]==10  && $board["suit"]=="diamond"){
            $points=$points+1;
        }
    }
    return[
        "points"=>$points
    ];
}

function RoundShareCards($gameid){
    global $mysqli;
    $game=getgame($gameid);
    $p1=$game["p1"];
    $p2=$game["p2"];
    $p1cards=[];
    $p2cards=[];
    $deckofcards=[];
    $boardcards=[];
    $sql = "SELECT deckofcards, boardofcards FROM game WHERE gameid=?";
    $st = $mysqli->prepare($sql);
    $st->bind_param("s", $gameid);
    $st->execute();
    $result = $st->get_result();
    $row = $result->fetch_assoc();
    $deckofcards=json_decode($row['deckofcards'], true);
    $boardcards=json_decode($row['boardofcards'],true);
    shuffle($deckofcards);
    for($i=0;$i<8;$i++){
        if(!empty($deckofcards)){
            if($i%2==0){
                    $card=array_pop($deckofcards);
                    array_push($p1cards,$card);
            }else{
                    $card=array_pop($deckofcards);
                    array_push($p2cards,$card);                
            }
        }else{break;}
    }
    $p1cards=json_encode($p1cards);
    $p2cards=json_encode($p2cards);
    $boardcards=json_encode($boardcards);
    $deckofcards=json_encode($deckofcards);
    $sql = "UPDATE user SET gamecards=? WHERE gameid=? AND Userid=?;";
    $st = $mysqli->prepare($sql);
    $st->bind_param("ssi", $p1cards,$gameid, $p1);
    $st->execute();
    $sql = "UPDATE user SET gamecards=? WHERE gameid=? AND Userid=?;";
    $st = $mysqli->prepare($sql);
    $st->bind_param("ssi", $p2cards,$gameid, $p2);
    $st->execute();
    $sql = "UPDATE game SET deckofcards=? WHERE gameid=?;";
    $st = $mysqli->prepare($sql);
    $st->bind_param("ss", $deckofcards, $gameid);
    $st->execute();
    if(!empty($deckofcards)){
        return [
            "p1cards"=>$p1cards,
            "p2cards"=>$p2cards,
            "deckofcards"=>$deckofcards,
            "boardofcards"=>$boardcards,
            "status"=>"not empty"
        ];
    }else{
        return [
            "p1cards"=>$p1cards,
            "p2cards"=>$p2cards,
            "deckofcards"=>$deckofcards,
            "boardofcards"=>$boardcards,
            "status"=>"empty"
        ];
    }
}

function calcplus3($gameid){
    $gamedata=getgame($gameid);
    $pointcards1=json_encode(getcardsgathered($gamedata["p1"]));
    $pointcards2=json_encode(getcardsgathered($gamedata["p2"]));
    $gamepoints1=getgamepoints($gamedata["p1"]);
    $gamepoints2=getgamepoints($gamedata["p2"]);
    $gamepoints1=$gamepoints1["gamepoints"];
    $gamepoints2=$gamepoints2["gamepoints"];
    if($pointcards1>$pointcards2){
        $gamepoints1=$gamepoints1+3;
        updategamepoints($gamedata["p1"],$gamepoints1);
        return[
            "status"=>"3_POINTS"
        ];
    }elseif($pointcards2>$pointcards1){
        $gamepoints2=$gamepoints2+3;
        updategamepoints($gamedata["p2"],$gamepoints2);
        return[
            "status"=>"3_POINTS"
        ];
    }
}
?>