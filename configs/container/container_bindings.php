<?php

declare(strict_types=1);

use App\Auth;
use App\Csrf;
use Slim\App;
use App\Config;
use App\Session;
use Slim\Csrf\Guard;
use Slim\Views\Twig;
use App\Enum\SameSite;
use function DI\create;
use Clockwork\Clockwork;
use Doctrine\ORM\ORMSetup;
use App\Enum\StorageDriver;
use App\Enum\AppEnvironment;
use Slim\Factory\AppFactory;
use Doctrine\ORM\EntityManager;
use App\Contracts\AuthInterface;
use Doctrine\DBAL\DriverManager;
use League\Flysystem\Filesystem;
use App\DataObjects\SessionConfig;
use Clockwork\Storage\FileStorage;
use Twig\Extra\Intl\IntlExtension;
use App\Contracts\SessionInterface;
use Symfony\Component\Asset\Package;
use App\services\UserProviderService;
use Psr\Container\ContainerInterface;
use Symfony\Component\Asset\Packages;
use App\services\EntityManagerService;
use Doctrine\ORM\EntityManagerInterface;
use Clockwork\DataSource\DoctrineDataSource;
use Psr\Http\Message\ResponseFactoryInterface;
use App\Contracts\UserProviderServiceInterface;
use App\Contracts\EntityManagerServiceInterface;
use App\RequestValidator\RequestValidatorFactory;
use Symfony\Bridge\Twig\Extension\AssetExtension;
use League\Flysystem\Local\LocalFilesystemAdapter;
use Symfony\WebpackEncoreBundle\Asset\TagRenderer;
use App\Contracts\RequestValidatorFactoryInterface;
use Symfony\WebpackEncoreBundle\Asset\EntrypointLookup;
use Symfony\WebpackEncoreBundle\Twig\EntryFilesTwigExtension;
use Symfony\Component\Asset\VersionStrategy\JsonManifestVersionStrategy;

return [
  App::class =>
  function (ContainerInterface $container, Config $config) {
    AppFactory::setContainer($container);

    $addMiddlewares = require CONFIG_PATH . '/middleware.php';
    $router = require CONFIG_PATH . '/routes/web.php';

    $app = AppFactory::create();

    $router($app);
    $addMiddlewares($app);

    date_default_timezone_set($config->get('timezone'));

    return $app;
  },

  Config::class =>
  create(Config::class)->constructor(require CONFIG_PATH . '/app.php'),

  // EntityManager::class =>
  // fn (Config $config) => EntityManager::create(
  //   $config->get('doctrine.connection'),
  //   ORMSetup::createAttributeMetadataConfiguration(
  //     $config->get('doctrine.entity_dir'),
  //     $config->get('doctrine.dev_mode')
  //   )
  // ),

  EntityManagerInterface::class =>
  function (Config $config) {
    $ormConfig = ORMSetup::createAttributeMetadataConfiguration(
      $config->get('doctrine.entity_dir'),
      $config->get('doctrine.dev_mode')
    );

    return new EntityManager(
      DriverManager::getConnection($config->get('doctrine.connection'), $ormConfig),
      $ormConfig
    );
  },

  Twig::class =>
  function (Config $config, ContainerInterface $container) {
    $twig = Twig::create(VIEW_PATH, [
      'cache'       => STORAGE_PATH . '/cache/templates',
      'auto_reload' => AppEnvironment::isDevelopment($config->get('app_environment')),
    ]);

    $twig->addExtension(new IntlExtension());
    $twig->addExtension(new EntryFilesTwigExtension($container));
    $twig->addExtension(new AssetExtension($container->get('webpack_encore.packages')));

    return $twig;
  },

  ResponseFactoryInterface::class =>
  fn (App $app) => $app->getResponseFactory(),

  AuthInterface::class =>
  fn (ContainerInterface $container) => $container->get(Auth::class),

  UserProviderServiceInterface::class =>
  fn (ContainerInterface $container) => $container->get(UserProviderService::class),

  SessionInterface::class =>
  fn (Config $config) => new Session(new SessionConfig(
    $config->get('session.name', ''),
    $config->get('session.flash_name', 'flash'),
    $config->get('session.secure', true),
    $config->get('session.httponly', true),
    SameSite::from($config->get('session.samesite', 'lax'))
  )),

  RequestValidatorFactoryInterface::class =>
  fn (ContainerInterface $container) => $container->get(RequestValidatorFactory::class),

  /**
   * The following two bindings are needed for EntryFilesTwigExtension & AssetExtension to work for Twig
   */
  'webpack_encore.packages' =>
  fn () => new Packages(
    new Package(new JsonManifestVersionStrategy(BUILD_PATH . '/manifest.json'))
  ),

  'webpack_encore.tag_renderer' =>
  fn (ContainerInterface $container) => new TagRenderer(
    new EntrypointLookup(BUILD_PATH . '/entrypoints.json'),
    $container->get('webpack_encore.packages')
  ),

  'csrf' =>
  fn (ResponseFactoryInterface $responseFactory, Csrf $csrf) => new Guard(
    $responseFactory,
    persistentTokenMode: true,
    failureHandler: $csrf->failureHandler()
  ),

  Filesystem::class => function (Config $config) {
    $adapt = match ($config->get('storage.driver')) {
      StorageDriver::Local => new LocalFilesystemAdapter(STORAGE_PATH)
    };

    return new Filesystem($adapt);
  },

  Clockwork::class =>
  function (EntityManagerInterface $entityManager) {
    $clockwork = new Clockwork();

    $clockwork->storage(new FileStorage(STORAGE_PATH . '/clockwork'));
    $clockwork->addDataSource(new DoctrineDataSource($entityManager));

    return $clockwork;
  },

  EntityManagerServiceInterface::class =>
  fn (EntityManager $entityManager) => new EntityManagerService($entityManager)
];
