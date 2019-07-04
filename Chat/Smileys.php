<?php

namespace Chat;

/**
 * Tato trida prevadi znacky na smajliky. Osobne si myslim, ze by bylo idealni zpristupnit vsechny smajliky vsem a jen si uzivatel bude moct navybirat, jake chce mit v nabidce na kliknuti.
 * Pokud by to prevadeni ale melo byt zavisle na prihlasenem uzivateli, tak tu jde klidne do konstruktoru dat zavislost na AuthenticationToken a UserDetailProvider, kde budou ti smajlici nacitani z databaze.
 * @package Chat
 */
class Smileys
{
    private $map = [
        '**1' => 'https://s2.bdsmlife.cz/img/chat/chat1.png',
        '**2' => 'https://s2.bdsmlife.cz/img/chat/chat2.png',
        '**3' => 'https://s2.bdsmlife.cz/img/chat/chat3.png',
        '**4' => 'https://s2.bdsmlife.cz/img/chat/chat4.png',
        '**5' => 'https://s2.bdsmlife.cz/img/chat/chat5.png',
        '**6' => 'https://s2.bdsmlife.cz/img/chat/chat6.png',
        '**7' => 'https://s2.bdsmlife.cz/img/chat/chat7.png',
        '**8' => 'https://s2.bdsmlife.cz/img/chat/chat8.png',
        '**9' => 'https://s2.bdsmlife.cz/img/chat/chat9.png',
        '**10' => 'https://s2.bdsmlife.cz/img/chat/chat10.png',
        '***1' => 'https://s2.bdsmlife.cz/img/smileys/450.gif',
        '***2' => 'https://s2.bdsmlife.cz/img/smileys/266.gif',
        '***3' => 'https://s2.bdsmlife.cz/img/smileys/441.gif',
        '***4' => 'https://s2.bdsmlife.cz/img/smileys/73.gif',
        '***5' => 'https://s2.bdsmlife.cz/img/smileys/294.gif',
        '***6' => 'https://s2.bdsmlife.cz/img/smileys/438.gif',
        '***7' => 'https://s2.bdsmlife.cz/img/smileys/74.gif',
        '***8' => 'https://s2.bdsmlife.cz/img/smileys/332.gif',
        '***9' => 'https://s2.bdsmlife.cz/img/smileys/77.gif',
    ];

    public function __construct()
    {
    }

    public function convertSymbolsToHTML(string $text): string
    {
        return preg_replace_callback('~(\\*\\*\\*?\\d+)~', function ($match) {
            if (isset($this->map[$match[1]])) {
                return ' <img src="' . $this->map[$match[1]] . '" /> ';
            }
            return '';
        }, $text);
    }

    public function getMap()
    {
        return $this->map;
    }
}