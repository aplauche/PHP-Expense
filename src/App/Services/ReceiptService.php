<?php

declare(strict_types=1);

namespace App\Services;

use Framework\Database;
use Framework\Exceptions\ValidationException;
use App\Config\Paths;

class ReceiptService
{

  public function __construct(private Database $db)
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

  function upload(array $file, int $transaction)
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

    $this->db->query(
      "INSERT INTO receipts(transaction_id, original_filename, storage_filename, media_type)
      VALUES(:transaction_id, :original_filename, :storage_filename, :media_type)",
      [
        'transaction_id' => $transaction,
        'original_filename' => $file["name"],
        'storage_filename' => $newFileName,
        'media_type' => $file['type'],
      ]
    );
  }

  public function getReceipt(string $id)
  {
    $receipt = $this->db->query(
      "SELECT * FROM receipts WHERE id=:id",
      ['id' => $id]
    )->find();

    return $receipt;
  }


  public function read(array $receipt)
  {

    // check if file actually exists
    $filePath = Paths::STORAGE_UPLOADS . "/" . $receipt["storage_filename"];

    if (!file_exists($filePath)) {
      redirectTo('/');
    }

    // tell browser to send non-html file with headers

    // there are two value options: inline will try to render the file, attachment will auto download
    header("Content-Disposition: inline;filename={$receipt['original_filename']}");
    header("Content-Type: {$receipt['media_type']}");

    readfile($filePath);
  }

  public function delete(array $receipt)
  {
    // check if file actually exists
    $filePath = Paths::STORAGE_UPLOADS . "/" . $receipt["storage_filename"];

    if (!file_exists($filePath)) {
      redirectTo('/');
    }

    // This deletes the actual file
    unlink($filePath);

    // delete the db record
    $this->db->query(
      "DELETE FROM receipts WHERE id=:id",
      ["id" => $receipt['id']]
    );
  }
}
