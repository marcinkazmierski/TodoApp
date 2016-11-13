<?php
namespace MK\SMSBundle\Service;

use Symfony\Component\DependencyInjection\Container;

/**
 * Class PlusSMSSender
 * @package MK\SMSBundle\Service
 *
 * How to use:
 *          $service = $this->get('mk_sms_engine.class');
 *          $service->sendSMS('123456789', 'test message');
 */
class PlusSMSSender
{
    private $container;
    private $logger;
    private $sender;

    public function __construct(Container $container, $sender = array())
    {
        $this->container = $container;
        $this->logger = $this->container->get('monolog.logger.services');
        $this->sender = $sender;
    }

    /**
     * Only plus gateway.
     */
    public function sendSMS($phoneNumber, $body)
    {
        $body = strtr($body, 'ĘÓĄŚŁŻŹĆŃęóąśłżźćń', 'EOASLZZCNeoaslzzcn');

        $message = \Swift_Message::newInstance()
            ->setSubject('')
            ->setFrom($this->sender['address'])
            ->setTo('+48' . $phoneNumber . '@text.plusgsm.pl')
            ->setBody($body, 'text/plain');
        $result = $this->container->get('mailer')->send($message);
        if ($result) {
            $this->logger->info(sprintf("Success [SMS] to [%s].", $phoneNumber));
        } else {
            $this->logger->error(sprintf("Error [SMS] to [%s].", $phoneNumber));
        }
        return $result;
    }
}