<?php

namespace App\Controller;

use App\Entity\Images;
use App\Entity\Logs;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\BinaryFileResponse;



final class ImagesGetterController extends AbstractController
{
    
    public function __construct(private EntityManagerInterface $entityManager){

    }
    #[Route('/image-test-display/{id}', name: 'app_image_getter')]
    public function imageGet(int $id): Images
    {
        $Image = $this->entityManager->getRepository(Images::class)->find($id);
       
        if (!$Image) {
            throw $this->createNotFoundException(
                'No product found for id '.$id
            );
        }
        // tell Doctrine you want to (eventually) save the Product (no queries yet)
      

        // actually executes the queries (i.e. the INSERT query)
        return $Image ;
        
    }

    #[Route('/image-download/{id}', name: 'app_image_download_middleware')]
    public function downloadLogs(int $id): Response
    {
        try{
            $image = $this->imageGet(id: $id);
            $image->setNbDownload($image->getNbDownload()+1);

            $date = new DateTime();
            $logs = new Logs();
            $logs ->setImages($image);
            $logs->setDate($date);

            $this->entityManager->persist($logs);
            $this->entityManager->persist($image);

            $this->entityManager->flush();

            $filePath = __DIR__ . '/../../public/images_chat/' . $image->getFilename() ; 
            $response = new BinaryFileResponse($filePath);
            $response->headers->set('Content-Disposition', 'inline');

            return $response ; 


        } catch (\Exception $e) {
            return new Response('Error', 500) ;
        }
    }
    #[Route('/image-display/{id}', name: 'app_image_display_middleware', requirements: ['id' => '.+'], methods: ['GET'])]
    public function displayImages(string $id): Response
    {
        
        try {
            
           


            $image = $this->imageGet(id: $id);
            
            $image->setNbOpened($image->getNbOpened()+1);
            $datetime = new DateTime(); 
            $image->setLastTimeViewed($datetime);
            $this->entityManager->persist($image);
            $this->entityManager->flush();

            error_log('Display : ' . $image->getFilename()) ; 
            $filePath = __DIR__ . '/../../public/images_chat/' . $image->getFilename() ; 
            $response = new BinaryFileResponse($filePath);
            $response->headers->set('Content-Disposition', 'inline');

            return $response ;

            
           
           
        }
        catch (\Exception $e) {
            return new Response(''. $e->getMessage(), 500);
        }
    }


}
