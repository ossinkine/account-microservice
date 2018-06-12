<?php

declare(strict_types=1);

namespace App\Command;

use App\Repository\AccountRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableCell;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DumpCommand extends Command
{
    /**
     * @var string
     */
    protected static $defaultName = 'app:dump';

    /**
     * @var AccountRepository
     */
    private $accountRepository;

    public function __construct(AccountRepository $accountRepository)
    {
        parent::__construct();

        $this->accountRepository = $accountRepository;
    }

    protected function configure(): void
    {
        $this->setDescription('Show all accounts');
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $table = new Table($output);
        $table->setHeaders(['User ID', 'Balance', 'Blocked balance']);
        $accounts = $this->accountRepository->findAll();
        if (count($accounts) > 0) {
            foreach ($accounts as $account) {
                $table->addRow([$account->getUserId(), $account->getBalance(), $account->getBlockedBalance()]);
            }
        } else {
            $table->addRow([new TableCell('Account list is empty.', ['colspan' => 3])]);
        }
        $table->render();
    }
}
