<?php

namespace Chat;

/**
 * Spravuje viditelne zpravy na chatu.
 * Resi mimo jine to, aby se stare zpravy odmazavaly, a aby uzivatele videli jen zpravy pro ne urcene (tj. septani).
 * @package Chat
 */
class Messages
{
    /**
     * @var AuthenticationToken
     */
    private $authenticationToken;

    /**
     * @var TimeProvider
     */
    private $timeProvider;

    /**
     * @var \Doctrine\Common\Cache\Cache
     */
    private $storage;

    /**
     * @var \UserDetailProvider
     */
    private $userDetailProvider;

    /**
     * @var string
     */
    private $storageKey;

    /**
     * @var int
     */
    private $maxNumberOfStoredMessages;

    /**
     * @var int
     */
    private $maxAgeOfStoredMessages;

    /**
     * @var Smileys
     */
    private $smileys;

    public function __construct(
        Configuration $configuration,
        AuthenticationToken $authenticationToken,
        TimeProvider $timeProvider,
        \Doctrine\Common\Cache\Cache $storage,
        \UserDetailProvider $userDetailProvider,
        Smileys $smileys
    ) {
        $this->storageKey = $configuration->getMessagesStorageKey();
        $this->maxNumberOfStoredMessages = $configuration->getMaxNumberOfStoredMessages();
        $this->maxAgeOfStoredMessages = $configuration->getMaxAgeOfStoredMessages();
        $this->authenticationToken = $authenticationToken;
        $this->timeProvider = $timeProvider;
        $this->storage = $storage;
        $this->userDetailProvider = $userDetailProvider;
        $this->smileys = $smileys;
    }

    /**
     * @return Message[]
     */
    public function readMessages()
    {
        $cutoffTimestamp = $this->getMessageCutoffTimestamp();

        $allMessages = $this->readAllMessages();

        $messages = [];
        foreach ($allMessages as $message) {
            // Zpravy, ktere maji byt odmazany, se neprenesou do prohlizece. I nadale se budou odmazavat jen pri poslani zpravy (aby tato funkce vzdy jen cetla), ale takto bude odmazavani plynulejsi, kdyz nikdo nic nepise.
            if ($message->getTimestamp() < $cutoffTimestamp) {
                continue;
            }

            if ($message->isPrivate()) { // soukroma zprava
                if (($message->getFrom() == $this->authenticationToken->getId()) || ($message->getTo() == $this->authenticationToken->getId())) {
                    $messages[] = $message;
                }
            } else { // neni soukroma
                $messages[] = $message;
            }
        }

        return $messages;
    }

    public function addSystemMessage(string $text)
    {
        $now = $this->timeProvider->getCurrentTimestamp();
        $newMessage = new Message(
            $now,
            0,
            '',
            0,
            false,
            $text
        );

        $messages = $this->readAllMessages();
        $messages[] = $newMessage;
        $this->storeMessages($messages);
    }

    private function storeMessages($messages)
    {
        $serializedNewMessages = serialize($messages);
        $this->storage->save($this->storageKey, $serializedNewMessages, 0);
    }

    /**
     * @return Message[]
     */
    private function readAllMessages()
    {
        $serializedMessages = $this->storage->fetch($this->storageKey);

        if (!$serializedMessages) {
            return [];
        }

        $messages = unserialize($serializedMessages);
        if (!$messages) {
            return [];
        }
        return $messages;
    }

    private function getMessageCutoffTimestamp()
    {
        $now = $this->timeProvider->getCurrentTimestamp();
        $timestamp = $now - $this->maxAgeOfStoredMessages;
        return $timestamp;
    }

    public function sendMessage(string $text, int $to, bool $private)
    {
        if (!$text) {
            return;
        }

        $now = $this->timeProvider->getCurrentTimestamp();
        $cutoffTimestamp = $this->getMessageCutoffTimestamp();
        $fromId = (int)$this->authenticationToken->getId();
        $fromName = $this->userDetailProvider->getNameForId($fromId);
        $text = htmlspecialchars(strip_tags($text), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        if ($to) {
            $toName = $this->userDetailProvider->getNameForId($to);
            $text = $toName . ': ' . $text;
        }
        $text = $this->smileys->convertSymbolsToHTML($text);
        $newMessage = new Message(
            $now,
            $fromId,
            $fromName,
            $to,
            $private,
            $text
        );

        //TODO Tady je potreba udelat nejaky zamek na apcu kolem nasledujici sekce. V pripade hodne velke aktivity by mohlo dojit k tomu, ze tu vznikne race condition a ulozi se spatna data.

        $allMessages = $this->readAllMessages();
        $newMessages = [];

        foreach ($allMessages as $message) {
            if ($message->getTimestamp() >= $cutoffTimestamp) {
                $newMessages[] = $message;
            }
        }
        $newMessages[] = $newMessage;

        if (count($newMessages) > $this->maxNumberOfStoredMessages) {
            $newMessages = array_slice($newMessages, -$this->maxNumberOfStoredMessages);
        }

        $this->storeMessages($newMessages);
    }
}