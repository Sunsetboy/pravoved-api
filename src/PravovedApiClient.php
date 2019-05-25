<?php

namespace PravovedApi;

use PravovedApi\exceptions\NotAuthorizedException;

/**
 * Клиент для работы с API Правоведа
 * Class PravovedApiClient
 * @package PravovedApi
 */
class PravovedApiClient
{
    /** @var string */
    private $apiUrl;
    /** @var string */
    private $token;
    /** @var string */
    private $email;
    /** @var string */
    private $password;

    /** @var array */
    private $authHeader = [];

    /**
     * PravovedApiClient constructor.
     * @param string $apiUrl
     */
    public function __construct($apiUrl = 'https://pravoved.ru/restv2')
    {
        $this->apiUrl = $apiUrl;
    }

    /**
     * @param string $token
     * @return PravovedApiClient
     */
    public function setToken($token)
    {
        $this->token = $token;
        return $this;
    }

    /**
     * @param string $email
     * @return PravovedApiClient
     */
    public function setEmail($email)
    {
        $this->email = $email;
        return $this;
    }

    /**
     * @param string $password
     * @return PravovedApiClient
     */
    public function setPassword($password)
    {
        $this->password = $password;
        return $this;
    }

    /**
     * @return string
     */
    public function getApiUrl(): string
    {
        return $this->apiUrl;
    }

    /**
     * @return array
     */
    public function getAuthHeader(): array
    {
        return $this->authHeader;
    }

    /**
     * Создает заголовок авторизации из токена
     * @param string $token
     * @return PravovedApiClient
     */
    public function setAuthHeader($token)
    {
        $this->authHeader = ['Authorization' => 'Bearer ' . $token];
        return $this;
    }

    /**
     * Получение авторизационного токена
     * @return string
     * @throws NotAuthorizedException
     */
    public function getAuthToken()
    {
        $userCredentials = [
            'email' => $this->email,
            'password' => $this->password,
        ];

        $request = new Request($this, 'POST', '/auth/', [], $userCredentials);
        $responseArray = $request->send();
        $response = json_decode($responseArray['response'], true);

        if (isset($response['data']) && isset($response['data']['access_token'])) {
            return $response['data']['access_token'];
        } else {
            throw new NotAuthorizedException('Не удалось авторизоваться в API Правовед');
        }
    }

}
