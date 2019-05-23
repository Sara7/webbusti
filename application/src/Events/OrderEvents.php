<?php

namespace App\Events;

class OrderEvents
{
    public const BEFORE_CREATE = 'order.before_create';
    public const CREATED = 'order.created';
    public const PAID = 'order.paid';
    public const SHIPPED = 'order.shipped';
    public const DELETED = 'order.deleted';
}
