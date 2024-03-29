<?php

declare(strict_types=1);

use Slim\App;
use App\Middleware\AuthMiddleware;
use App\Controllers\AuthController;
use App\Controllers\HomeController;
use App\Middleware\GuestMiddleware;
use App\Controllers\VerifyController;
use Slim\Routing\RouteCollectorProxy;
use App\Controllers\ReceiptController;
use App\Controllers\CategoryController;
use App\Controllers\TransactionController;
use App\Controllers\TransactionImportController;
use App\Middleware\ValidateSignatureMiddleware;
use App\Middleware\VerifyEmailMiddleware;

return function (App $app) {
  $app->group('', function (RouteCollectorProxy $group) {
    $group->get('/', [HomeController::class, 'index']);

    $group->group('/categories', function (RouteCollectorProxy $categories) {
      $categories->get('', [CategoryController::class, 'index']);
      $categories->get('/load', [CategoryController::class, 'load']);
      $categories->post('', [CategoryController::class, 'store']);
      $categories->delete('/{category}', [CategoryController::class, 'delete']);
      $categories->get('/{category}', [CategoryController::class, 'get']);
      $categories->post('/{category}', [CategoryController::class, 'update']);
    });

    $group->group('/transactions', function (RouteCollectorProxy $transactions) {
      $transactions->get('', [TransactionController::class, 'index']);
      $transactions->get('/load', [TransactionController::class, 'load']);
      $transactions->post('', [TransactionController::class, 'store']);
      $transactions->post('/import', [TransactionImportController::class, 'import']);
      $transactions->delete('/{transaction}', [TransactionController::class, 'delete']);
      $transactions->get('/{transaction}', [TransactionController::class, 'get']);
      $transactions->post('/{transaction}', [TransactionController::class, 'update']);
      $transactions->post('/{transaction}/receipts', [ReceiptController::class, 'store']);
      $transactions->get('/{transaction}/receipts/{receipt}', [ReceiptController::class, 'download']);
      $transactions->delete('/{transaction}/receipts/{receipt}', [ReceiptController::class, 'delete']);
      $transactions->post('/{transaction}/review', [TransactionController::class, 'toggleReviewed']);
    });
  })->add(VerifyEmailMiddleware::class)->add(AuthMiddleware::class);


  $app->group('', function (RouteCollectorProxy $group) {
    $group->post('/logout', [AuthController::class, 'logout']);
    $group->get('/verify', [VerifyController::class, 'index']);
    $group->get('/verify/{id}/{hash}', [VerifyController::class, 'verify'])
      ->setName('verify')
      ->add(ValidateSignatureMiddleware::class);
  })->add(AuthMiddleware::class);

  $app->group('', function (RouteCollectorProxy $guest) {
    $guest->get('/login', [AuthController::class, 'loginView']);
    $guest->get('/register', [AuthController::class, 'registerView']);
    $guest->post('/login', [AuthController::class, 'login']);
    $guest->post('/register', [AuthController::class, 'register']);
  })->add(GuestMiddleware::class);
};
