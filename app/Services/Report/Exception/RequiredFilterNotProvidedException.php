<?php

namespace App\Services\Report\Exception;

use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class RequiredFilterNotProvidedException extends BadRequestException
{
}
