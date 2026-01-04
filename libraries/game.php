<?php 
require_once "conn.php"; 
require "uniqueid.js";

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
    $pointcards=file_get_contents('pointcards.json');
    $pointcards=json_encode($pointcards);
    $gameid = uniqid();
    $sql = "INSERT INTO game (gameid, p1, p2, turn, deckofcards, boardofcards) VALUES (?, ?, ?, ?, ?);";
    $st = $mysqli->prepare($sql);
    $st->bind_param("siiiss", $gameid,$usr1, $usr2, $usr1, $deckofcards, $boardcards);
    $st->execute();
    $sql = "UPDATE user SET in_game_state=?, gamecards=?, gamepoints=0, cardsFORpoints=?, gameid=? WHERE Userid=?;";
    $st = $mysqli->prepare($sql);
    $st->bind_param("issii", 1,$p1cards, $pointcards,$gameid, $userid1);
    $st->execute();
    $sql = "UPDATE user SET in_game_state=?, gamecards=?, gamepoints=0, cardsFORpoints=?, gameid=? WHERE Userid=?;";
    $st = $mysqli->prepare($sql);
    $st->bind_param("issii", 1,$p2cards, $pointcards,$gameid, $userid2);
    $st->execute();
    return[
        "gameid"=>$gameid
    ];
}

function resetgame($gameid, $usr1, $usr2){
    endgame($gameid);
    return[
        "gameid"=>creategame($usr1,$usr2),
        "status"=>"game reseted"
    ];
}

function endgame($gameid){
    #(did it but chekc again)kanoume delete to game kai ta xartia kai allazoume to state toy kathe paixti
    global $mysqli;
    $sql = "DELETE FROM game WHERE gameid=?";
    $st = $mysqli->prepare($sql);
    $st->bind_param("s", $gameid);
    $st->execute();
    return[
        "status"=>"game succesfully has ended"
    ];
}

function updategame($gameid, $cards, $turn, $boardcards, $pointcards,$userid){
    #need biulding
    global $mysqli;
    $sql="UPDATE game SET turn='$turn', boardofcards='$boardcards' WHERE gameid=$gameid";
    $st = $mysqli->prepare($sql);
	$st->execute();
    $sql="UPDATE user SET gamecards='$cards', cardsFORpoints='$pointcards' WHERE gameid=$gameid AND Userid=$userid";
    $st = $mysqli->prepare($sql);
	$st->execute();
    return[
        "status"=>"succesfully updated the game"
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

function getcardsofplayer($userid){
    global $mysqli;
    $sql = "SELECT gamecards FROM user WHERE Userid=? ";
    $st = $mysqli->prepare($sql);
    $st->bind_param("i", $userid);
    $st->execute();
    $result = $st->get_result();
    $row = $result->fetch_assoc();  
    return [
        "cards" => $row['gamecards'] 
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

function getpointcards($userid){
    global $mysqli;
    $sql = "SELECT cardsFORpoints FROM user WHERE Userid=?";
    $st = $mysqli->prepare($sql);
    $st->bind_param("i", $userid);
    $st->execute();
    $result = $st->get_result();
    $row = $result->fetch_assoc();  
    return [
        "cardsFORpoints" => $row['cardsFORpoints'] 
    ];
}

#GAME MECHANICS
function check($card, $cardonboard){
    if($card['value']==11){
        return true;}
    if($card['suit']>=$cardonboard['suit'] || $card['value']>=$cardonboard['value']){
        return true;
    }
    return false;
}

function check2($gameid){
    $gamedata=getgame($gameid);
    $board=json_encode(getpointcards($gamedata["boardofcards"]));
    $deck=json_encode(getpointcards($gamedata["deckofcards"]));
    $pointcards1=json_encode(getpointcards($gamedata["p1"]));
    $pointcards2=json_encode(getpointcards($gamedata["p2"]));
    if(empty($pointcards1) && empty($pointcards2) && empty($deck) && !empty($board)){
        return true;
    }else false;
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

function throwcard($gameid, $card){
    global $mysqli;
    if (!is_array($boardcards) || empty($boardcards)) {
        die("Error: boardcards is empty or not an array");
    }
    if (!is_array($card) || empty($card)) {
        die("Error: card is empty or not an array");
    }
    $turn=getturn($gameid)["turn"];
    $boardcards=getboard($gameid)["board"];
    $game=getgame($gameid);
    $p1=$game["p1"];
    $p2=$game["p2"];
    $pcards=getcardsofplayer($turn)["cards"];
    $pointcards=getpointcards($turn)["cardsFORpoints"];
    #really important
    $cardonboard=$boardcards[0];
    $pcardsE=$pcards;
    for ($i=0; $i<count($pcardsE);$i++){
        if($card==$pcardsE[$i]){
            unset($pcardsE[$i]);
            break;
        }
    }

    if (check($card, $cardonboard)){
        if(count($boardcards)==1){
            #KSERI!!!!
            for ($i=0; $i<count($pcards);$i++){
                if($card==$pcards[$i]){
                    unset($pcards[$i]);
                    if($card['value']==11 && $boardcards['value']==11){
                        #20 points 
                        $pointcards['kseres'][]=["values"=>"20"];
                    }else{
                        #10 points plz
                        $pointcards['kseres'][]=["values"=>"10"];
                    }
                    $boardcards='[]';
                    break;
                }
            }
        }else{
            #pairnoume ta fila! prepei na bgaloyme to xarti apo ta fulla mas
            array_push($boardcards, $card);
            foreach($boardcards as $boardcard){
                $pointcards['normalcards'][]=$boardcard;
            }
            $boardcards='[]';
        }
    }
    else{
        array_push($boardcards, $card);
        if(empty($pcardsE)){
            if(check2($gameid)){
                foreach($boardcards as $boardcard){
                    $pointcards['normalcards'][]=$boardcard;
                }
                $boardcards='[]';
            }
        }
    }
    $card=json_encode($card);
    $boardcards=json_encode($boardcards);
    $pcards=json_encode($pcardsE);
    $pointcards=json_encode($pointcards);
    if($p1==$turn){
        updategame($gameid, $pcards, $p2, $boardcards, $pointcards, $p2);
    }elseif($p2==$turn){
        updategame($gameid, $pcards, $p1, $boardcards, $pointcards, $p1);
    } 
    return[
        "status"=>"success"
    ];
}

function calculatepoints($usr){
    global $mysqli;
    $cards=getpointcards($userid)["cardsFORpoints"];
    $cards=json_decode($cards,true);
    $points=0;
    foreach($cards["kseres"]["value"] as $point){
        $points=$points+$point;
    }
    foreach($cards["normalcards"] as $point){
        if($point[][]["value"]=10 || $point[][]["value"]=11 || $point[][]["value"]=12 || $point[][]["value"]=13){
            $points=$points+1;
        }elseif($point[][]["value"]=2  && $point[]["suit"][]="spades"){
            $points=$points+1;
        }elseif($point[][]["value"]=10  && $point[]["suit"][]="diamond"){
            $points=$points+1;
        }
    }
    if($usr==calcplus3($gameid)){
        $points=$points+3;
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
    $sql = "SELECT deckofcards FROM game WHERE gameid=?";
    $st = $mysqli->prepare($sql);
    $st->bind_param("s", $gameid);
    $st->execute();
    $result = $st->get_result();
    $deckofcards=jsondecode($result, true);
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
    $sql = "UPDATE game SET deckofcards=?, boardofcards=? WHERE gameid=?;";
    $st = $mysqli->prepare($sql);
    $st->bind_param("sss", $deckofcards, $boardcard, $gameid);
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
    $pointcards1=json_encode(getpointcards($gamedata["p1"]));
    $pointcards2=json_encode(getpointcards($gamedata["p2"]));
    $score1 = (count($pointcards1["kseres"]) * 2) + (count($pointcards1["normalcards"]) * 1);
    $score2 = (count($pointcards2["kseres"]) * 2) + (count($pointcards2["normalcards"]) * 1);
    if($score1>$score2){
        return[
            "player"=>$gamedata["p1"]
        ];
    }elseif($score2>$score1){
        return[
            "player"=>$gamedata["p2"]
        ];
    }
}

?>