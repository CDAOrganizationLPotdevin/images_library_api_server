<?php

// src/Command/LcEmailCommand.php
namespace App\Command;

use App\Repository\ImagesRepository;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

#[AsCommand(
    name: 'lc:email',
    description: 'Envoie automatiquement les 20 images les plus téléchargées de la galerie des chats par email.'
)]
class LcEmailCommand extends Command
{
    public function __construct(
        private ImagesRepository $imagesRepository,
        private MailerInterface $mailer,
        private ParameterBagInterface $params
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $images = $this->imagesRepository->findTopDownloadedImages(20);
        $recipient = $this->params->get('top_images_recipient_email');
        $senderEmail = $this->params->get('top_email_sender'); 


        $email = (new TemplatedEmail())
            ->from($senderEmail)
            ->to($recipient)
            ->subject('Top 20 images les plus téléchargées de la galerie des chats')
            ->htmlTemplate('emails/top_images.html.twig')
            ->context([
                'images' => $images,
            ]);

        $this->mailer->send($email);

        $output->writeln("Email envoyé à $recipient avec les 20 images de chats les plus téléchargées.");

        return Command::SUCCESS;
    }
}

