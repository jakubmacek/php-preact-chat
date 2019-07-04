<?php

namespace Chat;

/**
 * Slouzi k ulozeni autentizacniho token do prohlizece a naopak jeho nacteni a zpracovani pro kazdy pozadavek.
 * Pokud nektera trida pozada o autentizacni token a uzivatel neni prihlasen, tak bude vyvolana vyjimka NotAuthenticatedException. Tu je zadouci odchytit na urovni controlleru nebo jeste lepe frameworku.
 * @package Chat
 */
class Authenticator
{
    /**
     * @var TimeProvider
     */
    private $timeProvider;

    /**
     * @var string
     */
    private $cookieName;

    /**
     * @var string
     */
    private $secret;

    /**
     * @var int
     */
    private $cookieExpiration;

    public function __construct(Configuration $configuration, TimeProvider $timeProvider)
    {
        $this->secret = $configuration->getAuthenticationSecret();
        $this->cookieName = $configuration->getChatTokenCookieName();
        $this->cookieExpiration = $configuration->getAuthenticationTokenExpiration();
        $this->timeProvider = $timeProvider;
    }

    public function setAuthenticationToken(string $userId, string $userName)
    {
        /** @var DeserializedAuthenticationToken $authenticationToken */
        $authenticationToken = new \stdClass();
        $authenticationToken->exp = $this->timeProvider->getCurrentTimestamp() + $this->cookieExpiration;
        $authenticationToken->sub = $userId;
        $authenticationToken->name = $userName;

        // JWT je standard pro data, ktera jsou serializovana do JSON a zasifrovana+podepsana, tzn. i kdyz je preneseme do prohlizece, tak se muzeme spolehnout na jejich obsah
        // v tomto pripade pouzivam symetricke sifrovani (k dispozici je i asymetricke, RSA tusim), protoze nepotrebuji, aby mi prohlizec nejaka data predaval - jen se tam ukladam sva data
        // dulezite na cele te veci je, ze tu vubec nikde nemam $_SESSION ani session_start, takze zadne zdrzovani navic
        $token = \Firebase\JWT\JWT::encode($authenticationToken, $this->secret, 'HS256');

        // nastavovani a spolehani na cookie neni z pohledu architektury idealni, ale v pripade PHP se lze vcelku spolehnout na to, ze nic jineho nez webova aplikace z toho nebude; takze komplikace je jenom pri pripadnem automatickem testovani
        setcookie($this->cookieName, $token, $authenticationToken->exp); // toto by idealne bylo jeste abstrahovane do jine tridy, at se da v pripade potreby pouzit neco jineho nez cookie
    }

    public function unsetAuthenticationToken()
    {
        setcookie($this->cookieName, '', $this->timeProvider->getCurrentTimestamp() - 86400); // smaze cookie
    }

    public function getAuthenticationToken(): AuthenticationToken
    {
        if (empty($_COOKIE[$this->cookieName])) {
            throw new NotAuthenticatedException("Missing chat token cookie.");
        }

        $token = $_COOKIE[$this->cookieName];

        try {
            \Firebase\JWT\JWT::$leeway = 60;
            /** @var DeserializedAuthenticationToken $decodedToken */
            $decodedToken = \Firebase\JWT\JWT::decode($token, $this->secret, ['HS256']);
        } catch (\Exception $ex) {
            throw new NotAuthenticatedException("Authentication token invalid.", 0, $ex);
        }

        $authenticationToken = new AuthenticationToken((int)$decodedToken->sub, $decodedToken->name);
        return $authenticationToken;
    }
}