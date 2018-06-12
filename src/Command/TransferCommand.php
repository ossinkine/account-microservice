<?php

declare(strict_types=1);

namespace App\Command;

use App\Message\TransferMessage;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class TransferCommand extends Command
{
    /**
     * @var string
     */
    protected static $defaultName = 'app:transfer';

    /**
     * @var MessageBusInterface
     */
    private $bus;

    public function __construct(MessageBusInterface $bus)
    {
        parent::__construct();

        $this->bus = $bus;
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Transfers funds between user accounts')
            ->addArgument('source_user_id', InputArgument::REQUIRED, 'Source user ID')
            ->addArgument('destination_user_id', InputArgument::REQUIRED, 'Destination user ID')
            ->addArgument('amount', InputArgument::REQUIRED, 'Transaction amount (for example, 123.45)')
            ->addOption('quantity', null, InputOption::VALUE_REQUIRED, 'Transactions quantity', 1)
            ->addOption('reverse', null, InputOption::VALUE_NONE, 'Add reverse transaction to test deadlocks')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $amount = (string) $input->getArgument('amount');
        if (!is_numeric($amount)) {
            throw new \InvalidArgumentException('Amount must be numeric');
        }
        $message = new TransferMessage(
            $input->getArgument('source_user_id'),
            $input->getArgument('destination_user_id'),
            $amount
        );
        $addReverseMessage = (bool) $input->getOption('reverse');
        if ($addReverseMessage) {
            $reverseMessage = new TransferMessage(
                $input->getArgument('destination_user_id'),
                $input->getArgument('source_user_id'),
                $amount
            );
        }
        $quantity = max($input->getOption('quantity'), 1);
        while ($quantity--) {
            $this->bus->dispatch($message);
            if (isset($reverseMessage)) {
                $this->bus->dispatch($reverseMessage);
            }
        }
    }
}
