<?php

namespace App\Events;

class UserEvents
{
    public const BEFORE_CREATE = 'user.before_create';
    public const CREATED = 'user.created';
    public const ACTIVATED = 'user.activated';
    public const REMOVED = 'user.removed';
    public const RECOVER_PASSWORD = 'user.recover_password';
}
