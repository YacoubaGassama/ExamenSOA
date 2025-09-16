<?php
declare(strict_types=1);

ini_set('display_errors', '1');
error_reporting(E_ALL);
ini_set('soap.wsdl_cache_enabled', '0');

$wsdl = 'http://localhost:8001/TransactionService.wsdl';

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

$client = new SoapClient($wsdl, [
    'trace' => 1,
    'exceptions' => true,
    'classmap' => $classmap,
    'cache_wsdl' => WSDL_CACHE_NONE,
    'soap_version' => SOAP_1_2,
]);

echo "== CREATE ==\n";
$createReq = new App\Message\CreateTransactionRequest();
$input = new App\Dto\TransactionInput();
$input->date = '2025-09-16';
$input->type = 'DEPOT';
$input->montant = 5000.0;
$input->tel_expediteur = '770000001';
$input->tel_destinataire = '780000002';
$input->nom_expediteur = 'Alice';
$input->nom_destinataire = 'Bob';
$createReq->transaction = $input;
$createRes = $client->createTransaction($createReq);
var_dump($createRes);

echo "== LIST ==\n";
$listRes = $client->getAllTransactions([]);
var_dump($listRes);
