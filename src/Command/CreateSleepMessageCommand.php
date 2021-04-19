<?php

namespace App\Command;

use App\Message\SleepMessage;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Messenger\MessageBusInterface;

class CreateSleepMessageCommand extends Command
{
    protected static $defaultName = 'app:create-sleep-message';
    protected static $defaultDescription = 'Send a SleepMessage';

    private MessageBusInterface $messageBus;

    public function __construct(MessageBusInterface $messageBus
    )
    {
        $this->messageBus = $messageBus;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription(self::$defaultDescription)
            ->addArgument('count', InputArgument::OPTIONAL, 'Number of message', 1)
            ->addArgument('sleep', InputArgument::OPTIONAL, 'Sleep value in ms (Default: 500)', 500)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $count = intval($input->getArgument('count'));
        $sleepSecond = intval($input->getArgument('sleep'));

        if($count <= 0){
            $io->error('Count must be a positive integer.');
            return false;
        }

        if($sleepSecond <= 0){
            $io->error('Sleep must be a positive integer.');
            return false;
        }

        $message = new SleepMessage($sleepSecond);
        for($i = 0; $i < $count; ++$i){
            $this->messageBus->dispatch($message);
        }

        $io->success(sprintf('%d Message sent.', $count));

        return Command::SUCCESS;
    }
}
