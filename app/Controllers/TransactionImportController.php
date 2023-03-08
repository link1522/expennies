<?php

namespace App\Controllers;

use App\services\CategoryService;
use App\DataObjects\TransactionData;
use App\services\TransactionService;
use Psr\Http\Message\UploadedFileInterface;
use App\Contracts\RequestValidatorFactoryInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\RequestValidator\TransactionImportRequestValidator;

class TransactionImportController
{
  public function __construct(
    private readonly RequestValidatorFactoryInterface $requestValidatorFactory,
    private readonly TransactionService $transactionService,
    private readonly CategoryService $categoryService
  ) {
  }

  public function import(Request $request, Response $response): Response
  {
    /** @var UploadedFileInterface $file */
    $file = $this->requestValidatorFactory->make(TransactionImportRequestValidator::class)->validate(
      $request->getUploadedFiles()
    )['importFile'];

    $user = $request->getAttribute('user');
    $resource = fopen($file->getStream()->getMetadata('uri'), 'r');

    fgetcsv($resource);

    while (($row = fgetcsv($resource)) !== false) {
      [$date, $description, $category, $amount] = $row;

      $date = new \DateTime($date);
      $category = $this->categoryService->findByName($category);
      $amount = str_replace(['$', ','], '', $amount);

      $transactionData = new TransactionData($description, (float) $amount, $date, $category);

      $this->transactionService->create($transactionData, $user);
    }

    return $response;
  }
}
