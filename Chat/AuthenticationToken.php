<?php

namespace Chat;

/**
 * Autentizacni token mi dava informace o uzivateli, ktere jsem si nechal zapamatovat v prohlizeci. Jednotlive tridy, ktere s nim pracuji, se nemusi zabyvat tim, kde se ID vzalo, ale muzou se spolehnout na to, ze je spravne.
 * @package Chat
 */
class AuthenticationToken
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
     * @param int $id
     * @param string $name
     */
    public function __construct(int $id, string $name)
    {
        $this->id = $id;
        $this->name = $name;
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
}
