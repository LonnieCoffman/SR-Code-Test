<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\User;

class PlayersIntegrityTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testGoaliePlayersExist () 
    {      
/*
		Check there are players that have can_play_goalie set as 1   
*/
		$result = User::ofPlayers()->where('can_play_goalie', 1)->count();
		$this->assertTrue($result > 1);
	
    }

    public function testAtLeastOneGoaliePlayerPerTeam () 
    {
/*
	    calculate how many teams can be made so that there is an even number of teams and they each have between 18-22 players.
	    Then check that there are at least as many players who can play goalie as there are teams
*/

/*      NOTE: Number of teams is equal to total players divided by the min number of players per team.  Considered making one db call
        to grab the goalie status of all players and then counting the values within the returned associative array, but
        decided for better readability to stick with 2 db calls.
*/
        $minPlayers = 18;
        $players = User::ofPlayers()->count();
        $goalies = User::ofPlayers()->where('can_play_goalie', 1)->count();

        $numTeams = intdiv($players, $minPlayers);

        $this->assertGreaterThanOrEqual($numTeams, $goalies);
    }
}
