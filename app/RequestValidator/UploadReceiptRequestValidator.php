<?php

declare(strict_types=1);

namespace App\RequestValidator;

use Psr\Http\Message\UploadedFileInterface;
use App\Contracts\RequestValidatorInterface;
use App\Exceptions\ValidationException;
use finfo;

class UploadReceiptRequestValidator implements RequestValidatorInterface
{
  public function validate(array $data): array
  {
    /** @var UploadedFileInterface $uploadedFile */
    $uploadedFile = $data['receipt'] ?? null;

    // 1. validate upload file
    if (!$uploadedFile) {
      throw new ValidationException(['receipt' => ['Please select a receipt file']]);
    }

    if ($uploadedFile->getError() !== UPLOAD_ERR_OK) {
      throw new ValidationException(['receipt' => ['Fail to upload the receipt file']]);
    }

    // 2. validate the file size
    $maxFileSize = 5 * 1024 * 1024;

    if ($uploadedFile->getSize() > $maxFileSize) {
      throw new ValidationException(['receipt' => ['Maximum allow size is 5 MB']]);
    }

    // 3. validate the file name
    $filename = $uploadedFile->getClientFilename();
    if (!preg_match('/^[a-zA-Z0-9\s._-]+$/', $filename)) {
      throw new ValidationException(['receipt' => ['Invalid filename']]);
    }

    // 4. validate the file type
    $allowedMimeTypes = ['image/jpeg', 'image/png', 'application/pdf'];
    $allowedExtensions = ['pdf', 'png', 'jpg', 'jpeg'];
    $tmpFilePath = $uploadedFile->getStream()->getMetadata('uri');

    if (!in_array($uploadedFile->getClientMediaType(), $allowedMimeTypes)) {
      throw new ValidationException(['receipt' => ['Receipt has to be either an image or a pdf document']]);
    }


    if (!in_array($this->getExtension($tmpFilePath), $allowedExtensions)) {
      throw new ValidationException(['receipt' => ['Receipt has to be either "pdf", "png", "jpg", or "jpeg"']]);
    }

    if (!in_array($this->getMimeType($tmpFilePath), $allowedMimeTypes)) {
      throw new ValidationException(['receipt' => ['Invalid file type']]);
    }

    return $data;
  }

  private function getExtension(string $path): string
  {
    return (new finfo(FILEINFO_EXTENSION))->file($path) ?: '';
  }

  private function getMimeType(string $path): string
  {
    return (new finfo(FILEINFO_MIME_TYPE))->file($path) ?: '';
  }
}
