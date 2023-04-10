<?php

declare(strict_types=1);

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping\Id;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\Table;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use App\Entity\Traits\HasTimestamp;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;
use Doctrine\Common\Collections\ArrayCollection;

#[Entity, Table('transactions')]
#[HasLifecycleCallbacks]
class Transaction
{
  use HasTimestamp;

  #[Id, Column(options: ['unsigned' => true]), GeneratedValue]
  private int $id;

  #[Column(name: 'was_reviewed', options: ['default' => 0])]
  private bool $wasReviewed;

  #[Column]
  private string $description;

  #[Column]
  private DateTime $date;

  #[Column(name: 'amount', type: Types::DECIMAL, precision: 13, scale: 3)]
  private float $amount;

  #[Column(name: 'created_at')]
  private DateTime $createdAt;

  #[Column(name: 'updated_at')]
  private DateTime $updatedAt;

  #[ManyToOne(inversedBy: 'transactions')]
  private User $user;

  #[ManyToOne(inversedBy: 'transactions')]
  private ?Category $category;

  #[OneToMany(mappedBy: 'transaction', targetEntity: Receipt::class)]
  private Collection $receipts;

  public function __construct()
  {
    $this->receipts = new ArrayCollection();
  }

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
  public function getDescription(): string
  {
    return $this->description;
  }

  /**
   * @param string $description 
   * @return self
   */
  public function setDescription(string $description): self
  {
    $this->description = $description;
    return $this;
  }

  /**
   * @return DateTime
   */
  public function getDate(): DateTime
  {
    return $this->date;
  }

  /**
   * @param DateTime $date 
   * @return self
   */
  public function setDate(DateTime $date): self
  {
    $this->date = $date;
    return $this;
  }

  /**
   * @return float
   */
  public function getAmount(): float
  {
    return $this->amount;
  }

  /**
   * @param float $amount 
   * @return self
   */
  public function setAmount(float $amount): self
  {
    $this->amount = $amount;
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
   * @return DateTime
   */
  public function getUpdatedAt(): DateTime
  {
    return $this->updatedAt;
  }

  /**
   * @return User
   */
  public function getUser(): User
  {
    return $this->user;
  }

  /**
   * @param User $user 
   * @return self
   */
  public function setUser(User $user): self
  {
    // $user->addTransaction($this);

    $this->user = $user;

    return $this;
  }

  /**
   * @return Category
   */
  public function getCategory(): ?Category
  {
    return $this->category;
  }

  /**
   * @param Category $category 
   * @return self
   */
  public function setCategory(?Category $category): self
  {
    // $category?->addTransaction($this);

    $this->category = $category;

    return $this;
  }

  /**
   * @return Collection
   */
  public function getReceipts(): Collection
  {
    return $this->receipts;
  }

  /**
   * @param Receipt $receipt
   * @return self
   */
  public function addReceipt(Receipt $receipt): self
  {
    $this->receipts->add($receipt);
    return $this;
  }

  /**
   * @return bool
   */
  public function wasReviewed(): bool
  {
    return $this->wasReviewed;
  }

  /**
   * @param bool $wasReviewed 
   * @return self
   */
  public function setReviewed(bool $wasReviewed): self
  {
    $this->wasReviewed = $wasReviewed;
    return $this;
  }
}
