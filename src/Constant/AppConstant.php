<?php

namespace App\Constant;

class AppConstant
{
    //roles
    public const USER = 'ROLE_USER';
    public const ADMIN = 'ROLE_ADMIN';
    public const EMPLOYEE = 'ROLE_EMPLOYEE';

    //create and change password 
    public const MIN_PASSWORD_LENGTH = 10;

    //Order
    public const DEFAULT_REQUESTED_TIME = '12:00';
    public const DELIVERY_BASE_PRICE = '5.00';
    public const DELIVERY_PRICE_PER_KM = '0.59';

    public const DISCOUNT_EXTRA_PEOPLE_THRESHOLD = 5;
    public const GROUP_DISCOUNT_PERCENT = 10;
    public const DISCOUNT_MULTIPLIER = '0.9';

}