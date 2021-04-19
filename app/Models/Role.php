<?php

namespace App\Models;

final class Role {

    public const ADMIN = 'Admin';
    public const USER  = 'User';

    public const ALL = [
        'Admin' => Role::ADMIN,
        'User' => Role::USER,
    ];
    
}
