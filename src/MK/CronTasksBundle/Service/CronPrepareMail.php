<?php
namespace MK\CronTasksBundle\Service;

use OldSound\RabbitMqBundle\RabbitMq\Producer;

class CronPrepareMail
{
    private $producer;

    public function __construct(Producer $producer)
    {
        $this->producer = $producer;
    }

    public function process($subject, $content, $mailTo)
    {
        $body = array(
            'subject' => $subject,
            'content' => $content,
            'mailTo' => $mailTo,
        );

        $this->producer->publish(serialize($body));
    }
}