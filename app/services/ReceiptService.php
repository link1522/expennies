<?php

declare(strict_types=1);

namespace App\services;

use App\Entity\Receipt;
use App\Entity\Transaction;
use DateTime;
use Doctrine\ORM\EntityManager;

class ReceiptService
{
  public function __construct(private readonly EntityManager $entityManager)
  {
  }

  public function create(Transaction $transaction, string $filename, string $storageFilename, string $mediaType): Receipt
  {
    $receipt = new Receipt();

    $receipt->setTransaction($transaction);
    $receipt->setFilename($filename);
    $receipt->setStorageFilename($storageFilename);
    $receipt->setMediaType($mediaType);
    $receipt->setCreatedAt(new DateTime());

    $this->entityManager->persist($receipt);
    $this->entityManager->flush();

    return $receipt;
  }

  public function getById(int $id): ?Receipt
  {
    return $this->entityManager->find(Receipt::class, $id);
  }

  public function delete(Receipt $receipt): void
  {
    $this->entityManager->remove($receipt);
    $this->entityManager->flush();
  }
}
