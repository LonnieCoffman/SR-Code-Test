<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>SR Fantasy League</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">

        <link rel="stylesheet" href="css/app.css">
    </head>
    <body class="antialiased bg-gray-900 text-white">
      <header class="border-b border-gray-800 mb-6">
        <h2 class="text-2xl text-center mt-6 mb-6">SR Fantasy Leagues</h2>
      </header>
      <div class="container mx-auto px-4">
        <div class="grid-list sm:flex flex-wrap gap-y-6">
          @foreach($teams as $team)
            <div class="grid-item px-3 z-10 w-full md:w-6/12 lg:w-3/12">
             {{-- <div class="text-2xl bg-gray-500">@php preg_match_all('/(?<=\s|^)[A-Z]/', $team['name'], $matches); echo implode('', $matches[0]) @endphp</div> --}}
             <h2 class="text-2xl underline mb-2">{{ $team['name'] }}</h2>
             <p class="text-base leading-6">Coach Name: {{ $team['coach'] }}</p>
             <p class="text-base leading-6">Number of Players: {{ count($team['players']) }}</p>
             <p class="text-base leading-6  border-b border-gray-800 mb-3 pb-3">Team Average: {{ number_format($team['average'], 2) }}</p>
             @foreach($team['players'] as $player)
              <p class="text-base leading-6">{{ $player['name'] }}@if ($player['position'] == 'goalie') {{ '(Goalie)' }}  @endif: {{ $player['ranking'] }}</p>
             @endforeach
            </div>
            <!--/grid item-->
          @endforeach
        </div>
      </div>
      <footer class="border-t border-gray-800 mt-6"></footer>
    </body>
</html>