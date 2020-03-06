<?php
require_once("./vendor/autoload.php");

use Viber\Client;

$apiKey = '4b2c8ae9d8e7dd17-a4ddce61389be519-78f5da28d7dc8cd3'; // <- PLACE-YOU-API-KEY-HERE
$webhookUrl = 'https://renessans-viber-bot.herokuapp.com/bot.php'; // <- PLACE-YOU-HTTPS-URL

try {
    $client = new Client([ 'token' => $apiKey ]);
    $result = $client->setWebhook($webhookUrl);
    echo "Success!\n";
} catch (Exception $e) {
    echo "Error: ". $e->getError() ."\n";
}