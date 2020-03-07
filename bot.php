<?php

require_once("./vendor/autoload.php");

use Viber\Bot;
use Viber\Api\Sender;

$apiKey = '4b2c8ae9d8e7dd17-a4ddce61389be519-78f5da28d7dc8cd3';

$botSender = new Sender([
    'name' => 'Renessans bot',
//    'avatar' => 'https://miro.medium.com/fit/c/256/256/1*Ro9Yq4D5W85QagF2rFTivw.png',
    'avatar' => 'https://renessans-viber-bot.herokuapp.com/images/avatar.png',
]);

try {
    $bot = new Bot(['token' => $apiKey]);
    $bot
    ->onConversation(function ($event) use ($bot, $botSender) {
        $user = $bot->getClient()->getAccountInfo()->getData();
        $user2 = $bot->getClient()->getAccountInfo();
        var_dump($user['name']);
        var_dump($user2['name']);
        // this event fires if user open chat, you can return "welcome message"
        // to user, but you can't send more messages!
        $msg = 'Здравствуйте';
        if( $user['name'] ) {
            $msg .= ', ' . $user['name'];
        } else {
            $msg .= '!';
        }
        return (new \Viber\Api\Message\Text())
            ->setSender($botSender)
            ->setText($msg);
    })
        
    ->onText('/^вода\s*(\d+)\s*кв\s*(\d+)$/iu', function ($event) use ($bot, $botSender) {
        $reply = $event->getMessage()->getText();
        $data = parseWaterValue('/^вода\s*(\d+)\s*кв\s*(\d+)$/iu', $reply);
        $answer = "Извините, я не могу разобрать ваши показания!\nПопробуйте еще раз!";
        if( $data ) {
            $answer = "Подтвердите правильность данных!\nВы собираетесь передать следующие показания для квартиры №{$data['flat']}:\n- вода: {$data['value']}";
        }
        $bot->getClient()->sendMessage(
            (new \Viber\Api\Message\Text())
            ->setSender($botSender)
            ->setReceiver($event->getSender()->getId())
            ->setText($answer)
            ->setKeyboard(
                (new \Viber\Api\Keyboard)
                ->setButtons([
                    (new \Viber\Api\Keyboard\Button())
                    ->setColumns(3)
                    ->setText('<font color="#fff">Отмена</font>')
                    ->setTextSize('large')
                    ->setTextHAlign('center')
                    ->setTextVAlign('middle')
                    ->setBgColor('#c82333')
                    ->setActionBody("canceled вода {$data['value']} кв {$data['flat']}"),
                    (new \Viber\Api\Keyboard\Button())
                    ->setColumns(3)
                    ->setText('<font color="#fff">Подтверждаю</font>')
                    ->setTextSize('large')
                    ->setTextHAlign('center')
                    ->setTextVAlign('middle')
                    ->setBgColor('#28a745')
                    ->setActionBody("confirmed вода {$data['value']} кв {$data['flat']}"),
                ])
            )
        );
    })

    ->onText('/^confirmed\sвода\s*(\d+)\s*кв\s*(\d+)$/iu', function ($event) use ($bot, $botSender) {
        $reply = $event->getMessage()->getText();
        $data = parseWaterValue('/^confirmed\sвода\s*(\d+)\s*кв\s*(\d+)$/iu', $reply);
        $answer = "Извините, я не могу разобрать ваши показания!\nПопробуйте еще раз!";
        if( $data ) {
            $answer = "Спасибо!\nПриняты показания для квартиры №{$data['flat']}:\n- вода: {$data['value']}";
        }
        $bot->getClient()->sendMessage(
            (new \Viber\Api\Message\Text())
            ->setSender($botSender)
            ->setReceiver($event->getSender()->getId())
            ->setText($answer)
        );
    })

    ->onText('/^canceled\sкв\s*(\d+)$/iu', function ($event) use ($bot, $botSender) {
        $reply = $event->getMessage()->getText();
        $flat = parseSingleValue('/^canceled\sкв\s*(\d+)$/iu', $reply);
        $answer = "Извините, я не могу разобрать ваш ответ!\nПопробуйте еще раз!";
        if( $flat ) {
            $answer = "Вы отменили передачу показаний для квартиры №{$flat}";
        }
        $bot->getClient()->sendMessage(
            (new \Viber\Api\Message\Text())
            ->setSender($botSender)
            ->setReceiver($event->getSender()->getId())
            ->setText($answer)
        );
    })

    ->onText('/^help\s*(.*)$/', function ($event) use ($bot, $botSender) {
        $reply = $event->getMessage()->getText();
        $hint = parseSingleValue('/^help\s*(.*)$/iu', $reply);
        $answer = "Извините, я не могу разобрать ваше сообщение!\nПопробуйте еще раз!";
        if( $hint ) {
            $answer = $hint;
        }

        $bot->getClient()->sendMessage(
            (new \Viber\Api\Message\Text())
                ->setSender($botSender)
                ->setReceiver($event->getSender()->getId())
                ->setText($answer)
        );
    })

    ->onText('/.*/', function ($event) use ($bot, $botSender) {
        $answer = "Здравствуйте!\nЕсли Вам нужна помощь - выберите подходящий вариант из предложенных внизу!";
        $bot->getClient()->sendMessage(
            (new \Viber\Api\Message\Text())
                ->setSender($botSender)
                ->setReceiver($event->getSender()->getId())
                ->setText($answer)
                ->setKeyboard(
                    (new \Viber\Api\Keyboard)
                        ->setButtons([
                            (new \Viber\Api\Keyboard\Button())
                                ->setColumns(6)
                                ->setText('<font color="#fff">Передать показания воды</font>')
                                ->setTextSize('large')
                                ->setTextHAlign('center')
                                ->setTextVAlign('middle')
                                ->setBgColor('#17a2b8')
                                ->setActionBody("help Чтобы передать показания воды введите сообщение в следующем формате:\nвода X кв Y\nгде Х - показания водомера, Y - номер квартиры.")
                        ])
                )
        );
    })
    ->run();
} catch (Exception $e) {
    // todo - log exceptions
}

function parseWaterValue(string $regex, string $str) {
    $result = null;
    preg_match($regex, $str, $arr);
    if( !empty($arr) ) {
        $result = [
            'value' => $arr[1],
            'flat' => $arr[2]
        ];
    }
    return $result;
}

function parseSingleValue(string $regex, string $str) {
    $result = null;
    preg_match($regex, $str, $arr);
    if( !empty($arr) ) {
        $result = $arr[1];
    }
    return $result;
}