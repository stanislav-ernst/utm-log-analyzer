<?php

namespace App\Enums;

enum ResultType: string
{
    case LICENSE_ACCESS = 'license_access';
    case MULTI_DEVICE = 'multi_device';
    case HARDWARE_CLASS = 'hardware_class';
}
