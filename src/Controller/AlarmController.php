<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class AlarmController extends AbstractController
{
    /**
     * @Route("/alarm", name="alarm")
     */
    public function index()
    {
        return $this->render('alarm/index.html.twig', [
            'controller_name' => 'AlarmController',
        ]);
    }

    public function alarmSound($ipAddress, $port)
    {
        $url = 'http://' . $ipAddress . ':' . $port . '/?sound=1';
        return $this->redirect($url);
        //return $this->redirect('http://stackoverflow.com', 301); - for changing HTTP status code from 302 Found to 301 Moved Permanently

    }
}
