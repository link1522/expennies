<?php

namespace App\Controllers;

use League\Flysystem\Filesystem;
use App\RequestValidator\RequestValidatorFactory;
use Psr\Http\Message\ResponseInterface as Response;
use App\RequestValidator\UploadReceiptRequestValidator;
use Psr\Http\Message\ServerRequestInterface as Request;

class ReceiptController
{
  public function __construct(
    private readonly Filesystem $filesystem,
    private readonly RequestValidatorFactory $requestValidatorFactory
  ) {
  }
  public function store(Request $request, Response $response, array $args): Response
  {
    $file = $this->requestValidatorFactory->make(UploadReceiptRequestValidator::class)->validate(
      $request->getUploadedFiles()
    )['receipt'];
    $fileName = $file->getClientFileName();

    $this->filesystem->write('receipts/' . $fileName, $file->getStream()->getContents());

    return $response;
  }
}
