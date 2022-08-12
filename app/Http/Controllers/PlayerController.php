<?php

// /////////////////////////////////////////////////////////////////////////////
// PLEASE DO NOT RENAME OR REMOVE ANY OF THE CODE BELOW. 
// YOU CAN ADD YOUR CODE TO THIS FILE TO EXTEND THE FEATURES TO USE THEM IN YOUR WORK.
// /////////////////////////////////////////////////////////////////////////////

namespace App\Http\Controllers;

use App\Models\Player;
use App\Models\PlayerSkill;
use Illuminate\Http\Request;
use App\Http\Requests\TeamRequest;
use App\Http\Requests\PlayerRequest;

class PlayerController extends Controller
{
    public function index()
    {
        $players = Player::with('playerSkills')->get();
        return response()->json($players);
    }

    public function show()
    {
        return response("Failed", 500);
    }

    public function store(PlayerRequest $request)
    {
        if($error = self::validatePlayer($request)){
            return response()->json(['message'=>$error]);
        }
        
        $name = $request->input('name');
        $position = $request->input('position');
        $skills = $request->input('playerSkills');
        
        $player = new Player();
        $player->name = $name;
        $player->position = $position;
        $player->save();
        foreach($skills as $s){
            $skill = new PlayerSkill();
            $skill->player_id = $player->id;
            $skill->skill = $s['skill'];
            $skill->value = $s['value'];
            $skill->save();
        }
        $resp = Player::where('id', $player->id)->with('playerSkills')->get();
        return response()->json($resp);
    }

    public function update(PlayerRequest $request)
    {
        $player_id = $request->route('id');
        $player = Player::find($player_id);
        if(!$player){
            return response()->json(['message'=>'Invalid id']);
        }
        if($error = self::validatePlayer($request)){
            return response()->json(['message'=>$error]);
        }

        $name = $request->input('name');
        $position = $request->input('position');
        $skills = $request->input('playerSkills');

        $player->name = $name;
        $player->position = $position;
        $player->save();
        PlayerSkill::where(['player_id' => $player_id])->delete();
        foreach($skills as $s){
            $skill = new PlayerSkill();
            $skill->player_id = $player_id;
            $skill->skill = $s['skill'];
            $skill->value = $s['value'];
            $skill->save();
        }
        $resp = Player::where('id', $player_id)->with('playerSkills')->get();
        return response()->json($resp);
    }

    public function destroy(Request $request)
    {
        $player_id = $request->route('id');
        $player = Player::find($player_id);
        $token = $request->bearerToken();
        if($token ==="SkFabTZibXE1aE14ckpQUUxHc2dnQ2RzdlFRTTM2NFE2cGI4d3RQNjZmdEFITmdBQkE="){
            if(!$player){
                return response()->json(['message'=>'Invalid id']);
            }
            $player->delete();
            return response()->json(['message'=>"Player deleted successfully"], 200);
        }else{
            return response()->json(['message'=>'Unauthorised Access'], 401);
        }
    }

    public function selection(TeamRequest $request)
    {
        $numberOfPlayers = $request->input('numberOfPlayers');
        //print_r($request->input());
        $validPositions = ['defender', 'midfielder', 'forward'];
        $validSkills = [
            'defense',
            'attack',
            'speed',
            'strength',
            'stamina',
        ];
        $positionCount = [];
        $positionSkill = [];
        foreach($request->input() as $input) {
            $position = $input['position'];
            $mainSkill = $input['mainSkill'];
            $numberOfPlayers = $input['numberOfPlayers'];
            if(isset($positionCount[$position])){
                return response()->json(['message'=>"The position of the player should not be repeated in the request."]);
            }else{
                $positionCount[$position] = $numberOfPlayers;
                $positionSkill[$position] = $mainSkill;
            }
            if(!in_array($position, $validPositions)){
                return response()->json(['message'=>"Invalid value for position: ".$position]);
            }
            if(!in_array($mainSkill, $validSkills)){
                return response()->json(['message'=>"Invalid value for skill: ".$mainSkill]);
            }
        }
        $retObject = [];
        foreach($positionCount as $k=>$v){
            $players = [];
            $players = Player::where(['position'=>$k])->get();
            if(count($players) < $v){
                return response()->json(['message'=>"Insufficient number of players for position: ".$k]);
            }else{
                $players = PlayerSkill::select('p.id as pid')->leftJoin('players as p','p.id','=','player_skills.player_id')
                ->where(['position'=>$k,'skill'=>$positionSkill[$k]])->orderBy('value','desc')->limit($v)->get();                
                
                if(count($players) == 0){
                    $players = PlayerSkill::select('p.id as pid')->leftJoin('players as p','p.id','=','player_skills.player_id')
                    ->where(['position'=>$k])->orderBy('value','desc')->limit($v)->get();
                }
                foreach($players as $p){
                    $retObject[] = Player::where('id', $p->pid)->with('playerSkills')->first();
                }
            }
        }
        return response()->json($retObject);
    }

   
    public static function validatePlayer($request){
        $position = $request->input('position');
        $validPositions = ['defender', 'midfielder', 'forward'];
        $validSkills = [
            'defense',
            'attack',
            'speed',
            'strength',
            'stamina',
        ];
        $errorMsg = '';
        if(!in_array($position, $validPositions)){
            return "Invalid value for position: ".$position;
        }
        $skills = $request->input('playerSkills');
        foreach($skills as $s){
            if(!in_array($s['skill'], $validSkills)){
                return "Invalid value for skill: ".$s['skill'];
            }
            if(!is_numeric($s['value'])){
                return "Invalid value for value: ".$s['value'];
            }
        }
        return $errorMsg;
    }
}
