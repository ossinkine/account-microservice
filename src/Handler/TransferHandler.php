<?php

declare(strict_types=1);

namespace App\Handler;

use App\Entity\Account;
use App\Event\TransferEvent;
use App\Events;
use App\Message\TransferMessage;
use App\Repository\AccountRepository;
use Doctrine\DBAL\LockMode;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class TransferHandler implements MessageHandlerInterface
{
    /**
     * @var AccountRepository
     */
    private $accountRepository;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    public function __construct(AccountRepository $accountRepository, EntityManagerInterface $entityManager, EventDispatcherInterface $eventDispatcher)
    {
        $this->accountRepository = $accountRepository;
        $this->entityManager = $entityManager;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function __invoke(TransferMessage $message): void
    {
        $sourceAccount = $this->accountRepository->findOrCreateAccount($message->getSourceUserId());
        $destinationAccount = $this->accountRepository->findOrCreateAccount($message->getDestinationUserId());
        $this->entityManager->transactional(function () use ($message, $sourceAccount, $destinationAccount): void {
            $this->lock([$sourceAccount, $destinationAccount]);
            $sourceAccount->decreaseBalance($message->getAmount());
            $destinationAccount->increaseBalance($message->getAmount());
            $this->accountRepository->persist($sourceAccount);
            $this->accountRepository->persist($destinationAccount);
        });
        $this->eventDispatcher->dispatch(Events::TRANSFER, new TransferEvent($message->getSourceUserId(), $message->getDestinationUserId(), $message->getAmount()));
    }

    /**
     * @param Account[] $accounts
     */
    private function lock(array $accounts): void
    {
        // lock accounts in alphabetical order to avoid dead locks
        usort($accounts, function (Account $left, Account $right) {
            return strcmp($left->getUserId(), $right->getUserId());
        });
        foreach ($accounts as $account) {
            $this->entityManager->lock($account, LockMode::PESSIMISTIC_WRITE);
            $this->entityManager->refresh($account);
        }
    }
}
