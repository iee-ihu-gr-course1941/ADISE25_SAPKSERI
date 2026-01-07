<?php
#API
require_once "libraries/conn.php"; 
require_once "libraries/dbpassagg.php";
require_once "libraries/game.php";
require_once "libraries/match.php";
require_once "libraries/user.php";
# CHECK check() once again, do we need it ???
header('Content-Type: application/json');
$method=$_SERVER['REQUEST_METHOD'];
$path_info = isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : '';
$request = explode('/', trim($path_info, '/'));
$input = json_decode(file_get_contents('php://input'), true);
switch($method){
    case 'GET':
        switch($request[0]){
            case 'game':
                switch($request[1]){
                    case 'game':
                        if(isset($input['gameid'])){
                            $data = getgame($input['gameid']);
                            echo json_encode($data);
                        }else {
                            http_response_code(400);
                            echo json_encode(['error' => 'Missing game id']);
                        }
                        break;
                    case 'cardsofplayers':
                        if(isset($input['userid'])){
                            $data = getcardsofplayer($input['userid']);
                            echo json_encode($data);
                        }else {
                            http_response_code(400);
                            echo json_encode(['error' => 'Missing users ids']);
                        }
                        break;
                    case 'singlecard':
                        if(isset($input['userid']) && isset($input['number'])){
                            $data = getsinglecard($input['userid'], $input['number']);
                            echo json_encode($data);
                        }else {
                            http_response_code(400);
                            echo json_encode(['error' => 'Missing users ids']);
                        }
                        break;
                    case 'board':
                        if(isset($input['gameid'])){
                            $data = getboard($input['gameid']);
                            echo json_encode($data);
                        }else {
                            http_response_code(400);
                            echo json_encode(['error' => 'Missing game ids']);
                        }
                        break;
                    case 'turn':
                        if(isset($input['gameid'])){
                            $data = getturn($input['gameid']);
                            echo json_encode($data);
                        }else {
                            http_response_code(400);
                            echo json_encode(['error' => 'Missing game id']);
                        }
                        break;
                    case 'ID':
                        if(isset($input['usr1']) && isset($input['usr2'])){
                            $data = getgameID($input['usr1'],$input['usr2']);
                            echo json_encode($data);
                        }else {
                            http_response_code(400);
                            echo json_encode(['error' => 'Missing game id']);
                        }
                        break;
                    case 'pointcards':
                        if(isset($input['userid'])){
                            $data = getgamepoints($input['userid']);
                            echo json_encode($data);
                        }else {
                            http_response_code(400);
                            echo json_encode(['error' => 'Missing game id']);
                        }
                        break;
                }
                break;
            case 'user':
                switch($request[1]){
                    case 'in_game_state':
                        if(isset($input['userid'])){
                            $data = getIn_game_state($input['userid']);
                            echo json_encode($data);
                        }else {
                            http_response_code(400);
                            echo json_encode(['error' => 'Missing game id']);
                        }
                        break;
                    case 'username':
                        if(isset($input['userid'])){
                            $data = getusername($input['userid']);
                            echo json_encode($data);
                        }else {
                            http_response_code(400);
                            echo json_encode(['error' => 'Missing game id']);
                        }
                        break;
                    case 'points':
                        if(isset($input['userid'])){
                            $data = getpoints($input['userid']);
                            echo json_encode($data);
                        }else {
                            http_response_code(400);
                            echo json_encode(['error' => 'Missing game id']);
                        }
                        break;
                    case 'gameid':
                        if(isset($input['userid'])){
                            $data = getUsersGameID($input['userid']);
                            echo json_encode($data);
                        }else {
                            http_response_code(400);
                            echo json_encode(['error' => 'Missing game id']);
                        }
                        break;
                }
                break;
        }
        break;
    case 'POST':
        switch($request[0]){
            case 'game':
                switch($request[1]){
                    case 'create':
                        if(isset($input['usr1']) && isset($input['usr2'])){
                            $data = creategame($input['usr1'], $input['usr2']);
                            echo json_encode($data);
                        }else {
                            http_response_code(400);
                            echo json_encode(['error' => 'Missing users ids']);
                        }
                        break;
                    case 'reset':
                        if(isset($input['gameid']) && isset($input['usr1']) && isset($input['usr2'])){
                            $data=resetgame($input['gameid'],$input['usr1'], $input['usr2']);
                            echo json_encode($data);
                        }else {
                            http_response_code(400);
                            echo json_encode(['error' => 'Missing users ids and game id']);
                        }
                        break;
                    case 'firstshare':
                        $data=sharecards();
                        echo json_encode($data);
                        break;
                    case 'throwcard':
                        if(isset($input['gameid']) && isset($input['number'])){
                            $data=throwcard($input['gameid'],$input['number']);
                            echo json_encode($data);
                        }else {
                            http_response_code(400);
                            echo json_encode(['error' => 'Missing users ids and game id']);
                        }
                        break;
                    case 'calculatepoints':
                        if(isset($input['userid'])){
                            $data=calculatepoints($input['userid']);
                            echo json_encode($data);
                        }else {
                            http_response_code(400);
                            echo json_encode(['error' => 'Missing users ids and game id']);
                        }
                        break;
                    case 'roundshare':
                        if(isset($input['gameid'])){
                            $data=RoundShareCards($input['gameid']);
                            echo json_encode($data);
                        }else {
                            http_response_code(400);
                            echo json_encode(['error' => 'Missing users ids and game id']);
                        }
                        break;
                } 
                break;
            case 'match':
               switch($request[1]){
                    case 'queue':
                        if(isset($input['userid'])){
                            $data=inqueue($input['userid']);
                            echo json_encode($data);
                        }else {
                            http_response_code(400);
                            echo json_encode(['error' => 'Missing users ids and game id']);
                        }
                        break; 
                    case 'find':
                            $data=findMatch();
                            echo json_encode($data);
                        break;
               } 
            case 'user':
                switch($request[1]){
                    case 'create':
                        if(isset($input['username']) && isset($input['pword'])){
                            $data=createuser($input['username'], $input['pword']);
                            echo json_encode($data);
                        }else {
                            http_response_code(400);
                            echo json_encode(['error' => 'Missing users ids and game id']);
                        }
                        break; 
                }
        }
        break;
    case 'DELETE':
        switch($request[0]){
            case 'game':
                switch($request[1]){
                    case 'delete':
                        if(isset($input['gameid'])){
                            $data=endgame($input['gameid']);
                            echo json_encode($data);
                        }else {
                            http_response_code(400);
                            echo json_encode(['error' => 'Missing game id']);
                        }
                        break;
                }
                break;
            case 'user':
                switch($request[1]){
                    case 'delete':
                        if(isset($input['userid'])){
                            $data=deleteuser($input['userid']);
                            echo json_encode($data);
                        }else {
                            http_response_code(400);
                            echo json_encode(['error' => 'Missing users ids and game id']);
                        }
                        break; 
                }
        }
        break;
    case 'PUT':
        switch($request[0]){
            case 'game':
                switch($request[1]){
                    case 'update':
                        if(isset($input['gameid']) && isset($input['cards']) && isset($input['turn'])&& isset($input['boardcards'])&& isset($input['gamepoints'])&& isset($input['userid'])){
                            $data = updategame($input['gameid'], $input['cards'],$input['boardcards'],$input['gamepoints'],$input['userid']);
                            echo json_encode($data);
                        }else {
                            http_response_code(400);
                            echo json_encode(['error' => 'Missing everything']);
                        }
                        break;
                    case 'updategamepoints':
                        if(isset($input['userid']) && isset($input['gamepoints'])){
                            $data = updategamepoints($input['userid'], $input['gamepoints']);
                            echo json_encode($data);
                        }else {
                            http_response_code(400);
                            echo json_encode(['error' => 'Missing everything']);
                        }
                        break;
                }
        }

        break;    
    default:
        http_response_code(404);
        echo json_encode(['error' => 'Endpoint not found']);
        break;
}
?>