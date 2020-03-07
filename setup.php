<?php
require_once("./vendor/autoload.php");

use Viber\Client;

$apiKey = '4b2c8ae9d8e7dd17-a4ddce61389be519-78f5da28d7dc8cd3';
    $webhookUrl = 'https://renessans-viber-bot.herokuapp.com/bot.php';

try {
    $client = new Client([ 'token' => $apiKey ]);
    $result = $client->setWebhook($webhookUrl);
    echo "Success!\n";
} catch (Exception $e) {
    echo "Error: ". $e->getError() ."\n";
}