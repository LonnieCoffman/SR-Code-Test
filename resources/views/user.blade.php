<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>SR Fantasy League</title>
        <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="css/app.css">
    </head>
    <body class="antialiased bg-gray-900 text-white">
        <header class="border-b border-gray-800 mb-6">
            <h2 class="text-2xl text-center mt-6 mb-6">SR Fantasy Leagues</h2>
        </header>
        <div class="container mx-auto px-4">
            <div class="grid xl:grid-cols-4 lg:grid-cols-3 md:grid-cols-2 grid-cols-1 gap-5">
                @foreach($teams as $team)
                    <div class="rounded-lg bg-gray-700 shadow-sm transition duration-500 ease-in-out transform hover:-translate-y-1 hover:scale-105">
                        <h2 class="text-2xl bg-gray-800 rounded-lg rounded-b-none p-3 shadow-inner">{{ $team['name'] }}</h2>
                        <div class="p-3">
                            <p class="text-base leading-6">Coach Name: {{ $team['coach'] }}</p>
                            <p class="text-base leading-6">Number of Players: {{ count($team['players']) }}</p>
                            <p class="text-base leading-6">Average Ranking: {{ number_format($team['average'], 2) }}</p>
                        </div>
                        <div class="border-b border-gray-800"></div>
                        <div class="flex p-3">
                            <div class="flex-1">
                                <h3 class="underline">Player</h3>
                                @foreach($team['players'] as $player)
                                    <p>{{ $player['name'] }}@if ($player['position'] == 'goalie') {{ '(Goalie)' }}  @endif</p>
                                @endforeach
                            </div>
                            <div class="text-center pr-3">
                                <h3 class="underline">Rank</h3>
                                @foreach($team['players'] as $player)
                                    <p class="text-center">{{ $player['ranking'] }}</p>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        <footer class="border-t border-gray-800 mt-6"></footer>
    </body>
</html>