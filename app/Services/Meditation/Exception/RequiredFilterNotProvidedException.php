<?php

namespace App\Services\Meditation\Exception;

use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class RequiredFilterNotProvidedException extends BadRequestException
{
}
