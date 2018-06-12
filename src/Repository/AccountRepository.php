<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Account;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Account|null find($id, $lockMode = null, $lockVersion = null)
 * @method Account[] findAll()
 */
class AccountRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Account::class);
    }

    public function findOrCreateAccount(string $userId): Account
    {
        $account = $this->find($userId);
        if (null === $account) {
            $account = $this->_em->transactional(function () use ($userId) {
                $this->lockTable();
                $account = $this->find($userId);
                if (null === $account) {
                    $account = new Account($userId);
                    $this->persist($account);
                }

                return $account;
            });
        }

        return $account;
    }

    public function persist(Account $account): void
    {
        $this->_em->persist($account);
        $this->_em->flush();
    }

    private function lockTable(): void
    {
        $mode = 'SHARE UPDATE EXCLUSIVE';
        $this->_em->getConnection()->exec(sprintf('LOCK TABLE %s IN %s MODE', $this->getClassMetadata()->getTableName(), $mode));
    }
}
