<?php

declare(strict_types=1);

namespace App\Command;

use App\Message\AuthMessage;
use App\Message\CaptureMessage;
use App\Message\CreditMessage;
use App\Message\DebitMessage;
use App\Message\VoidMessage;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class TransactionCommand extends Command
{
    /**
     * @var string
     */
    protected static $defaultName = 'app:transaction';

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
            ->setDescription('Creates transaction on user account')
            ->addArgument('user_id', InputArgument::REQUIRED, 'User ID')
            ->addArgument('type', InputArgument::REQUIRED, 'Transaction type (debit, credit, auth, void, capture)')
            ->addArgument('amount', InputArgument::REQUIRED, 'Transaction amount (for example, 123.45)')
            ->addOption('quantity', null, InputOption::VALUE_REQUIRED, 'Transactions quantity', 1)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $classMap = [
            'debit' => DebitMessage::class,
            'credit' => CreditMessage::class,
            'auth' => AuthMessage::class,
            'void' => VoidMessage::class,
            'capture' => CaptureMessage::class,
        ];
        $type = $input->getArgument('type');
        if (!array_key_exists($type, $classMap)) {
            throw new \InvalidArgumentException('Unknown transaction type');
        }
        $amount = $input->getArgument('amount');
        if (!is_numeric($amount)) {
            throw new \InvalidArgumentException('Amount must be numeric');
        }
        $class = $classMap[$type];
        $message = new $class(
            $input->getArgument('user_id'),
            $amount
        );
        $quantity = max($input->getOption('quantity'), 1);
        while ($quantity--) {
            $this->bus->dispatch($message);
        }
    }
}
