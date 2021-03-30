<?php

namespace App\Services\Sms;

class ArraySender implements SmsSender
{
    /**
     * @var array $messages
     */
    private $messages = [];

    /**
     * @param string $number
     * @param string $text
     */
    public function send(string $number, string $text): void
    {
        $this->messages[] = [
            'to' => '+' . trim($number, '+'),
            'text' => $text
        ];
    }

    /**
     * @return array $messages
     */
    public function getMessages(): array
    {
        return $this->messages;
    }
}
