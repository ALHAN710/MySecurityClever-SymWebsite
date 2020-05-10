<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class MotionSensorsController extends AbstractController
{
    /**
     * @Route("/motion/sensors", name="motion_sensors_index")
     */
    public function index()
    {
        return $this->render('motion_sensors/motion_sensors_index.html.twig', [
            'controller_name' => 'MotionSensorController',
        ]);
    }
}
