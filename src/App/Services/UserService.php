<?php

declare(strict_types=1);


namespace App\Services;

use Framework\Database;
use Framework\Exceptions\ValidationException;

class UserService
{

  public function __construct(private Database $db)
  {
  }

  public function isEmailTaken(string $email)
  {
    $emailCount = $this->db->query(
      "SELECT COUNT(*) FROM users WHERE email = :email",
      ['email' => $email]
    )->count();

    if ($emailCount > 0) {
      throw new ValidationException([
        'email' => "Email is taken"
      ]);
    }
  }

  public function createUser(array $formData)
  {

    // Bcrypt is intentionally slow, 'cost' will ramp up the slowdown. 10 is default.
    // password_hash auto salts password so we don't have to

    $hashed = password_hash($formData["password"], PASSWORD_BCRYPT, ['cost' => 12]);

    $this->db->query(
      "INSERT INTO users(email, password, age, country, social_media_url) 
      VALUES(:email, :password, :age, :country, :social_media_url)",
      [
        "email" => $formData["email"],
        "password" => $hashed,
        "age" => $formData["age"],
        "country" => $formData["country"],
        "social_media_url" => $formData["socialMediaURL"],
      ]
    );

    // auto login after registering
    session_regenerate_id();

    $_SESSION['user'] = $this->db->lastId();
  }

  public function login(array $formData)
  {
    // get user with matching email
    $user = $this->db->query(
      "SELECT * FROM users WHERE email = :email",
      ['email' => $formData['email']]
    )->find();

    // compare submitted password with hashed pw in database using password_verify function
    $passwordsMatch = password_verify($formData['password'], $user['password'] ?? '');

    if (!$user || !$passwordsMatch) {
      // pass in our own custom error message to not reveal anything about which field failed
      throw new ValidationException(['password' => ['invalid credentials']]);
    }

    // this refreshes session so hackers cannot use old hijacked cookies
    // change it on every login
    session_regenerate_id();

    // assign the user ID to session
    $_SESSION['user'] = $user['id'];
  }

  public function logout()
  {
    /* 
    METHOD 1
    // unset can seletivley delete session data
    unset($_SESSION['user']); 
    // Regen ID as extra security precaution
    session_regenerate_id();
    */

    /*
    METHOD 2
    Deletes everything, full session and cookie in browser
    */

    // delete session
    session_destroy();

    // we can also delete the browser cookie as well
    $params = session_get_cookie_params(); // grab current cookie info
    setcookie(
      "PHPSESSID",
      "",
      time() - 3600, // expiration to time in the past
      $params['path'],
      $params['domain'],
      $params['secure'],
      $params['httponly']
    );
  }
}
