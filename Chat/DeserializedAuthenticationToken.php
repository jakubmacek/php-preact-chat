<?php

namespace Chat;

/**
 * Anotace pro stdClass, ktery mi vraci JWT::decode (potazmo uvnitr jeho json_decode).
 * @property int exp Timestamp expirace tokenu.
 * @property string sub ID uzivatele.
 * @property string name Prezdivka uzivatele.
 */
interface DeserializedAuthenticationToken
{
}
