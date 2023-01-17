<?php

declare(strict_types=1);

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Table;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\GeneratedValue;

#[Entity, Table('receipts')]
class Receipt {
  #[Id, Column(options: ['unsigned' => true]), GeneratedValue]
  private int $id;

  #[Column(name: 'file_name')]
  private string $fileName;

  #[Column(name: 'created_at')]
  private \DateTime $createdAt;

  #[ManyToOne(inversedBy: 'receipts')]
  private Transaction $transaction;

  /**
   * @return int
   */
  public function getId(): int {
    return $this->id;
  }

  /**
   * @return string
   */
  public function getFileName(): string {
    return $this->fileName;
  }

  /**
   * @param string $fileName 
   * @return self
   */
  public function setFileName(string $fileName): self {
    $this->fileName = $fileName;
    return $this;
  }

  /**
   * @return DateTime
   */
  public function getCreatedAt(): DateTime {
    return $this->createdAt;
  }

  /**
   * @param DateTime $createdAt 
   * @return self
   */
  public function setCreatedAt(DateTime $createdAt): self {
    $this->createdAt = $createdAt;
    return $this;
  }

  /**
   * @return Transaction
   */
  public function getTransaction(): Transaction {
    return $this->transaction;
  }

  /**
   * @param Transaction $transaction 
   * @return self
   */
  public function setTransaction(Transaction $transaction): self {
    $transaction->addReceipt($this);

    $this->transaction = $transaction;

    return $this;
  }
}
