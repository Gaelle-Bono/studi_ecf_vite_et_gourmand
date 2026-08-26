<?php

namespace App\Constant;

class AppConstant
{

    //Footer
    public const DAYS_OF_WEEK = [
        1 => 'Lundi',
        2 => 'Mardi',
        3 => 'Mercredi',
        4 => 'Jeudi',
        5 => 'Vendredi',
        6 => 'Samedi',
        7 => 'Dimanche',
    ];



    //roles
    public const USER = 'ROLE_USER';
    public const ADMIN = 'ROLE_ADMIN';
    public const EMPLOYEE = 'ROLE_EMPLOYEE';

    //create and change password 
    public const MIN_PASSWORD_LENGTH = 10;

    //Order

    public const CLOSED = "L'entreprise est fermée ce jour";
    public const MAX_DELIVERY_DISTANCE_KM = 15;
    public const DELIVERY_BASE_PRICE = '5.00';
    public const DELIVERY_PRICE_PER_KM = '0.59';

    public const DISCOUNT_EXTRA_PEOPLE_THRESHOLD = 5;
    public const GROUP_DISCOUNT_PERCENT = 10;
    public const DISCOUNT_MULTIPLIER = '0.9';

}