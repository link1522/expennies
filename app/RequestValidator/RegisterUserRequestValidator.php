<?php

declare(strict_types=1);

namespace App\RequestValidator;

use App\Entity\User;
use Valitron\Validator;
use App\Exceptions\ValidationException;
use App\Contracts\RequestValidatorInterface;
use Doctrine\ORM\EntityManagerInterface;

class RegisterUserRequestValidator implements RequestValidatorInterface
{
  public function __construct(private readonly EntityManagerInterface $entityManager)
  {
  }

  public function validate(array $data): array
  {
    $v = new Validator($data);

    $v->rule('required', ['name', 'email', 'password', 'confirmPassword'])
      ->rule('email', 'email')
      ->rule('equals', 'confirmPassword', 'password')->label('Confirm Password')
      ->rule(
        fn ($field, $value) => !$this->entityManager->getRepository(User::class)->count(['email' => $value]),
        'email'
      )->message('User with the given email is already exists');

    if (!$v->validate()) {
      throw new ValidationException($v->errors());
    }

    return $data;
  }
}
