<?php

namespace App\Controllers;

use App\Contracts\AuthInterface;
use App\Entity\User;
use Slim\Views\Twig;
use Valitron\Validator;
use Doctrine\ORM\EntityManager;
use App\Exceptions\ValidationException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class AuthController
{
  public function __construct(
    private readonly Twig $twig,
    private readonly EntityManager $entityManager,
    private readonly AuthInterface $auth
  ) {
  }

  public function loginView(Request $request, Response $response): Response
  {
    return $this->twig->render($response, 'auth/login.twig');
  }

  public function registerView(Request $request, Response $response): Response
  {
    return $this->twig->render($response, 'auth/register.twig');
  }

  public function register(Request $request, Response $response): Response
  {
    $data = $request->getParsedBody();

    $v = new Validator($data);

    $v->rule('required', ['name', 'email', 'password', 'confirmPassword'])
      ->rule('email', 'email')
      ->rule('equals', 'confirmPassword', 'password')->label('Confirm Password')
      ->rule(
        fn ($field, $value) => !$this->entityManager->getRepository(User::class)->count(['email' => $value]),
        'email'
      )->message('User with the given email is already exists');

    if ($v->validate()) {
      echo "Yay! We're all good!";
    } else {
      throw new ValidationException($v->errors());
    }

    $user = new User();

    $user
      ->setName($data['name'])
      ->setEmail($data['email'])
      ->setPassword(password_hash($data['password'], PASSWORD_BCRYPT, ['cost' => 12]));

    $this->entityManager->persist($user);
    $this->entityManager->flush();

    return $response;
  }

  public function login(Request $request, Response $response): Response
  {
    $data = $request->getParsedBody();

    $v = new Validator($data);

    $v->rule('required', ['email', 'password'])
      ->rule('email', 'email');

    if (!$this->auth->attemptLogin($data)) {
      throw new ValidationException(['password' => ['You have entered an invalid username or password']]);
    }

    return $response->withHeader('Location', '/')->withStatus(302);
  }

  public function logout(Request $request, Response $response): Response
  {
    $this->auth->logout();

    return $response->withHeader('Location', '/')->withStatus(302);
  }
}
