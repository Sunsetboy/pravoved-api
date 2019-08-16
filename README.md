# Клиент для API Правоведа

[![Maintainability](https://api.codeclimate.com/v1/badges/334c15601db179ab0562/maintainability)](https://codeclimate.com/github/Sunsetboy/pravoved-api/maintainability)

Неофициальная библиотека для работы с API сервиса pravoved.ru. 

Внимание: автор никак не связан с компанией Правовед. Библиотека была создана для интеграции моего проекта с сервисом и поставляется "Как есть".

### Возможности:
* Получение авторизационного токена
* Получение списка предзаказов
* Получение лидов из предзаказа

### Требования:
* PHP 7.2+
* Модули Curl и Json
* Аккаунт в сервисе Правовед

### Установка через Composer
```
composer require yurcrm/pravoved-api
```

## Использование
Перед началом использования убедитесь, что вы зарегистрированы в сервисе Правовед и вам включен доступ к API (в моем случае он был включен автоматически для 2 клиентов из 2)

### Получение токена для работы с API
```php
$pravovedClient = new PravovedApi\PravovedApiClient();
// $email и $password - ваши данные доступа к Правоведу
$pravovedClient->setEmail($email); 
$pravovedClient->setPassword($password);

try {
    $token = $pravovedClient->getAuthToken();
} catch (\Exception $e) {
    // обработка неудачной аутентификации
}
```
Получив токен, вы можете использовать его в следующих запросах

### Получение списка предзаказов
```php
// $token - ваш токен
$pravovedClient = new PravovedApi\PravovedApiClient($token);
try {
    $preorders = $pravovedClient->getPreorders();
} catch (\Exception $e) {
    // обработка ошибки получения предзаказов
}
// задержка между запросами для обхода ограничения на частоту запросов
sleep(60 / PravovedApiClient::MAX_FREQUENCY);

// получение только активных предзаказов
$activePreorders = $pravovedClient->filterActivePreorders($preorders);
```

### Получение списка лидов предзаказа
```php
foreach ($activePreorders as $activePreorder) {

    $preorderId = $activePreorder['id'];

    try {
        sleep(60 / PravovedApiClient::MAX_FREQUENCY);
        // Получим 50 последних лидов предзаказа
        $leadsFromPravoved = $pravovedClient->getPreorderLeads($preorderId, 50);
    } catch (\Exception $e) {
        // обработка ошибки получения лидов
    }
}
```

### Контакты
Отзывы и предложения жду по адресу: misha.sunsetboy@gmail.com
