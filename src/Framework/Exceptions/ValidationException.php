<?php


declare(strict_types=1);


namespace Framework\Exceptions;

// runtime is same as exception but by convention notates an error that does not need to fixed, just handled
use RuntimeException;

// this is all very optional, but categorizing errors makes it more helpful
class ValidationException extends RuntimeException
{
  public function __construct(int $code = 422)
  {
    parent::__construct(code: $code);
  }
}
