<?php

require_once(__DIR__ . '/configuration.php');

$container->call(function(League\Plates\Engine $platesEngine, \Chat\AuthenticationToken $authenticationToken, \Chat\Smileys $smileys) {
    echo $platesEngine->render('Chat/chat', [
        'authenticationToken' => $authenticationToken,
        'smileys' => $smileys->getMap()
    ]);
});
