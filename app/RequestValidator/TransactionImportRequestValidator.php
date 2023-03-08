<?php

declare(strict_types=1);

namespace App\RequestValidator;

use League\MimeTypeDetection\FinfoMimeTypeDetector;
use App\Exceptions\ValidationException;
use Psr\Http\Message\UploadedFileInterface;
use App\Contracts\RequestValidatorInterface;

class TransactionImportRequestValidator implements RequestValidatorInterface
{
  public function validate(array $data): array
  {
    /** @var UploadedFileInterface $uploadFile */
    $uploadFile = $data['importFile'] ?? null;

    if (!$uploadFile) {
      throw new ValidationException(['importFile' => ['Please select a file to import']]);
    }

    if ($uploadFile->getError() !== UPLOAD_ERR_OK) {
      throw new ValidationException(['importFile' => ['Failed to upload the file for import']]);
    }

    $maxFileSize = 20 * 1024 * 1024;

    if ($uploadFile->getSize() > $maxFileSize) {
      throw new ValidationException(['importFile' => ['Maximum allowed size is 20 MB']]);
    }

    $allowedMimeTypes = ['text/csv'];

    if (!in_array($uploadFile->getClientMediaType(), $allowedMimeTypes)) {
      throw new ValidationException(['importFile' => ['Please select a CSV file to import']]);
    }

    $detector = new FinfoMimeTypeDetector();
    $mimeType = $detector->detectMimeTypeFromFile($uploadFile->getStream()->getMetadata('uri'));

    if (!in_array($mimeType, $allowedMimeTypes)) {
      throw new ValidationException(['importFile' => ['Invalid file type']]);
    }

    return $data;
  }
}
