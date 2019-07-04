<?php

require_once(__DIR__ . '/configuration.php');

// Super vlastnost PHP-DI - umoznuje injektovat i do funkci. Obvykle to slouci pro mikroframeworky typu Flight. Ja tu vlastne taky mam velmi mikroframework (nula-framework :-).
$container->call(function(League\Plates\Engine $platesEngine, UserDetailProvider $userDetailProvider, Chat\Authenticator $authenticator) {
    if (!empty($_REQUEST['user'])) { // predstirame prihlaseni s pomoci databaze
        $userId = $_REQUEST['user'];
        $userName = $userDetailProvider->getNameForId($userId);
        $authenticator->setAuthenticationToken($userId, $userName); // nastaveni autentikacniho tokenu by se normalne provedlo na strance chat.php pri vstupu do chatu podle session (a smazani pri vystupu z chatu)
        header('Location: /chat.php');
    }

    echo $platesEngine->render('Chat/index', [
        'users' => UserDetailProvider::$users,
    ]);
});
