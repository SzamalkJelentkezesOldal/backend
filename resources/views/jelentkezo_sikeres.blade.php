<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <title>Sikeres felvételi értesítés</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            background-color: #f7f7f7; 
            padding: 20px; 
            color: #333; 
        }
        .container { 
            background-color: #ffffff; 
            padding: 20px; 
            border-radius: 5px; 
            max-width: 600px; 
            margin: auto; 
        }
        h1 { 
            color: #00848b; 
        }
        h2 { 
            font-size: 18px; 
            margin-bottom: 10px; 
        }
        .szak {
            margin: 20px 0;
            padding: 15px;
            background-color: #f0f9fa;
            border-left: 4px solid #00848b;
            font-weight: bold;
        }
        p {
            line-height: 1.6;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Sikeres felvételi értesítés</h1>
        
        <h2>Tisztelt {{ $nev }}!</h2>
        
        <p>Örömmel értesítjük, hogy a jelentkezése sikeres volt! Az Ön által preferált szakok közül a következőre nyert felvételt:</p>
        
        <div class="szak">{{ $elfogadottSzakok[0] }}</div>
        
        <p>A felvételivel kapcsolatos további teendőkről hamarosan újabb értesítést küldünk.</p>
        
        <p>Gratulálunk a sikeres felvételihez!</p>
        
        <p>Köszönettel,<br>SZÁMALK-Szalézi Technikum és Szkg.</p>
    </div>
</body>
</html> 