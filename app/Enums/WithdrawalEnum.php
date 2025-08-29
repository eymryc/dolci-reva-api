<?php

namespace App\Enums;

enum WithdrawalEnum: string
{
    case PENDING = 'PENDING';
    case APPROVED = 'APPROVED';
    case REJECTED = 'REJECTED';

}
