<?php

namespace PravovedApi\exceptions;

/**
 * Исключение при попытке обратиться без токена к методам API, требующим авторизацию
 * Class AuthorizationRequiredException
 * @package PravovedApi\exceptions
 */
class AuthorizationRequiredException extends \Exception
{

}