<?php

declare(strict_types=1);

namespace App\RequestValidator;

use App\Contracts\RequestValidatorInterFace;
use App\Contracts\RequestValidatorFactoryInterface;
use Psr\Container\ContainerInterface;

class RequestValidatorFactory implements RequestValidatorFactoryInterface
{
  public function __construct(private readonly ContainerInterface $container)
  {
  }

  public function make(string $class): RequestValidatorInterFace
  {
    $validator = $this->container->get($class);

    if ($validator instanceof RequestValidatorInterFace) {
      return $validator;
    }

    throw new \RuntimeException('Failed to instantiate the request validator class"' . $class . '"');
  }
}
