<?php

define('DOCUMENT_ROOT', dirname(dirname(__FILE__)));

$regexEmail       = '/^[\w.-]+@[\w.-]+\.[A-Za-z]{2,6}$/';
$regexKey         = '/^[a-z0-9]{32}$/';
$regexOnlyLetters = '/^[a-zA-ZáéíóúÁÉÍÓÚÑñÜü ]+$/';
$regexDni         = '/^[\d]{8}$/';
$regexUser        = '/^[a-z0-9-_]+$/i';

$minPasswordLength = 8;
$maxPasswordLength = 64;

$minUsernameLength = 3;
$maxUsernameLength = 20;
