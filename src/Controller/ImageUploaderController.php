<?php

namespace App\Controller;

use DateTime;
use DateTimeZone;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\Routing\Attribute\Route;

final class ImageUploaderController extends AbstractController
{

    #[Route('/upload/images', name: 'image_upload', methods: ['POST'])]
    public function upload(Request $request): Response
    {

        $image = $request->files->get('image'); // Récupérer le fichier
        $name = $image->getClientOriginalName();

        if ($image) {

            $destination = $this->getParameter('kernel.project_dir') . '/public/images';
            $newFilename = $name . "_" . uniqid() . '.' . $image->guessExtension();
            $image->move($destination, $newFilename);

            $client = HttpClient::create();
            $response = $client->request('POST', 'http://127.0.0.1:8002/images', [
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    "url" => "http://127.0.0.1:8002/images/" . $newFilename,
                    "name" => $name,
                    "nb_download" => 0,
                    "nb_opened" => 0,
                    "last_time_viewed" => new DateTime(), // Date actuelle
                    "is_deleted" => false,
                    "creation_date" => new DateTime(), // Date actuelle
                    "logs" => []
                ],
            ]);


            return new Response('Image downloaded successfully', Response::HTTP_OK);
        }

        return new Response('No image uploaded in api', Response::HTTP_BAD_REQUEST);
    }



}
