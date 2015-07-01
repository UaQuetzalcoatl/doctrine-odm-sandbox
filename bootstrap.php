<?php

// bootstrap.php

$vendorDir = __DIR__.'/vendor';

$file = $vendorDir.'/autoload.php';
if (file_exists($file)) {
    $autoload = require_once $file;
} else {
    throw new RuntimeException('Install dependencies with composer.');
}

$params = array(
    'driver'    => 'pdo_mysql',
    'host'      => 'localhost',
    'user'      => 'root',
    'password'  => 'mysql',
    'dbname'    => 'phpcr_odm',
);

$workspace = 'default';
$user = 'admin';
$pass = 'admin';

/* --- transport implementation specific code begin --- */
// for more options, see https://github.com/jackalope/jackalope-doctrine-dbal#bootstrapping
$dbConn = \Doctrine\DBAL\DriverManager::getConnection($params);
$parameters = array('jackalope.doctrine_dbal_connection' => $dbConn);
$repository = \Jackalope\RepositoryFactoryDoctrineDBAL::getRepository($parameters);
$credentials = new \PHPCR\SimpleCredentials(null, null);
/* --- transport implementation specific code  ends --- */

$session = $repository->login($credentials, $workspace);

/* prepare the doctrine configuration */
use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\ODM\PHPCR\Mapping\Driver\AnnotationDriver;
use Doctrine\ODM\PHPCR\Configuration;
use Doctrine\ODM\PHPCR\DocumentManager;
use Doctrine\ORM\Proxy\Autoloader as ProxyAutoloader;

AnnotationRegistry::registerLoader(array($autoload, 'loadClass'));

$reader = new AnnotationReader();
$driver = new AnnotationDriver($reader, array(
    // this is a list of all folders containing document classes
    'vendor/doctrine/phpcr-odm/lib/Doctrine/ODM/PHPCR/Document',
    'src/Demo',
));

$proxyDir = './data/Proxies';
$proxyNamespace = 'Proxy';
$config = new Configuration();
$config->setMetadataDriverImpl($driver);
$config->setProxyDir($proxyDir);
$config->setProxyNamespace($proxyNamespace);
$config->setAutoGenerateProxyClasses(true);

ProxyAutoloader::register($proxyDir, $proxyNamespace);
$documentManager = DocumentManager::create($session, $config);

/*
 * Init cli
 */

if (isset($argv[1])
    && $argv[1] != 'jackalope:init:dbal'
    && $argv[1] != 'list'
    && $argv[1] != 'help'
) {
    $helperSet = new \Symfony\Component\Console\Helper\HelperSet(
        array(
            'dialog' => new \Symfony\Component\Console\Helper\DialogHelper(),
            'phpcr' => new \PHPCR\Util\Console\Helper\PhpcrHelper($session),
            'phpcr_console_dumper' => new \PHPCR\Util\Console\Helper\PhpcrConsoleDumperHelper(),
        )
    );
} elseif (isset($argv[1]) && $argv[1] == 'jackalope:init:dbal') {
    // special case: the init command needs the db connection, but a session is impossible if the db is not yet initialized
    $helperSet = new \Symfony\Component\Console\Helper\HelperSet(
        array(
            'connection' => new \Jackalope\Tools\Console\Helper\DoctrineDbalHelper($dbConn),
        )
    );
}

return $autoload;
