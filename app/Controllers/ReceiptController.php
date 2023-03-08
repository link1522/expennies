<?php

namespace App\Controllers;

use Slim\Psr7\Stream;
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

    $this->receiptService->create($transaction, $filename, $randomFilename, $file->getClientMediaType());

    return $response;
  }

  public function download(Request $request, Response $response, array $args): Response
  {
    $transactionId = (int) $args['transactionId'];
    $receiptId = (int) $args['id'];

    if (!$transactionId || !($this->transactionService->getById($transactionId))) {
      return $response->withStatus(404);
    }

    if (!$receiptId || !($receipt = $this->receiptService->getById($receiptId))) {
      return $response->withStatus(404);
    }

    if ($receipt->getTransaction()->getId() !== $transactionId) {
      return $response->withStatus(401);
    }

    $file = $this->filesystem->readStream('receipts/' . $receipt->getStorageFilename());

    $response = $response->withHeader(
      'Content-Disposition',
      'inline; filename="' . $receipt->getFilename() . '"'
    )->withHeader('Content-Type', $receipt->getMediaType());

    return $response->withBody(new Stream($file));
  }

  public function delete(Request $request, Response $response, array $args): Response
  {
    $transactionId = (int) $args['transactionId'];
    $receiptId = (int) $args['id'];

    if (!$transactionId || !$this->transactionService->getById($transactionId)) {
      return $response->withStatus(400);
    }

    if (!$receiptId || !($receipt = $this->receiptService->getById($receiptId))) {
      return $response->withStatus(400);
    }

    if ($receipt->getTransaction()->getId() !== $transactionId) {
      return $response->withStatus(401);
    }

    $this->filesystem->delete('receipts/' . $receipt->getStorageFilename());

    $this->receiptService->delete($receipt);

    return $response;
  }
}
