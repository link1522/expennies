<?php

declare(strict_types=1);

namespace App\services;

use App\DataObjects\DataTableQueryParams;
use App\Entity\User;
use App\Entity\Category;
use App\Entity\Receipt;
use App\Entity\Transaction;
use DateTime;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Pagination\Paginator;

class ReceiptService
{
  public function __construct(private readonly EntityManager $entityManager)
  {
  }

  public function create(Transaction $transaction, string $filename, string $storageFilename): Receipt
  {
    $receipt = new Receipt();

    $receipt->setTransaction($transaction);
    $receipt->setFilename($filename);
    $receipt->setStorageFilename($storageFilename);
    $receipt->setCreatedAt(new DateTime());

    $this->entityManager->persist($receipt);
    $this->entityManager->flush();

    return $receipt;
  }
}
