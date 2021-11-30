<?php
header('Content-type: text/plain; charset=utf-8');
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
include "../vendor/autoload.php";
use AmoCRM\{AmoAPI, AmoAPIException};
use AmoCRM\TokenStorage\{FileStorage, TokenStorageException};

try {
    // Параметры авторизации по протоколу oAuth 2.0
    $clientId     = '006be00d-8c7c-4e39-85b4-2c2b546c1c3f';
    $clientSecret = 'ZuPhmQ18TpVai8i4kqUgoM9KcJFPqOQgI1M9HzpGXktZ8z5YM1jO6Uze6mdvZG4b';
    $authCode     = 'def502006e3720641e3161b2c776703aedf4c94e960e8e677a027b63601ef47e8c901cd07742cba7ac9343b7d9a5fc8370affdd07b7947df356d8b3fa8f5218c6bebdb634a9f0879a77e4341155e250ace453f59b27b09846f098ccbd4157067f3d7a1f9d71e29ed0571d50af2ec519a686dbfdf12fca00b879d8bdb010dff6bc48442db44d2289cd71e423a52303c4e6f4c962d508c74f63c791166946fbf481b980017c7737fe565814ad81f26e027357bdf2a5e0c14cc9f75e5b4fd458e6cc1b332582828eaeed3dfe0b790f3e59a8edfc4c02ff375bbd3e6f57cfbdbf23ff1e4303e5c13098c8ecc5e43fb291737317a4fb7e6c72d99df829e57e15e0bd83b818bf9d65465d46e09819e77d81b7d304498643b30f0ae833317a584f0d686622e8146411f0cd7cab4c75a9bcd765b123bcaaf472f3ebce82506d080002e66ca83dda37133816105da3dc3cf3bf2b1bf5d607df36ac41b3682c2431516a2418367cfe1bf00551ad482665ae3db538dcd3946336ea1049b86bdff1fd00b5872a87c1f1fe021a75ec3ffa98eb345ac3aa92b7a032980c40c8145f6cab152f43cb37eb4b5abed7ff6f87a493cf9f0691d4789608023f3afc0200c354680a2a22557ba6cdc07cc30f8e66cfaa325b786e06305e1dfce0852';
    $redirectUri  = 'https://kitchen.g99322e8.beget.tech/oauth2/';
    $subdomain    = 'tatama7206';

    $domain = AmoAPI::getAmoDomain($subdomain);
    $isFirstAuth = !(new FileStorage())->hasTokens($domain);

    if ($isFirstAuth) {
        // Первичная авторизация
        AmoAPI::oAuth2($subdomain, $clientId, $clientSecret, $redirectUri, $authCode);
    } else {
        // Последующие авторизации
        AmoAPI::oAuth2($subdomain);
    }

    // Получение информации об аккаунте вместе с пользователями и группами
    // Все параметры для вывода данных можно посмотреть в /vendor/andrey-tech/amocrm-api-php/README.md
    // print_r(AmoAPI::getAccount($with = 'users,groups'));
    //print_r(AmoAPI::getAccount($with = 'pipelines'));
    print_r(AmoAPI::getAccount($with = 'custom_fields')); 

} catch (AmoAPIException $e) {
    printf('Ошибка авторизации (%d): %s' . PHP_EOL, $e->getCode(), $e->getMessage());
} catch (TokenStorageException $e) {
    printf('Ошибка обработки токенов (%d): %s' . PHP_EOL, $e->getCode(), $e->getMessage());
}