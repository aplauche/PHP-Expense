<?php

declare(strict_types=1);

namespace App\Services;

use Framework\Database;
use Framework\Exceptions\ValidationException;
use App\Config\Paths;

class ReceiptService
{

  public function __construct(private Database $database)
  {
  }

  public function validateFile(?array $file)
  {

    // Check if no file, or if there is an error with the upload - upload err ok means eveything good

    if (!$file || $file['error'] !== UPLOAD_ERR_OK) {
      throw new ValidationException([
        'receipt' => ['Failed to upload file']
      ]);
    }

    // Check the file size to make sure it is not too big

    $maxFileSize = 3;  //3mb max
    // filesize stored in bytes, we multiply by 1024 to get kb, then again for bytes
    if ($file['size'] > $maxFileSize * 1024 * 1024) {
      throw new ValidationException([
        'receipt' => ['File upload is too big']
      ]);
    }

    // Validate the filename to get rid of special chars

    $originalFileName = $file['name'];

    // a-z 0-9 spaces (\s) and .-_ chars are okay. ^ anchors to beg. + means any number chars, and $ anchors to end.
    // this means all chars in filename must match the allowed chars.
    if (!preg_match('/^[A-Za-z0-9\s.-_]+$/', $originalFileName)) {
      throw new ValidationException([
        'receipt' => ['File name contains disallowed characters']
      ]);
    }

    // Finally, lets validate MIME type
    $clientMimeType = $file['type'];
    $allowedMimeTypes = ['image/jpeg', 'image/png', 'application/pdf'];

    if (!in_array($clientMimeType, $allowedMimeTypes)) {
      throw new ValidationException([
        'receipt' => ['File type is not allowed. Please use a jpeg, png, or pdf']
      ]);
    }
  }

  function upload(array $file)
  {

    // Generate a random filename to make sure overrides do not accidently happen

    // random bytes generates random binary machine code - bin2hex makes it a readable filename

    $fileExtension = pathinfo($file['name'], PATHINFO_EXTENSION); // extract the extension

    $newFileName = bin2hex(random_bytes(16)) . "." . $fileExtension;


    // Store the actual file to disk
    // we do not use public folder to avoid users being able to download other user's files

    $uploadPath = Paths::STORAGE_UPLOADS . "/" . $newFileName; //create full system path for where we want file

    // move_uploaded_file will return bool if successful or not
    // $file['tmp_name'] is the path for temp file storage
    if (!move_uploaded_file($file['tmp_name'], $uploadPath)) {
      throw new ValidationException([
        'receipt' => ['Your file was not successfully uploaded.']
      ]);
    }
  }
}
