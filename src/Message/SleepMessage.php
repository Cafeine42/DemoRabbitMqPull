<?php


namespace App\Message;


class SleepMessage
{
    private int $sleepMs;

    /**
     * SleepMessage constructor.
     * @param int $sleepMs
     */
    public function __construct(int $sleepMs)
    {
        $this->sleepMs = $sleepMs;
    }

    /**
     * @return int
     */
    public function getSleepMs(): int
    {
        return $this->sleepMs;
    }
}