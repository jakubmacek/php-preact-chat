<?php

namespace Chat;

/**
 * Pracuje se seznamem aktivnich uzivatelu na chatu.
 * V zasade jen aktualizuje kdyz kdo naposled promluvil a pak pri pozadani o seznam vyfiltruje ty, kteri uz dlouho nepromluvili. Komplikovanejsi bylo vymyslet, jak v takovy okamzik pridat zpravu aniz by byla kruhova zavislost mezi tridami Messages a Users.
 * @package Chat
 */
class Users
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
     * @var string
     */
    private $storageKey;

    /**
     * @var int
     */
    private $timeNotActiveTolerance;

    /**
     * @var \UserDetailProvider
     */
    private $userDetailProvider;

    /**
     * @var Messages
     */
    private $messages;

    public function __construct(
        Configuration $configuration,
        AuthenticationToken $authenticationToken,
        TimeProvider $timeProvider,
        \Doctrine\Common\Cache\Cache $storage,
        \UserDetailProvider $userDetailProvider,
        Messages $messages
    ) {
        $this->storageKey = $configuration->getUsersStorageKey();
        $this->timeNotActiveTolerance = $configuration->getUserTimeNotActiveTolerance();
        $this->authenticationToken = $authenticationToken;
        $this->timeProvider = $timeProvider;
        $this->storage = $storage;
        $this->userDetailProvider = $userDetailProvider;
        $this->messages = $messages;
    }

    /**
     * @return User[]
     */
    public function getActiveUsers()
    {
        $serializedUsers = $this->storage->fetch($this->storageKey);
        if (!$serializedUsers) {
            $users = [];
        } else {
            /** @var User[] $users */
            $users = unserialize($serializedUsers);
            if (!$users) {
                $users = [];
            }
        }

        $now = $this->timeProvider->getCurrentTimestamp();
        $cutoffTimestamp = $now - $this->timeNotActiveTolerance;

        $storeNewUsers = false;
        foreach (array_keys($users) as $id) {
            $user = $users[$id];
            if ($user->getLastReadMessagesTimestamp() < $cutoffTimestamp) { // uz neaktivni
                $this->messages->addSystemMessage('Uživatel ' . $user->getName() . ' 30 minut nepromluvil a byl vyhozen.');
                unset($users[$id]);
                $storeNewUsers = true;
            }
        }

        // seradime podle prezdivky
        uasort($users, function(\Chat\User $a, \Chat\User $b) {
            return strcmp($a->getName(), $b->getName()); // naivni implementace, neumi cestinu, ale je rychla
        });

        if ($storeNewUsers) {
            $this->storeUsers($users);
        }

        return $users;
    }

    private function storeUsers($users)
    {
        $serializedUsers = serialize($users);
        $this->storage->save($this->storageKey, $serializedUsers, 0);
    }

    public function updateUserActivityLastReadMessage(int $id)
    {
        $users = $this->getActiveUsers();

        if (!isset($users[$id])) {
            $name = $this->userDetailProvider->getNameForId($id);
            $sex = $this->userDetailProvider->getSexForId($id);
            $users[$id] = new User($id, $name, $sex);
        }

        $now = $this->timeProvider->getCurrentTimestamp();
        $users[$id]->setLastReadMessagesTimestamp($now);

        $this->storeUsers($users);
    }
}