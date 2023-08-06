<?php

declare(strict_types=1);

namespace Framework\Rules;

use Framework\Contracts\RuleInterface;


class URLRule implements RuleInterface
{

  public function validate(array $data, string $field, array $params): bool
  {
    // filter var gives us lots of built in validation options - we must typecast as a bool
    return (bool) filter_var($data[$field], FILTER_VALIDATE_URL);
  }
  public function getMessage(array $data, string $field, array $params): string
  {
    return "Must be a valid URL eg. http://www.example.com";
  }
}
