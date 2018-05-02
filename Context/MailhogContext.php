<?php
declare(strict_types=1);

namespace ItkDev\Behat\Context;

use Behat\Mink\Mink;
use Behat\MinkExtension\Context\MinkAwareContext;
use Exception;
use rpkamp\Mailhog\MailhogClient;
use rpkamp\Behat\MailhogExtension\Context\MailhogAwareContext;
use rpkamp\Mailhog\Message\Contact;

class MailhogContext implements MailhogAwareContext, MinkAwareContext
{
    /** @var  MailhogClient */
    private $mailhog;

    /** @var  Mink */
    private $mink;

    /** @var  array */
    private $minkParameters;

    public function setMailhog(MailhogClient $client)
    {
        $this->mailhog = $client;
    }

    public function setMink(Mink $mink)
    {
        $this->mink = $mink;
    }

    public function setMinkParameters(array $parameters)
    {
        $this->minkParameters = $parameters;
    }

    /**
     * @When /^I follow the (?P<index>\d+)(?:st|nd|rd|th) link in email sent to "(?P<recipient>[^"]+)"$/
     */
    public function iFollowTheNthLinkInEmailSentTo($index, $recipient)
    {
        $message = $this->mailhog->getLastMessage();
        if (!$message->recipients->contains(Contact::fromString($recipient))) {
            throw new Exception(sprintf('Last message not sent to "%s"', $recipient));
        }
        $urlPattern = '@(?P<url>[a-z]+://\S+)@';
        if (!preg_match_all($urlPattern, $message->body, $matches, PREG_SET_ORDER)) {
            throw new Exception(sprintf('No url found in message body (%s)', $message->body));
        }

        $index--;
        if (!isset($matches[$index]['url'])) {
            throw new Exception(sprintf('No url with index %d found in message body (%s)', $index));
        }

        $this->mink->getSession()->visit($matches[$index]['url']);
    }
}
