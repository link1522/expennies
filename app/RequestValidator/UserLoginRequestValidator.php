<?php

declare(strict_types=1);

namespace App\RequestValidator;

use Valitron\Validator;
use App\Exceptions\ValidationException;
use App\Contracts\RequestValidatorInterface;

class UserLoginRequestValidator implements RequestValidatorInterface
{
  public function validate(array $data): array
  {
    $v = new Validator($data);

    $v->rule('required', ['email', 'password'])
      ->rule('email', 'email');

    if (!$v->validate()) {
      throw new ValidationException($v->errors());
    }

    return $data;
  }
}
