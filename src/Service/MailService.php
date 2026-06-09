<?php

namespace App\Service;

use App\Entity\User;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Psr\Log\LoggerInterface;

class MailService
{
    public function __construct(private MailerInterface $mailer,private LoggerInterface $logger, private string $mailFrom)
    {
    }

    public function sendMail(User $user, string $subject, string $template, array $context = []):bool
    {

        $email = (new TemplatedEmail())
            ->from($this->mailFrom)
            ->to($user->getEmail())
            ->subject($subject)
            ->htmlTemplate($template)
            ->context($context);
        try 
        {
            $this->mailer->send($email);
            return true;
        } catch (\Throwable $e) {
            $this->logger->error('Erreur envoi mail',[
                'email' => $user->getEmail(), 
                'error' => $e->getMessage()
                
            ]);
            return false;
        }    
    }
}