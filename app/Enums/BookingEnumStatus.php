<?php

namespace App\Enums;

enum BookingEnumStatus: string
{
    case CONFIRME = 'CONFIRME';
    case ANNULE = 'ANNULE';
    case EN_ATTENTE = 'EN_ATTENTE';

}
