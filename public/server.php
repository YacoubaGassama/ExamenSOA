<?php
declare(strict_types=1);

ini_set('display_errors', '1');
error_reporting(E_ALL);
ini_set('soap.wsdl_cache_enabled', '0');

// Inclure les fichiers nécessaires pour les DTOs, Messages et le Service
require_once __DIR__ . '/transaction.php';
require_once __DIR__ . '/TransactionInput.php';
require_once __DIR__ . '/CreateTransactionRequest.php';
require_once __DIR__ . '/CreateTransactionResponse.php';
require_once __DIR__ . '/GetTransactionRequest.php';
require_once __DIR__ . '/GetTransactionResponse.php';
require_once __DIR__ . '/UpdateTransactionRequest.php';
require_once __DIR__ . '/UpdateTransactionResponse.php';
require_once __DIR__ . '/DeleteTransactionRequest.php';
require_once __DIR__ . '/DeleteTransactionResponse.php';

require_once __DIR__ . '/TransactionService.php';

// Classmap pour faire correspondre les noms de classes WSDL aux classes PHP
$classmap = [
    'Transaction' => App\Dto\Transaction::class,
    'TransactionInput' => App\Dto\TransactionInput::class,
    'CreateTransactionRequest' => App\Message\CreateTransactionRequest::class,
    'CreateTransactionResponse' => App\Message\CreateTransactionResponse::class,
    'GetTransactionRequest' => App\Message\GetTransactionRequest::class,
    'GetTransactionResponse' => App\Message\GetTransactionResponse::class,
    'UpdateTransactionRequest' => App\Message\UpdateTransactionRequest::class,
    'UpdateTransactionResponse' => App\Message\UpdateTransactionResponse::class,
    'DeleteTransactionRequest' => App\Message\DeleteTransactionRequest::class,
    'DeleteTransactionResponse' => App\Message\DeleteTransactionResponse::class,
];

// Configuration du serveur SOAP
$options = [
    'uri' => 'http://localhost:8001/server.php',
    'classmap' => $classmap,
    'exceptions' => true,
    'trace' => 1,
    'soap_version' => SOAP_1_2,
    'cache_wsdl' => WSDL_CACHE_NONE,
];

$wsdl = __DIR__ . '/TransactionService.wsdl';
$server = new SoapServer($wsdl, $options);
$server->setClass(TransactionService::class);
$server->handle();
