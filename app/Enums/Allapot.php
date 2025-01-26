<?php

namespace App\Enums;

enum Allapot: string
{
    case JELENTKEZETT = 'Jelentkezett';
    case REGISZTRALT = 'Regisztrált';
    case TORZSADATOK_FELTOLTVE = 'Törzsadatok feltöltve';
    case DOKUMENTUMOK_FELTOLTVE = 'Dokumentumok feltöltve';
    case MODOSITASRA_VAR = 'Módósításra vár';
    case ELFOGADVA = 'Elfogadva';
    case ELUTASITVA = 'Elutasítva';
}