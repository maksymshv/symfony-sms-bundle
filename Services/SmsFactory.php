<?php

namespace cspoo\SmsBundle\Services;

use Symfony\Component\DependencyInjection\ContainerAware;

use cspoo\SmsBundle\Transport;
use cspoo\SmsBundle\Model\Sms;

class SmsFactory extends ContainerAware
{
	private $transport_name = null;
    private $transport = null;

    public function sendSms(Sms $sms)
    {
        $this->loadTransport();
        return $this->transport->sendSms($sms);
    }

    private function loadTransport()
    {
        if ($this->transport != null)
            return;

        if ($this->transport_name == null)
            $this->transport_name = $this->container->getParameter('sms.default_transport');

        switch ($this->transport_name)
        {
            case 'smscreator':
                $transport = new Transport\SmscreatorTransport();

                $username = $this->container->getParameter(sprintf('sms.transports.%s.username', $this->transport_name));
                $transport->setUsername($username);

                $password = $this->container->getParameter(sprintf('sms.transports.%s.password', $this->transport_name));
                $transport->setPassword($password);

                $this->transport = $transport;
                break;
        }

        if ($this->transport == null)
            throw new \Exception('Could not initialize SMS transport interface');
    }

    public function createSms($recipient, $message)
    {
    	$sms = new Sms();

    	$sms->setRecipient($recipient);
    	$sms->setMessage($message);

    	return $sms;
    }
}