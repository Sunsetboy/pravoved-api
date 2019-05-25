<?php

namespace PravovedApi;

/**
 * Формирование и отрпава запросов к API
 * Class Request
 * @package PravovedApi
 */
class Request
{
    protected $curlLink;

    /**
     * Request constructor.
     * @param PravovedApiClient $apiClient
     * @param string $method
     * @param string $route
     * @param array $headers
     * @param null $data
     */
    public function __construct(PravovedApiClient $apiClient, $method, $route, $headers = [], $data = null)
    {
        $url = $apiClient->getApiUrl() . $route;

        $this->curlLink = curl_init();
        curl_setopt($this->curlLink, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->curlLink, CURLOPT_HEADER, false);
        curl_setopt($this->curlLink, CURLOPT_CONNECTTIMEOUT, 2);
        $requestHeaders = array_merge($apiClient->getAuthHeader(), $headers);

        curl_setopt($this->curlLink, CURLOPT_HTTPHEADER, $requestHeaders);
        curl_setopt($this->curlLink, CURLOPT_URL, $url);

        if ($method === 'POST') {
            curl_setopt($this->curlLink, CURLOPT_POST, 1);
            curl_setopt($this->curlLink, CURLOPT_POSTFIELDS, $data);
        }
    }

    /**
     * Отправка запроса
     * @return array
     */
    public function send(): array
    {
        $jsonResponse = curl_exec($this->curlLink);
        $curlInfo = curl_getinfo($this->curlLink);

        return ['curlInfo' => $curlInfo, 'response' => $jsonResponse];
    }

    private function __destruct()
    {
        curl_close($this->curlLink);
    }
}