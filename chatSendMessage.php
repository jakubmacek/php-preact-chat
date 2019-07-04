<?php

require_once(__DIR__ . '/configuration.php');

$container->call(function (\Chat\Messages $messages) {
    /**
     * Pomocna trida pro deserializaci pozadavku poslaneho pomoci javascriptoveho fetch(). Posilat parametry v URL je u fetch() dost komplikovane.
     * @property bool private
     * @property int to
     * @property string text
     */
    interface SendMessageRequest
    {
    }

    /** @var SendMessageRequest $request */
    $request = json_decode(file_get_contents('php://input'));
    $status = 'error';
    if ($request && !empty($request->text)) {
        $messages->sendMessage($request->text, $request->to, $request->private);
        $status = 'ok';
    }

    header('Content-Type: text/json; charset=UTF-8');
    $result = [
        'status' => $status
    ];
    echo json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
});
