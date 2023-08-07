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
  }
}
