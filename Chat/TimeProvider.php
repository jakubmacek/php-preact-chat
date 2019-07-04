<?php

namespace Chat;

/**
 * V ramci testovatelnosti se vzdycky hodi nemit v kodu primo ziskavani aktualniho casu (at uz pomoci time() nebo new DateTime()).
 * Da se tim pak ladit nejaka zakerna chyba, ktera nastava treba jen kdyz cas presahne pulnoc nebo tak.
 * @package Chat
 */
class TimeProvider
{
    public function getCurrentTimestamp(): int
    {
        return time();
    }
}
