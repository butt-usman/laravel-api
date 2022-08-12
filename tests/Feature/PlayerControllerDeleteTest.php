<?php

// /////////////////////////////////////////////////////////////////////////////
// TESTING AREA
// THIS IS AN AREA WHERE YOU CAN TEST YOUR WORK AND WRITE YOUR TESTS
// /////////////////////////////////////////////////////////////////////////////

namespace Tests\Feature;

class PlayerControllerDeleteTest extends PlayerControllerBaseTest
{

    public function test_sample()
    {
        $token = "SkFabTZibXE1aE14ckpQUUxHc2dnQ2RzdlFRTTM2NFE2cGI4d3RQNjZmdEFITmdBQkE=";
        $res = $this->delete(self::REQ_URI . '2',[],['Authorization' => "Bearer $token"])->assertJsonStructure(['message']);
        //print_r($res['message']);
        $this->assertNotNull($res);
    }
}
