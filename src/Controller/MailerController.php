<?php

namespace App\Controller;

use Symfony\Component\Mime\Email;
use App\Repository\DevicesRepository;
use App\Controller\ApplicationController;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Notifier\TexterInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Notifier\Message\SmsMessage;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MailerController extends ApplicationController
{
    /**
     * @Route("/mailer", name="mailer")
     */
    public function index()
    {
        return $this->render('mailer/index.html.twig', [
            'controller_name' => 'MailerController',
        ]);
    }

    /**
     * @Route("/email")
     */
    public function send(MailerInterface $mailer, TexterInterface $texter, DevicesRepository $repoDevices)
    {
        $object = 'Time for Symfony Mailer!';
        $email = (new Email())
            //->from('mysecurityclever@gmail.com')
            ->to('alhadoumpascal@gmail.com')
            //->addTo('cabrelmbakam@gmail.com')
            //->addTo('naomidinamona@gmail.com')
            //->cc('cabrelmbakam@gmail.com')
            //->bcc('bcc@example.com')
            //->replyTo('fabien@example.com')
            //->priority(Email::PRIORITY_HIGH)
            ->subject($object)
            ->text('message sent from the Symfony Web Apps My Security Clever!!')
            //->html('<p>See Twig integration for better HTML integration!</p>')
        ;

        $sms = new SmsMessage(
            // the phone number to send the SMS message to
            '+237690442311',
            // the message
            'message sent from the Symfony Web Apps My Security Clever!!'
        );

        $texter->send($sms);

        $mailer->send($email);

        return $this->render('mailer/index.html.twig', [
            'controller_name' => 'MailerController',
            'devicesNb'  => $this->getRepoDevice($repoDevices)['devicesNb'],
            'devicesTab' => $this->getRepoDevice($repoDevices)['devicesTab']
        ]);
        // ...
    }

    /**
     * @Route("/sms")
     */
    public function loginSuccess(TexterInterface $texter)
    {
        $sms = new SmsMessage(
            // the phone number to send the SMS message to
            '+237690442311',
            // the message
            'A new login was detected!'
        );

        $texter->send($sms);

        // ...
    }
}
