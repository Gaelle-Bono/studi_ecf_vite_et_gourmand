<?php
namespace App\Service;

use App\Entity\User;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Psr\Log\LoggerInterface;

class MailService
{
    public function __construct(private MailerInterface $mailer,private LoggerInterface $logger)
    {
        $this->mailer = $mailer;
        $this->logger = $logger;
    }

    public function sendMail(User $user, string $subject, string $template, array $context = [])
    {

        $email = (new TemplatedEmail())
            ->from('noreply@vite-et-gourmand.com')
            ->to($user->getEmail())
            ->subject($subject)
            ->htmlTemplate($template)
            ->context($context);
        try 
        {
            $this->mailer->send($email);
        } catch (\Exception $e) {
            $this->logger->error('Erreur envoi mail',[
                'email' => $user->getEmail(), 
                'error' => $e->getMessage()
            ]);
        }    
    }
}