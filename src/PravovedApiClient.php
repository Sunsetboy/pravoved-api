<?php

namespace PravovedApi;

use PravovedApi\exceptions\NotAuthorizedException;
use PravovedApi\exceptions\AuthorizationRequiredException;
use PravovedApi\exceptions\NoDataException;

/**
 * Клиент для работы с API Правоведа
 * Class PravovedApiClient
 * @package PravovedApi
 */
class PravovedApiClient
{
    // Максимальная частота запросов в минуту, разрешенная Правоведом
    const MAX_FREQUENCY = 20;

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
     * @param string $apiUrl URL API Правоведа
     * @param string|null $authToken Токен, если известен
     */
    public function __construct($authToken = null, $apiUrl = 'https://pravoved.ru/restv2')
    {
        $this->apiUrl = $apiUrl;

        if (!is_null($authToken)) {
            $this->setAuthHeader($authToken);
        }
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

    /**
     * Получение списка предзаказов
     * @param int $limit Лимит выдачи, одно из чисел ​ 10, 25, 50
     * @param int $offset Сдвиг, больше либо равен 0
     * @return array Ассоциативный массив предзаказов
     * @throws NoDataException
     * @throws AuthorizationRequiredException
     */
    public function getPreorders($limit = 10, $offset = 0): array
    {
        if (empty($this->getAuthHeader())) {
            throw new AuthorizationRequiredException('Для совершения этого действия требуется авторизация по токену');
        }

        $route = '/preorders/?' . http_build_query([
                'limit' => $limit,
                'offset' => $offset,
            ]);

        $request = new Request($this, 'GET', $route, []);
        $responseArray = $request->send();
        $response = json_decode($responseArray['response'], true);

        if (isset($response['data']) && isset($response['data']['preorders'])) {
            return $response['data']['preorders'];
        } else {
            throw new NoDataException('Не удалось получить список предзаказов');
        }
    }

    /**
     * Получение массива лидов предзаказа
     * @param int $id ID предзаказа
     * @param int $limit
     * @param int $offset
     * @return array
     * @throws NoDataException
     */
    public function getPreorderLeads($id, $limit = 10, $offset = 0): array
    {
        if (empty($this->getAuthHeader())) {
            throw new AuthorizationRequiredException('Для совершения этого действия требуется авторизация по токену');
        }

        $route = '/preorders/' . (int)$id . '/leads/?' . http_build_query([
                'limit' => $limit,
                'offset' => $offset,
            ]);

        $request = new Request($this, 'GET', $route, []);
        $responseArray = $request->send();
        $response = json_decode($responseArray['response'], true);

        if (isset($response['data']) && isset($response['data']['leads'])) {
            return $response['data']['leads'];
        } else {
            throw new NoDataException('Не удалось получить список лидов');
        }
    }

    /**
     * Получение из массива предзаказов только неудаленных
     * @param array $preorders
     * @return array
     */
    public function filterActivePreorders($preorders): array
    {
        $activePreorders = [];

        foreach ($preorders as $preorder) {
            if ($preorder['deleted'] != 1 && $preorder['state_active'] == 1) {
                $activePreorders[] = $preorder;
            }
        }

        return $activePreorders;
    }
}
