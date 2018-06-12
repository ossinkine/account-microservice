<?php

declare(strict_types=1);

namespace App\Handler;

use App\Event\VoidEvent;
use App\Events;
use App\Message\VoidMessage;
use App\Repository\AccountRepository;
use Doctrine\DBAL\LockMode;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class VoidHandler implements MessageHandlerInterface
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

    public function __invoke(VoidMessage $message): void
    {
        $account = $this->accountRepository->findOrCreateAccount($message->getUserId());
        $this->entityManager->transactional(function () use ($message, $account): void {
            $this->entityManager->lock($account, LockMode::PESSIMISTIC_WRITE);
            $this->entityManager->refresh($account);
            $account->decreaseBlockedBalance($message->getAmount());
            $account->increaseBalance($message->getAmount());
            $this->accountRepository->persist($account);
        });
        $this->eventDispatcher->dispatch(Events::VOID, new VoidEvent($message->getUserId(), $message->getAmount()));
    }
}
