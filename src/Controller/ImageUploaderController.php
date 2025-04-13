<?php

namespace App\Controller;

use App\Entity\Images;
use DateTime;
use DateTimeZone;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\Routing\Attribute\Route;

final class ImageUploaderController extends AbstractController
{
    public function __construct(private EntityManagerInterface $entityManager) {}

    // Méthode de creation d'une nouvelle image (ajout en base de données et envoie de l'image sur le dossier public/images_chat)
    #[Route('/upload/images', name: 'image_upload', methods: ['POST'])]
    public function upload(Request $request): Response
    {

        $image = $request->files->get('image'); // Récupérer le fichier
        $name = $image->getClientOriginalName();

        if ($image) {

            $destination = $this->getParameter('kernel.project_dir') . '/public/images_chat';
            $newFilename = $name . "_" . uniqid() . '.' . $image->guessExtension();
            $image->move($destination, $newFilename);

            $image = new Images();
            $image->setFilename($newFilename);
            $image->setName($name);
            $image->setNbDownload(0);
            $image->setNbOpened(0);
            $image->setLastTimeViewed(new DateTime());
            $image->setCreationDate(new DateTime());
            $image->setIsDeleted(false);

            $this->entityManager->persist($image);
            $this->entityManager->flush();

            return new Response('Image downloaded successfully', Response::HTTP_OK);
        }

        return new Response('No image uploaded in api', Response::HTTP_BAD_REQUEST);
    }



}
