<?php

declare(strict_types=1);

namespace App\Entity;

use App\Contracts\UserInterface;
use DateTime;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Table;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;
use Doctrine\ORM\Mapping\PrePersist;
use Doctrine\ORM\Mapping\PreUpdate;

#[Entity, Table('users')]
#[HasLifecycleCallbacks]
class User implements UserInterface
{
  #[Id, Column(options: ['unsigned' => true]), GeneratedValue]
  private int $id;

  #[Column]
  private string $email;

  #[Column]
  private string $password;

  #[Column]
  private string $name;

  #[Column(name: 'created_at')]
  private DateTime $createdAt;

  #[Column(name: 'updated_at')]
  private DateTime $updatedAt;

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

  #[PrePersist, PreUpdate]
  public function updateTimestamps(LifecycleEventArgs $args)
  {
    if (!isset($this->createdAt)) {
      $this->createdAt = new DateTime();
    }

    $this->updatedAt = new DateTime();
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
   * @param DateTime $createdAt 
   * @return self
   */
  public function setCreatedAt(DateTime $createdAt): self
  {
    $this->createdAt = $createdAt;
    return $this;
  }

  /**
   * @return DateTime
   */
  public function getUpdatedAt(): DateTime
  {
    return $this->updatedAt;
  }

  /**
   * @param DateTime $updatedAt 
   * @return self
   */
  public function setUpdatedAt(DateTime $updatedAt): self
  {
    $this->updatedAt = $updatedAt;
    return $this;
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
