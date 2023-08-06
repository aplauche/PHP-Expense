<?php

declare(strict_types=1);

function dd(mixed $value, bool $die = true)
{
  echo "<pre>";
  print_r($value);
  echo "</pre>";
  if ($die) {
    die();
  }
}

function e(mixed $value): string
{
  return htmlspecialchars((string) $value);
}

function redirectTo(string $path)
{
  header("Location: {$path}");
  http_response_code(302);
  exit;
}
