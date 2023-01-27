<?php

declare(strict_types=1);

namespace App\Middleware;

use App\services\RequestService;
use App\Contracts\SessionInterface;
use App\Exceptions\ValidationException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseFactoryInterface;

class ValidationExceptionMiddleware implements MiddlewareInterface
{

  public function __construct(
    private readonly ResponseFactoryInterface $responseFactor,
    private readonly SessionInterface $session,
    private readonly RequestService $requestService
  ) {
  }
  public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
  {
    try {
      return $handler->handle($request);
    } catch (ValidationException $e) {
      $response = $this->responseFactor->createResponse();
      $referer = $this->requestService->getReferer($request);
      $oldData = $request->getParsedBody();

      $sensitiveFields = ['password', 'confirmPassword'];

      $this->session->flash('errors', $e->errors);
      $this->session->flash('old', array_diff_key($oldData, array_flip($sensitiveFields)));

      return $response->withHeader('Location', $referer)->withStatus(302);
    }
  }
}
