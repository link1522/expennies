<?php

declare(strict_types=1);

namespace App\Contracts;

use App\Entity\User;
use App\DataObjects\RegisterUserData;

interface UserProviderServiceInterface
{
  public function getById(int $userId): ?UserInterface;

  public function getByCredentials(array $credentials): ?UserInterface;

  public function createUser(RegisterUserData $data): UserInterface;

  public function verifyUser(User $user): void;
}
