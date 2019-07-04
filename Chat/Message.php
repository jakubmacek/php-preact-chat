<?php

namespace Chat;

/**
 * Trida musi kompletne obsahovat vsechny udaje o jedne zprave v chatu a zaroven zadne dalsi navic (zejmena ne nic citliveho).
 * Instance budou serializovany do JSON, takze zaroven neni zadouci mit jine udaje nez skalary (zejmena ne treba kruhove vazby objektu).
 * @package Chat
 */
class Message
{
    /**
     * @var int
     */
    private $timestamp;

    /**
     * @var int
     */
    private $from;

    /**
     * @var string
     */
    private $fromName;

    /**
     * @var int
     */
    private $to;

    /**
     * @var bool
     */
    private $private;

    /**
     * @var string
     */
    private $text;

    /**
     * @param int $timestamp
     * @param int $from
     * @param string $fromName
     * @param int $to
     * @param bool $private
     * @param string $text
     */
    public function __construct(int $timestamp, int $from, string $fromName, int $to, bool $private, string $text)
    {
        $this->timestamp = $timestamp;
        $this->from = $from;
        $this->fromName = $fromName;
        $this->to = $to;
        $this->private = $private;
        $this->text = $text;
    }

    /**
     * @return int
     */
    public function getTimestamp(): int
    {
        return $this->timestamp;
    }

    /**
     * @return int
     */
    public function getFrom(): int
    {
        return $this->from;
    }

    /**
     * @return string
     */
    public function getFromName(): string
    {
        return $this->fromName;
    }

    /**
     * @return int
     */
    public function getTo(): int
    {
        return $this->to;
    }

    /**
     * @return bool
     */
    public function isPrivate(): bool
    {
        return $this->private;
    }

    /**
     * @return string
     */
    public function getText(): string
    {
        return $this->text;
    }
}
