<?php

declare(strict_types=1);

namespace App\Message;

abstract class AbstractAccountMessage
{
    /**
     * @var string
     */
    private $userId;

    /**
     * @var string
     */
    private $amount;

    public function __construct(string $userId, string $amount)
    {
        $this->userId = $userId;
        $this->amount = $amount;
    }

    public function getUserId(): string
    {
        return $this->userId;
    }

    public function getAmount(): string
    {
        return $this->amount;
    }
}
