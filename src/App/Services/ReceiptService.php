<?php

declare(strict_types=1);

namespace App\Services;

use Error;
use Framework\Database;
use Framework\Exceptions\ValidationException;

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

    dd($file);
  }
}
