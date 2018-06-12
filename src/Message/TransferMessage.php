<?php

declare(strict_types=1);

namespace App\Message;

class TransferMessage
{
    /**
     * @var string
     */
    private $sourceUserId;

    /**
     * @var string
     */
    private $destinationUserId;

    /**
     * @var string
     */
    private $amount;

    public function __construct(string $sourceUserId, string $destinationUserId, string $amount)
    {
        $this->sourceUserId = $sourceUserId;
        $this->destinationUserId = $destinationUserId;
        $this->amount = $amount;
    }

    public function getSourceUserId(): string
    {
        return $this->sourceUserId;
    }

    public function getDestinationUserId(): string
    {
        return $this->destinationUserId;
    }

    public function getAmount(): string
    {
        return $this->amount;
    }
}
