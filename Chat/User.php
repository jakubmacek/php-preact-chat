<?php

namespace Chat;

/**
 * Trida musi kompletne obsahovat vsechny udaje o jednom uzivateli v seznamu chatu a zaroven zadne dalsi navic (zejmena ne nic citliveho).
 * Instance budou serializovany do JSON, takze zaroven neni zadouci mit jine udaje nez skalary (zejmena ne treba kruhove vazby objektu).
 * Serializace se momentalne provadi rucne, takze to jde trochu ridit. V tomto pripade lastReadMessagesTimestamp neni serializovan, viz chatReadMessages.
 * @package Chat
 */
class User
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $sex;

    /**
     * @var int
     */
    private $lastReadMessagesTimestamp;

    public function __construct(int $id, string $name, string $sex)
    {
        $this->id = $id;
        $this->name = $name;
        $this->sex = $sex;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getSex(): string
    {
        return $this->sex;
    }

    /**
     * @return int
     */
    public function getLastReadMessagesTimestamp(): int
    {
        return $this->lastReadMessagesTimestamp;
    }

    /**
     * @param int $lastReadMessagesTimestamp
     */
    public function setLastReadMessagesTimestamp(int $lastReadMessagesTimestamp): void
    {
        $this->lastReadMessagesTimestamp = $lastReadMessagesTimestamp;
    }
}