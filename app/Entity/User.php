<?php

declare(strict_types=1);

namespace App\Entity;

use App\Contracts\UserInterface;
use App\Entity\Traits\HasTimestamp;
use DateTime;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Table;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;

#[Entity, Table('users')]
#[HasLifecycleCallbacks]
class User implements UserInterface
{
  use HasTimestamp;

  #[Id, Column(options: ['unsigned' => true]), GeneratedValue]
  private int $id;

  #[Column]
  private string $email;

  #[Column]
  private string $password;

  #[Column]
  private string $name;

  #[OneToMany(mappedBy: 'user', targetEntity: Transaction::class)]
  private Collection $transactions;

  #[OneToMany(mappedBy: 'user', targetEntity: Category::class)]
  private Collection $categories;

  public function __construct()
  {
    $this->transactions = new ArrayCollection();
    $this->categories = new ArrayCollection();
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
  public function getEmail(): string
  {
    return $this->email;
  }

  /**
   * @param string $email 
   * @return self
   */
  public function setEmail(string $email): self
  {
    $this->email = $email;
    return $this;
  }

  /**
   * @return string
   */
  public function getPassword(): string
  {
    return $this->password;
  }

  /**
   * @param string $password 
   * @return self
   */
  public function setPassword(string $password): self
  {
    $this->password = $password;
    return $this;
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
   * @return Collection
   */
  public function getCategories(): Collection
  {
    return $this->categories;
  }

  /**
   * @param Category $category
   * @return self
   */
  public function addCategory(Category $category): self
  {
    $this->categories->add($category);
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
