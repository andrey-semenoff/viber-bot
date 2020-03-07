<?php

require_once("./vendor/autoload.php");

use Viber\Bot;
use Viber\Api\Sender;

$apiKey = '4b2c8ae9d8e7dd17-a4ddce61389be519-78f5da28d7dc8cd3';

// reply name
$botSender = new Sender([
    'name' => 'Renessans bot',
    // 'avatar' => 'https://developers.viber.com/img/favicon.ico',
    'avatar' => 'https://miro.medium.com/fit/c/256/256/1*Ro9Yq4D5W85QagF2rFTivw.png',
]);

try {
    $bot = new Bot(['token' => $apiKey]);
    $bot
    ->onConversation(function ($event) use ($bot, $botSender) {
        // this event fires if user open chat, you can return "welcome message"
        // to user, but you can't send more messages!
        return (new \Viber\Api\Message\Text())
            ->setSender($botSender)
            ->setText("Can i help you?");
    })
    ->onText('|whois .*|si', function ($event) use ($bot, $botSender) {
        // match by template, for example "whois Bogdaan"
        $bot->getClient()->sendMessage(
            (new \Viber\Api\Message\Text())
            ->setSender($botSender)
            ->setReceiver($event->getSender()->getId())
            ->setText("I do not know )")
        );
    })
    ->onText('.*', function ($event) use ($bot, $botSender) {
        // match by template, for example "whois Bogdaan"
        $bot->getClient()->sendMessage(
            (new \Viber\Api\Message\Text())
            ->setSender($botSender)
            ->setReceiver($event->getSender()->getId())
            ->setText("Select action")
            ->setKeyboard(
                (new \Viber\Api\Keyboard)
                ->setButtons([
                    (new \Viber\Api\Keyboard\Button())
                    ->setColumns(6)
                    ->setText('Click me')
                    ->setActionType('reply')
                    ->setActionBody('Clicked!')
                ])
            )
        );
    })
    ->run();
} catch (Exception $e) {
    // todo - log exceptions
}