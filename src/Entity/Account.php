<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AccountRepository")
 */
class Account
{
    private const SCALE = 2;

    /**
     * @ORM\Id
     * @ORM\Column(type="string", unique=true)
     *
     * @var string
     */
    private $userId;

    /**
     * @ORM\Column(type="bigint")
     *
     * @var string
     */
    private $balance;

    /**
     * @ORM\Column(type="bigint")
     *
     * @var string
     */
    private $blockedBalance;

    public function __construct(string $userId)
    {
        $this->userId = $userId;
        $this->balance = '0';
        $this->blockedBalance = '0';
    }

    public function getUserId(): string
    {
        return $this->userId;
    }

    public function getBalance(): string
    {
        return $this->formatAmount($this->balance);
    }

    public function increaseBalance(string $amount): void
    {
        $this->balance = bcadd($this->balance, $this->canonicalizeAmount($amount));
    }

    public function decreaseBalance(string $amount): void
    {
        $this->balance = bcsub($this->balance, $this->canonicalizeAmount($amount));
    }

    public function getBlockedBalance(): string
    {
        return $this->formatAmount($this->blockedBalance);
    }

    public function increaseBlockedBalance(string $amount): void
    {
        $this->blockedBalance = bcadd($this->blockedBalance, $this->canonicalizeAmount($amount));
    }

    public function decreaseBlockedBalance(string $amount): void
    {
        $this->blockedBalance = bcsub($this->blockedBalance, $this->canonicalizeAmount($amount));
    }

    private function canonicalizeAmount(string $amount): string
    {
        return bcmul($amount, (string) (10 ** static::SCALE));
    }

    private function formatAmount(string $amount): string
    {
        return bcdiv($amount, (string) (10 ** static::SCALE), static::SCALE);
    }
}
