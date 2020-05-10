<?php

namespace App\Controller;

use App\Repository\DevicesRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CamerasController extends ApplicationController
{
    /**
     * @Route("/cameras/videos/streaming", name="video_cameras_streaming")
     */
    public function index(DevicesRepository $repoDevices)
    {
        return $this->render('cameras/index_cameras.html.twig', [
            'devicesNb'  => $this->getRepoDevice($repoDevices)['devicesNb'],
            'devicesTab' => $this->getRepoDevice($repoDevices)['devicesTab']
        ]);
    }
}
