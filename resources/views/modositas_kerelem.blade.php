<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Módosítás kérelem</title>
  <style>
    body { font-family: Arial, sans-serif; }
    h1, h3 { color: #333; }
    ul { list-style-type: disc; margin-left: 20px; }
  </style>
</head>
<body>
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
          <strong>{{ $fieldNames[$field] ?? ucfirst($field) }}</strong> mező a törzsadatokban módosítást követel, 
          <strong>{{ $torzsadatReasons[$reason] ?? $reason }}</strong> miatt.
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
</body>
</html>
