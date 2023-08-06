<?php

/**
 * Catch errors from form validation and add errors and submitted data to the session for flashing
 * Redirect to referring url
 */

declare(strict_types=1);


namespace App\Middleware;

use Framework\Contracts\MiddlewareInterface;
use Framework\Exceptions\ValidationException;

class ValidationExceptionMiddleware implements MiddlewareInterface
{
  public function process(callable $next)
  {
    // can add a try catch to middleware to catch specific error types and handle accordingly by wrapping next function
    try {
      $next();
    } catch (ValidationException $e) {

      $oldFormData = $_POST;
      $excluded = ["password", "confirmPassword"];
      // Array diff will exclude matching keys from both arrays
      $cleanedData = array_diff_key(
        $oldFormData,
        array_flip($excluded) // Flip to be ["password" => 0, "confirmPassword" => 1] to match the post data
      );

      $_SESSION['errors'] = $e->errors;

      $_SESSION['oldData'] = $cleanedData;

      // referer stores the page that submitted the form - SECURITY ISSUES EXIST
      $referer = $_SERVER['HTTP_REFERER'];
      redirectTo($referer);
    }
  }
}
