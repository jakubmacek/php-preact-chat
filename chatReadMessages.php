<?php

require_once(__DIR__ . '/configuration.php');

$container->call(function (\UserDetailProvider $userDetailProvider, \Chat\Users $users, \Chat\Messages $messages, \Chat\AuthenticationToken $authenticationToken) {
    $users->updateUserActivityLastReadMessage($authenticationToken->getId());

    // Nacte aktivni uzivatele.
    $activeUsers = $users->getActiveUsers();
    $activeUsers = array_values(array_map(function(\Chat\User $user) {
        return [ // V .NETu bezne strcim primo objekty do vystupu a framework se mi postara o jejich korektni serializaci do JSON. Tady tomu musim explicitne pomoct.
            'id' => $user->getId(),
            'name' => $user->getName(),
            'sex' => $user->getSex(),
        ];
    }, $activeUsers));

    // Nacte aktivni zpravy.
    $activeMessages = $messages->readMessages();
    $activeMessages = array_map(function(\Chat\Message $message) {
        return [
            'timestamp' => $message->getTimestamp(),
            'from' => $message->getFrom(),
            'fromName' => $message->getFromName(),
            'to' => $message->getTo(),
            'private' => $message->isPrivate(),
            'text' => $message->getText(),
        ];
    }, $activeMessages);

    header('Content-Type: text/json; charset=UTF-8');
    $result = [
        'status' => 'ok',
        'messages' => $activeMessages,
        'users' => $activeUsers,
    ];
    echo json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
});
