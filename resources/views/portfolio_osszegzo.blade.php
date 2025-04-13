<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <title>Portfólió értékelési összegzés</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f7f7f7; padding: 20px; color: #333; }
        .container { background-color: #ffffff; padding: 20px; border-radius: 5px; max-width: 600px; margin: auto; }
        h1 { color: #00848b; }
        h2 { font-size: 18px; margin-bottom: 10px; }
        ul { list-style: none; padding: 0; }
        li { margin-bottom: 5px; }
        .registration-link { margin-top: 20px; }
    </style>
</head>
<body>
<div class="container">
    <h1>Portfólió értékelési összegzés</h1>

    <h2>Tisztelt {{ $jelentkezo->nev }},</h2> 
    <p>A portfóliód értékelése befejeződött. Az alábbiakban láthatod az eredményeket:</p>
    
    @if($elfogadottPortfoliok->count())
        <h3>Elfogadott portfóliók:</h3>
        <ul>
            @foreach($elfogadottPortfoliok as $pf)
                <li>{{ $pf->szak->elnevezes }} – <a href="{{ $pf->portfolio_url }}" target="_blank">{{ $pf->portfolio_url }}</a></li>
            @endforeach
        </ul>
    @endif

    @if($elutasitottPortfoliok->count())
        <h3>Elutasított portfóliók:</h3>
        <ul>
            @foreach($elutasitottPortfoliok as $pf)
                <li>{{ $pf->szak->elnevezes }} – <a href="{{ $pf->portfolio_url }}" target="_blank">{{ $pf->portfolio_url }}</a></li>
            @endforeach
        </ul>
    @endif

    <div class="registration-link">
        <p>Mivel legalább egy portfóliód elfogadásra került, folytathatod a regisztrációt az alábbi linkre kattintva:</p>
        <p><a href="{{ $registrationLink }}" target="_blank">{{ $registrationLink }}</a></p>
    </div>

    <p>Ha kérdésed van, kérjük lépj kapcsolatba a titkársággal.</p>
    <p>Köszönettel,<br>SZÁMALK-Szalézi Technikum és Szkg.</p>
</div>
</body>
</html>
