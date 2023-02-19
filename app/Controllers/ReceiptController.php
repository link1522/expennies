<?php

namespace App\Controllers;

use League\Flysystem\Filesystem;
use Psr\Http\Message\ResponseInterface as Response;
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
