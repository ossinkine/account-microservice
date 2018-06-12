<?php

declare(strict_types=1);

namespace App\Event;

use Symfony\Component\EventDispatcher\Event;

abstract class AbstractAccountEvent extends Event
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
