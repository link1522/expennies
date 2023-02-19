<?php

namespace App\Controllers;

use League\Flysystem\Filesystem;
use Slim\Views\Twig;
use App\Contracts\AuthInterface;
use App\DataObjects\RegisterUserData;
use App\Exceptions\ValidationException;
use App\Contracts\RequestValidatorFactoryInterface;
use App\RequestValidator\UserLoginRequestValidator;
use Psr\Http\Message\ResponseInterface as Response;
use App\RequestValidator\RegisterUserRequestValidator;
use Psr\Http\Message\ServerRequestInterface as Request;

class ReceiptController
{
  public function __construct(private readonly Filesystem $filesystem)
  {
  }
  public function store(Request $request, Response $response, array $args): Response
  {
    $file = $request->getUploadedFiles()['receipt'];

    return $response;
  }
}
