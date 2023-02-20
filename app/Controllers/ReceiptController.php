<?php

namespace App\Controllers;

use App\services\ReceiptService;
use League\Flysystem\Filesystem;
use App\services\TransactionService;
use App\RequestValidator\RequestValidatorFactory;
use Psr\Http\Message\ResponseInterface as Response;
use App\RequestValidator\UploadReceiptRequestValidator;
use Psr\Http\Message\ServerRequestInterface as Request;

class ReceiptController
{
  public function __construct(
    private readonly Filesystem $filesystem,
    private readonly RequestValidatorFactory $requestValidatorFactory,
    private readonly TransactionService $transactionService,
    private readonly ReceiptService $receiptService
  ) {
  }
  public function store(Request $request, Response $response, array $args): Response
  {
    $file = $this->requestValidatorFactory->make(UploadReceiptRequestValidator::class)->validate(
      $request->getUploadedFiles()
    )['receipt'];
    $filename = $file->getClientFileName();

    $id = (int) $args['id'];

    if (!$id || !($transaction = $this->transactionService->getById($id))) {
      return $response->withStatus(404);
    }

    $randomFilename = bin2hex(random_bytes(25));

    $this->filesystem->write('receipts/' . $randomFilename, $file->getStream()->getContents());

    $this->receiptService->create($transaction, $filename, $randomFilename);

    return $response;
  }
}
