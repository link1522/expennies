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
class Receipt
{
  #[Id, Column(options: ['unsigned' => true]), GeneratedValue]
  private int $id;

  #[Column]
  private string $filename;

  #[Column(name: 'storage_filename')]
  private string $storageFilename;

  #[Column(name: 'media_type')]
  private string $mediaType;

  #[Column(name: 'created_at')]
  private DateTime $createdAt;

  #[ManyToOne(inversedBy: 'receipts')]
  private Transaction $transaction;

  /**
   * @return int
   */
  public function getId(): int
  {
    return $this->id;
  }

  /**
   * @return string
   */
  public function getFilename(): string
  {
    return $this->filename;
  }

  /**
   * @param string $filename 
   * @return self
   */
  public function setFilename(string $filename): self
  {
    $this->filename = $filename;
    return $this;
  }

  /**
   * @return DateTime
   */
  public function getCreatedAt(): DateTime
  {
    return $this->createdAt;
  }

  /**
   * @param DateTime $createdAt 
   * @return self
   */
  public function setCreatedAt(DateTime $createdAt): self
  {
    $this->createdAt = $createdAt;
    return $this;
  }

  /**
   * @return Transaction
   */
  public function getTransaction(): Transaction
  {
    return $this->transaction;
  }

  /**
   * @param Transaction $transaction 
   * @return self
   */
  public function setTransaction(Transaction $transaction): self
  {
    $transaction->addReceipt($this);

    $this->transaction = $transaction;

    return $this;
  }

  /**
   * @return string
   */
  public function getStorageFilename(): string
  {
    return $this->storageFilename;
  }

  /**
   * @param string $storageFilename 
   * @return self
   */
  public function setStorageFilename(string $storageFilename): self
  {
    $this->storageFilename = $storageFilename;
    return $this;
  }

  /**
   * @return string
   */
  public function getMediaType(): string
  {
    return $this->mediaType;
  }

  /**
   * @param string $mediaType 
   * @return self
   */
  public function setMediaType(string $mediaType): self
  {
    $this->mediaType = $mediaType;
    return $this;
  }
}
