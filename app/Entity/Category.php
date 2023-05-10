<?php

declare(strict_types=1);

namespace App\Entity;

use App\Contracts\OwnableInterface;
use App\Entity\Traits\HasTimestamp;
use DateTime;
use App\Entity\User;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Table;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;
use Doctrine\Common\Collections\ArrayCollection;

#[Entity, Table('categories')]
#[HasLifecycleCallbacks]
class Category implements OwnableInterface
{
  use HasTimestamp;

  #[Id, Column(options: ['unsigned' => true]), GeneratedValue]
  private int $id;

  #[Column]
  private string $name;

  #[ManyToOne(inversedBy: 'categories')]
  private User $user;

  #[OneToMany(mappedBy: 'category', targetEntity: Transaction::class)]
  private Collection $transactions;

  public function __construct()
  {
    $this->transactions = new ArrayCollection();
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
  public function getName(): string
  {
    return $this->name;
  }

  /**
   * @param string $name 
   * @return self
   */
  public function setName(string $name): self
  {
    $this->name = $name;
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
    $user->addCategory($this);

    $this->user = $user;

    return $this;
  }

  /**
   * @return Collection
   */
  public function getTransactions(): Collection
  {
    return $this->transactions;
  }

  /**
   * @param Transaction $transaction 
   * @return self
   */
  public function addTransaction(Transaction $transaction): self
  {
    $this->transactions->add($transaction);
    return $this;
  }
}
