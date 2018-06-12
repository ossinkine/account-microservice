<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\Account;
use PHPUnit\Framework\TestCase;

class AccountTest extends TestCase
{
    /**
     * @var Account
     */
    private $account;

    protected function setUp(): void
    {
        $this->account = new Account('foo');
    }

    public function testGetUserId(): void
    {
        $this->assertSame('foo', $this->account->getUserId());
    }

    public function testBalance(): void
    {
        $this->assertSame('0.00', $this->account->getBalance());
        $this->account->increaseBalance('123456789012345.67');
        $this->assertSame('123456789012345.67', $this->account->getBalance());
        $this->account->decreaseBalance('1.67');
        $this->assertSame('123456789012344.00', $this->account->getBalance());
    }

    public function testBlockedBalance(): void
    {
        $this->assertSame('0.00', $this->account->getBlockedBalance());
        $this->account->increaseBlockedBalance('123456789012345.67');
        $this->assertSame('123456789012345.67', $this->account->getBlockedBalance());
        $this->account->decreaseBlockedBalance('1.67');
        $this->assertSame('123456789012344.00', $this->account->getBlockedBalance());
    }
}
