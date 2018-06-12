<?php

declare(strict_types=1);

namespace App\Handler;

use App\Event\DebitEvent;
use App\Events;
use App\Message\DebitMessage;
use App\Repository\AccountRepository;
use Doctrine\DBAL\LockMode;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class DebitHandler implements MessageHandlerInterface
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

    public function __invoke(DebitMessage $message): void
    {
        $account = $this->accountRepository->findOrCreateAccount($message->getUserId());
        $this->entityManager->transactional(function () use ($message, $account): void {
            $this->entityManager->lock($account, LockMode::PESSIMISTIC_WRITE);
            $this->entityManager->refresh($account);
            $account->increaseBalance($message->getAmount());
            $this->accountRepository->persist($account);
        });
        $this->eventDispatcher->dispatch(Events::DEBIT, new DebitEvent($message->getUserId(), $message->getAmount()));
    }
}
