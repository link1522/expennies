<?php

declare(strict_types=1);

namespace App\RequestValidator;

use Valitron\Validator;
use App\Exceptions\ValidationException;
use App\Contracts\RequestValidatorInterface;

class UpdateCategoryRequestValidator implements RequestValidatorInterface
{
  public function validate(array $data): array
  {
    $v = new Validator($data);

    $v->rule('required', 'name');
    $v->rule('lengthMax', 'name', 50);

    if (!$v->validate()) {
      throw new ValidationException($v->errors());
    }

    return $data;
  }
}
