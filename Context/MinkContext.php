<?php

namespace ItkDev\Behat\Context;

use Behat\Behat\Tester\Exception\PendingException;
use Behat\Mink\Mink;
use Behat\MinkExtension\Context\MinkAwareContext;

class MinkContext implements MinkAwareContext
{
    /** @var  Mink */
    private $mink;

    /** @var  array */
    private $minkParameters;

    public function setMink(Mink $mink)
    {
        $this->mink = $mink;
    }

    public function setMinkParameters(array $parameters)
    {
        $this->minkParameters = $parameters;
    }

    /**
     * @Then I should be on url matching :pattern
     */
    public function iShouldBeOnUrlMatching($pattern)
    {
        $url = $this->mink->getSession()->getCurrentUrl();
        throw new PendingException($pattern . ' ' . $url);
    }
}
