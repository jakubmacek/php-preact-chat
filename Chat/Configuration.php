<?php

namespace Chat;

/**
 * Tridy v modulu Chat potrebuji ke sve praci nastavit tyto parametry.
 * @package Chat
 */
interface Configuration
{
    /**
     * @return string
     */
    function getAuthenticationSecret();

    /**
     * @return int
     */
    function getAuthenticationTokenExpiration();

    /**
     * @return string
     */
    function getChatTokenCookieName();

    /**
     * @return string
     */
    function getMessagesStorageKey();

    /**
     * @return string
     */
    function getUsersStorageKey();

    /**
     * @return int
     */
    function getMaxNumberOfStoredMessages();

    /**
     * @return int
     */
    function getMaxAgeOfStoredMessages();

    /**
     * @return int
     */
    function getUserTimeNotActiveTolerance();
}