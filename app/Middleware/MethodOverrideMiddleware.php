<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Contracts\SessionInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class GuestMiddleware implements MiddlewareInterface
{
  public function __construct()
  {
  }

  public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
  {
    $methodHeader = $request->getHeaderLine('X-Http-Method-Override');

    if ($methodHeader) {
      $request = $request->withMethod($methodHeader);
    } else if (strtoupper($request->getMethod()) === 'POST') {
      $body = $request->getParsedBody();

      if (is_array($body) && !empty($body['_METHOD'])) {
        $request = $request->withMethod($body['_METHOD']);
      }

      if ($request->getBody()->eof()) {
        $request->getBody()->rewind();
      }
    }

    return $handler->handle($request);
  }
}
