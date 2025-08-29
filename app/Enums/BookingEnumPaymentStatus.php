<?php

namespace App\Enums;

enum BookingEnumPaymentStatus: string
{
    case PAYE = 'PAYE';
    case REMBOURSE = 'REMBOURSE';
    case EN_ATTENTE = 'EN_ATTENTE';
}
