<?php

namespace App\Services\Sms;

/**
 * Interface SmsSender
 *
 * @package App\Services\Sms
 */
interface SmsSender
{
    /**
     * @param string $number
     * @param string $text
     */
    public function send(string $number, string $text): void;
}
