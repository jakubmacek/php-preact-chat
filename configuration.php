<?php

// Cely tento soubor by mel byt soucasti globalni konfigurace projektu. Nic z toho by nemelo byt samostatna konfigurace pro chat.

// Trivialni autoload podle zvyklosti, ze namespace = adresar.
spl_autoload_register(function ($className) {
    $filePath = __DIR__ . '/' . str_replace('\\', '/', $className) . '.php';
    if (is_readable($filePath)) {
        require_once($filePath);
    }
});

require_once(__DIR__ . '/vendor/autoload.php');

\Tracy\Debugger::enable(\Tracy\Debugger::DEVELOPMENT);

// Namespace Chat je teoreticky naprosto izolovany modul, ktery lze integrovat do libovolneho webu.
// Nevyresene jsou pouze controllery, ktere se musi napsat dle konkretniho frameworku a nema smysl je v ukazde prilis pripravovat.
// Izolace je mimo jine docilena tim, ze ma vlastni konfiguracni tridu a nemusi tedy znat, kde a jak je ulozena globalni konfigurace. Zbytek zavislosti zajisti PHP-DI kontejner.

class TestChatConfiguration implements Chat\Configuration
{
    public function getAuthenticationSecret()
    {
        return 'do43rVmSqkzpHSvvVGjkiIfiB1F8DEXBRV1zYk6C';
    }

    public function getAuthenticationTokenExpiration()
    {
        return 8 * 60 * 60;
    }

    public function getChatTokenCookieName()
    {
        return 'chattoken';
    }

    public function getMessagesStorageKey()
    {
        return 'chatmessages';
    }

    public function getUsersStorageKey()
    {
        return 'chatusers';
    }

    public function getMaxNumberOfStoredMessages()
    {
        return 100;
    }

    public function getMaxAgeOfStoredMessages()
    {
        return 300;
    }

    public function getUserTimeNotActiveTolerance()
    {
        return 1800;
    }
}

// Toto je muj pomocny ladici kod. Slouzi podobnemu ucelu jako FirePHP - udelat nejaky var_dump v situacich, kdy vysledek pozadavku musi byt validni JSON a tudiz neni ten var_dump kam strcit. Toto konkretne je vyladeno na pouziti s nastrojem Log2console.
function logRemote($logger, $level, $message)
{
    static $nedostupne = false;

    if ($nedostupne) {
        return;
    }

    if (!isset($_SERVER['REMOTE_ADDR'])) {
        return;
    }
    if (!in_array($_SERVER['REMOTE_ADDR'], ['127.0.0.1', '::1'])) // povolene IP adresy pro UDP logovani
    {
        return;
    }

    $socket = fsockopen('tcp://' . $_SERVER['REMOTE_ADDR'], 9991, $errno, $errstr, 3);
    if ($socket === false) {
        $nedostupne = true;
        return;
    }

    $timestamp = (int)(microtime(true) * 1000);
    $logger = htmlspecialchars($logger, ENT_QUOTES, 'UTF-8');
    $level = htmlspecialchars($level, ENT_QUOTES, 'UTF-8');
    fwrite($socket, '<log4j:event logger="' . $logger . '" timestamp="' . $timestamp . '" level="' . $level . '" thread="1"><log4j:message><![CDATA[' . $message . ']]></log4j:message></log4j:event>');

    fclose($socket);
}

/** @var \DI\Container $container */
$container = call_user_func(function () {
    // Vytvoreni kontejneru davam do anonymni funkce, aby nikoho neladalo sahnout do toho ContainerBuilder a neco v nem menit.

    $builder = new DI\ContainerBuilder();
    $builder->writeProxiesToFile(true, __DIR__ . '/temp');
    $builder->addDefinitions([
        // Rychle pracujici uloziste pro uzivatele a zpravy chatu. Sdilena pamet na serveru je idealni - za cenu par desitek kilobajtu pameti neni nutne vubec sahat do databaze.
        Doctrine\Common\Cache\Cache::class => function () {
            return new \Doctrine\Common\Cache\WinCacheCache();
            //return new \Doctrine\Common\Cache\FilesystemCache(__DIR__ . '/temp'); // na testovacim hostingu neni ani APCu, takze jsem si musel pomoct takto
        },

        // Globalni tridy, ktere budou vyuzivany i v jinych castech projektu.
        UserDetailProvider::class => \DI\autowire(UserDetailProvider::class)->lazy(), // tato trida by jinak vyzadovala pristup k databazi, takze je lazy-optimalizovana aby se nepripojovala k databazi, dokud to neni potreba

        // Sablonovovani by resil casto zvoleny framework. Treba Symfony ma Twig, Laravel ma Blade, ... Ja ale potrebuju nejake sablony a Plates jsou sablony v cistem PHP, takze minimalni prekazky.
        League\Plates\Engine::class => function (\Psr\Container\ContainerInterface $container) {
            $engine = new League\Plates\Engine(__DIR__ . '/views');

            $engine->addData([
            ]); //http://platesphp.com/v3/templates/data/

            return $engine;
        },

        // Je treba zaregistrovat vsechny tridy chatu. Jak je videt, tak treba v pripade Configuration neexistuje v tom modulu Chat nejaka implementace a ocekava se, ze bude dodana "zvenci".
        Chat\Configuration::class => \DI\autowire(TestChatConfiguration::class),
        Chat\Authenticator::class => \DI\autowire(Chat\Authenticator::class),
        Chat\TimeProvider::class => \DI\autowire(Chat\TimeProvider::class),
        Chat\Smileys::class => \DI\autowire(Chat\Smileys::class),
        Chat\Messages::class => \DI\autowire(Chat\Messages::class),
        Chat\AuthenticationToken::class => function (\Psr\Container\ContainerInterface $container) {
            // Tady by slo klidne pro testovani vytvaret pevny token pro konkretniho uzivatele. Nyni je implementovano ziskavani pomoci Authenticatoru a tudiz z cookie.
            $authenticator = $container->get(Chat\Authenticator::class);
            return $authenticator->getAuthenticationToken();
        },
    ]);
    return $builder->build();
});
