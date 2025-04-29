<!DOCTYPE html>
<html lang="hu">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Módosítás kérelem</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #f7f7f7;
      padding: 20px;
      color: #333333;
    }
    .container {
      background-color: #ffffff;
      padding: 30px;
      border-radius: 5px;
      max-width: 600px;
      margin: 0 auto;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    h1 {
      color: #00848b;
      font-size: 24px;
      margin-bottom: 15px;
    }
    h3 {
      color: #00848b;
      font-size: 20px;
      margin-top: 25px;
      margin-bottom: 10px;
    }
    p {
      font-size: 16px;
      line-height: 1.5;
      margin-bottom: 20px;
    }
    ul {
      list-style-type: disc;
      margin-left: 20px;
      margin-bottom: 20px;
    }
    li {
      margin-bottom: 8px;
    }
    a {
      color: #00848b;
      text-decoration: none;
      font-weight: bold;
    }
    a:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>
  <div class="container">
    @php
      $fieldNames = [
        'vezeteknev'      => 'Vezetéknév',
        'keresztnev'      => 'Keresztnév',
        'adoazonosito'    => 'Adóazonosító',
        'lakcim'          => 'Lakcím',
        'taj_szam'        => 'TAJ szám',
        'szuletesi_hely'   => 'Születési hely',
        'szuletesi_nev'    => 'Születési név',
        'szuletesi_datum'  => 'Születési dátum',
        'allampolgarsag'   => 'Állampolgárság',
        'anyja_neve'      => 'Anyja neve',
      ];

      $torzsadatReasons = [
        'hibasAdat' => 'hibás adat',
        'egyeni'    => 'egyéni indok'
      ];

      $dokumentumReasons = [
        'hibasAdat'    => 'hibás adat',
        'hiany'        => 'hiányos adat',
        'rosszMinoseg' => 'nem megfelelő képminőség',
        'egyeni'       => 'egyéni indok'
      ];
    @endphp

    <h1>Kedves {{ $data['name'] ?? 'Jelentkező' }},</h1>
    <p>A következő módosítási kérelmeket teljesítsd, hogy folytathasd a beiratkozást:</p>

    @if(isset($data['selectedTorzsadat']) && count($data['selectedTorzsadat']) > 0)
      <h3>Törzsadatok módosítása</h3>
      <ul>
        @foreach($data['selectedTorzsadat'] as $field => $reason)
          <li>
            <strong>{{ $fieldNames[$field] ?? ucfirst($field) }}</strong> mező a törzsadatokban módosítást követel, <strong>{{ $torzsadatReasons[$reason] ?? $reason }}</strong> miatt.
          </li>
        @endforeach
      </ul>
    @endif

    @if(isset($data['selectedDokumentum']) && count($data['selectedDokumentum']) > 0)
      <h3>Dokumentumok módosítása</h3>
      <ul>
        @foreach($data['selectedDokumentum'] as $docId => $reason)
          @php
            $docType = '-';
            if(isset($data['dokumentumok'])) {
              foreach($data['dokumentumok'] as $doc) {
                if($doc['id'] == $docId) {
                  $docType = $doc['dokumentumTipus'] ?? 'Dokumentum';
                  break;
                }
              }
            }
          @endphp
          <li>
            <strong>{{ $docType }}</strong> módosítást követel, <strong>{{ $dokumentumReasons[$reason] ?? $reason }}</strong> miatt.
          </li>
        @endforeach
      </ul>
    @endif

    <p>Köszönettel,<br>SZÁMALK-Szalézi Technikum és Szkg.</p>
  </div>
</body>
</html>
