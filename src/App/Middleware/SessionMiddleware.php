<?php

/**
 * Check for a session, if none go ahead and create one so we can store data in the session variable
 */


declare(strict_types=1);


namespace App\Middleware;

use App\Exceptions\SessionException;
use Framework\Contracts\MiddlewareInterface;


class SessionMiddleware implements MiddlewareInterface
{

  public function process(callable $next)
  {
    // check if session already exists
    if (session_status() === PHP_SESSION_ACTIVE) {
      throw new SessionException("Session Already Active");
    }

    // php sends data in chunks, if data has already been sent, this will be thrown

    // to prevent, don't echo before the session runs, or enable output buffering on server through php.ini
    if (headers_sent($filename, $line)) {
      // headers sent can accept a filename and line and will automatically provide the source of the info sent.
      throw new SessionException("Headers Already Sent from {$filename}, Line {$line}. Consider turning on Output Buffering.");
    }

    session_start();
    $next();

    // this tells php to write session data, but then close it - performance boost
    session_write_close();
  }
}
