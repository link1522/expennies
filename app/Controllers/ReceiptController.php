<?php

namespace App\Controllers;

use Slim\Psr7\Stream;
use App\services\ReceiptService;
use League\Flysystem\Filesystem;
use App\services\TransactionService;
use App\Contracts\EntityManagerServiceInterface;
use App\Entity\Receipt;
use App\Entity\Transaction;
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
    private readonly ReceiptService $receiptService,
    private readonly EntityManagerServiceInterface $entityManagerService
  ) {
  }
  public function store(Request $request, Response $response, Transaction $transaction): Response
  {
    $file = $this->requestValidatorFactory->make(UploadReceiptRequestValidator::class)->validate(
      $request->getUploadedFiles()
    )['receipt'];
    $filename = $file->getClientFileName();

    $randomFilename = bin2hex(random_bytes(25));

    $this->filesystem->write('receipts/' . $randomFilename, $file->getStream()->getContents());

    $receipt = $this->receiptService->create($transaction, $filename, $randomFilename, $file->getClientMediaType());

    $this->entityManagerService->sync($receipt);

    return $response;
  }

  public function download(Response $response, Transaction $transaction, Receipt $receipt): Response
  {
    if ($receipt->getTransaction()->getId() !== $transaction->getId()) {
      return $response->withStatus(401);
    }

    $file = $this->filesystem->readStream('receipts/' . $receipt->getStorageFilename());

    $response = $response->withHeader(
      'Content-Disposition',
      'inline; filename="' . $receipt->getFilename() . '"'
    )->withHeader('Content-Type', $receipt->getMediaType());

    return $response->withBody(new Stream($file));
  }

  public function delete(Response $response, Transaction $transaction, Receipt $receipt): Response
  {
    if ($receipt->getTransaction()->getId() !== $transaction->getId()) {
      return $response->withStatus(401);
    }

    $this->filesystem->delete('receipts/' . $receipt->getStorageFilename());

    $this->entityManagerService->delete($receipt, true);

    return $response;
  }
}
