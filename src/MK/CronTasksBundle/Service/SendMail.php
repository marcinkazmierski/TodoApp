<?php
namespace MK\CronTasksBundle\Service;

use MK\MailBundle\Service\MailTemplate;
use Monolog\Logger;
use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;

class SendMail implements ConsumerInterface
{
    private $logger;
    private $mailTemplate;

    public function __construct(Logger $logger, MailTemplate $mailTemplate)
    {
        $this->logger = $logger;
        $this->mailTemplate = $mailTemplate;
    }

    public function execute(AMQPMessage $msg)
    {
        $body = unserialize($msg->body);

        if (empty($body['mailTo'])) {
            $this->logger->info("Empty email [SendMail]");
            return false;
        }

        $result = $this->mailTemplate->sendMail(
            $body['subject'], $body['content'], $body['mailTo']
        );

        sleep(2); // only for test, TODO: remove sleep
        $this->logger->info(sprintf('Send mail to: "%s" - result: %d', $body['mailTo'], $result));
        return true;
    }
}