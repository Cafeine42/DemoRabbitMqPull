<?php


namespace App\MessageHandler;


use App\Message\SleepMessage;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Stopwatch\Stopwatch;

class SleepMessageHandler implements MessageHandlerInterface
{
    private Stopwatch $stopwatch;
    private LoggerInterface $logger;

    public function __construct(Stopwatch $stopwatch, LoggerInterface $logger){
        $this->stopwatch = $stopwatch;
        $this->logger = $logger;
    }

    public function __invoke(SleepMessage $message)
    {
        $this->stopwatch->start('message');

//        usleep($message->getSleepMs() * 1000);

        $event = $this->stopwatch->stop('message');

        $this->logger->info(sprintf('Handle message took %d ms', $event->getDuration()));

        $this->stopwatch->reset();
    }
}