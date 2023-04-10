<?php

declare(strict_types=1);

namespace App\services;

use DateTime;
use App\Entity\Receipt;
use App\Entity\Transaction;
use App\services\EntityManagerService;

class ReceiptService extends EntityManagerService
{
  public function create(Transaction $transaction, string $filename, string $storageFilename, string $mediaType): Receipt
  {
    $receipt = new Receipt();

    $receipt->setTransaction($transaction);
    $receipt->setFilename($filename);
    $receipt->setStorageFilename($storageFilename);
    $receipt->setMediaType($mediaType);
    $receipt->setCreatedAt(new DateTime());

    $this->entityManager->persist($receipt);

    return $receipt;
  }

  public function getById(int $id): ?Receipt
  {
    return $this->entityManager->find(Receipt::class, $id);
  }

  public function delete(Receipt $receipt): void
  {
    $this->entityManager->remove($receipt);
  }
}
