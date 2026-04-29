<?php

namespace App\Enum;

enum ContactMethod: string
{
    case EMAIL = 'email';
    case PHONE = 'phone';
    case SMS = 'sms';
}