<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\Routing\Attribute\Route;

final class ImageUploaderController extends AbstractController
{
    
    #[Route('/upload/images', name: 'image_upload', methods: ['POST'])]
    public function upload(Request $request): Response
    {
        
        $image = $request->files->get('image'); // Récupérer le fichier
        
        if ($image) {

            $destination = $this->getParameter('kernel.project_dir') . '/public/images';
            $newFilename = uniqid() . '.' . $image->guessExtension();
            $image->move($destination, $newFilename);

            return new Response('Image downloaded successfully', Response::HTTP_OK);

        }
        
        return new Response('No image uploaded in api', Response::HTTP_BAD_REQUEST);
    }
    

    
}
