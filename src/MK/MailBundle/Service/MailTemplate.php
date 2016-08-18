<?php
namespace MK\MailBundle\Service;

use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\DependencyInjection\Container;

/**
 * Class MailTemplate
 * @package MK\MailBundle\Service
 *
 * How to use:
 *          $service = $this->get('mk_mail_engine.class');
 *          $service->sendMail('Subject','Test message', 'sklepflock@gmail.com');
 *
 */
class MailTemplate
{
    private $container;
    private $templating;
    private $logger;
    private $sender;
    private $template;

    public function __construct(Container $container, EngineInterface $templating, $sender = array(), $template)
    {
        $this->container = $container;
        $this->templating = $templating;
        $this->logger = $this->container->get('monolog.logger.services');
        $this->sender = $sender;
        $this->template = $template;
    }

    public function sendMail($subject, $content, $mailTo)
    {
        $rendered = $this->templating->render($this->template, array('content' => $content));

        $message = \Swift_Message::newInstance()
            ->setSubject($subject)
            ->setFrom($this->sender['address'], $this->sender['name'])
            ->setTo($mailTo)
            ->setBody($rendered, 'text/html');
        $result = $this->container->get('mailer')->send($message);

        if ($result) {
            $this->logger->info(sprintf("Success [Swift_Message] to [%s].", $mailTo));
        } else {
            $this->logger->error(sprintf("Error [Swift_Message] to [%s].", $mailTo));
        }
        return $result;
    }

    public function sendMailWithTemplate($subject, $params, $mailTo, $template)
    {
        $templateName = 'MKMailBundle:Emails:Parts/' . $template . '.html.twig';
        $content = $this->templating->render($templateName, array('params' => $params));
        $this->sendMail($subject, $content, $mailTo);
    }
}