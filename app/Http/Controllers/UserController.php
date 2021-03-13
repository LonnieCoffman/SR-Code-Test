<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \App\Models\User;
use Faker\Factory as Faker;
use TeamNameGenerator;

class UserController extends Controller
{

    /*
        NOTES:
        * I found that the recommended Faker package did not create fun team names, so I created a provider for faker that created
            amusing team names and published it on Packagist (https://packagist.org/packages/lonniecoffman/faker-team-names).
        * Team sizes are random and players are randomly assigned to teams prior to refining their placement.  Doing this allows
            for unique teams to be generated on each refresh.
        * Since coaches were provided in the sample database I gave each team a random coach if one is available.
    */


    public function index() {
        
        $faker = Faker::create();
        $faker->addProvider(new TeamNameGenerator\FakerProvider($faker));

        $minTeamSize = 18;
        $maxTeamSize = 22;

        // get all players and coaches
        $players = User::ofPlayers()->inRandomOrder()->get();
        $coaches = User::ofCoaches()->inRandomOrder()->get();

        // number of teams to create
        $numTeams = intdiv(count($players), $minTeamSize);
        $teams = [];

        // randomly increase team size using remaining available players
        $available = count($players) % $minTeamSize;
        for ($i = 0; $i < $numTeams; $i++) {
            $teams[$i]['size'] = $minTeamSize;
            if ($available > 0) {
                $limit = (($maxTeamSize - $minTeamSize) < $available) ? $maxTeamSize - $minTeamSize : $available;
                $added = rand(0, $limit);
                $teams[$i]['size'] = $minTeamSize + $added;
                $available -= $added;
            }
        }

        // assign team name, coach and goalie to each team.  remove goalie from players object.
        for ($i = 0; $i < $numTeams; $i++) {
            $teams[$i]['name'] = $faker->teamName;
            $teams[$i]['coach'] = count($coaches) > $i ? $coaches[$i]->fullname : 'unknown';
            foreach ($players as $key => $player) {
                if ($player->isGoalie) {
                    $teams[$i]['players'][0]['name'] = $player->fullname;
                    $teams[$i]['players'][0]['ranking'] = $player->ranking;
                    $teams[$i]['players'][0]['position'] = 'goalie';
                    unset($players[$key]);
                    continue 2;
                }
            }
        }

        // perform intial random player distribution and average ranking
        $offset = 0;
        for ($i = 0; $i < $numTeams; $i++) {
            $idx = 1;
            while ($idx < $teams[$i]['size']) {
                if (isset($players[$offset])) {
                    $teams[$i]['players'][$idx]['name'] = $players[$offset]->fullname;
                    $teams[$i]['players'][$idx]['ranking'] = $players[$offset]->ranking;
                    $teams[$i]['players'][$idx]['position'] = 'player';
                    $idx++;
                }
                $offset++;
            }
            $teams[$i]['average'] = $this->CalculateAverage($teams[$i]);
        }

        // balance teams by shuffling players between lowest average and highest average team. limit to 10 iterations.
        $sorting = true;
        $limit = 0;
        while ($sorting && $limit <= 10) {
            // get min-max average keys
            $averages = array_column($teams, 'average');
            $min = array_search(min($averages), $averages);
            $max = array_search(max($averages), $averages);

            // balance min-max teams
            $sorting = $this->BalancePlayers($teams[$min], $teams[$max]);

            // recalculate team average
            $teams[$min]['average'] = $this->CalculateAverage($teams[$min]);
            $teams[$max]['average'] = $this->CalculateAverage($teams[$max]);

            $limit++;
        }

        return view('user', compact('teams'));
    }

    // calculate team average
    private function CalculateAverage($team) : float {
        return array_sum(array_column($team['players'], 'ranking')) / count($team['players']);
    }

    // Balance teams
    private function BalancePlayers(&$minArr, &$maxArr) : bool {
        $gap = round(($maxArr['average'] - $minArr['average']) * 0.5 / (1/count($maxArr['players'])),0);

        // no work to be done
        if ($gap == 0) return false;

        for ($i = $gap; $i > 0; $i--) {
            for ($j = 5; $j > 0; $j--) {
                if ($gap > 0) {
                    $minKey = array_search($j - $i, array_column($minArr['players'], 'ranking'));
                    $maxKey = array_search($j, array_column($maxArr['players'], 'ranking'));
                    if ($minKey && $maxKey) {
                        $this->SwapPlayers($minArr['players'], $minKey, $maxArr['players'], $maxKey);
                        $gap -= $j;
                    }
                }
            }
        }
        
        return true;
    }

    // Swap players by key
    private function SwapPlayers(&$arr1, $key1, &$arr2, $key2) {
        $a = $arr1[$key1];
        $b = $arr2[$key2];
        $arr1[$key1] = $b;
        $arr2[$key2] = $a;
    }
}
