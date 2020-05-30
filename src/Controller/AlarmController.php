<?php

namespace App\Controller;

use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

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

    /**
     * Permet d'envoyer les requêtes aux devices de type Alarme
     * 
     *
     * @param [string] $ipAddress
     * @param [string] $port
     * @return void
     */
    public function alarmSound($ipAddress, $port, $on)
    {
        $url    = [];
        $url[0] = "http://" . $ipAddress . ":" . $port . "/ring?sound={'exp': 'central','cmd': 'stop'}";
        $url[1] = "http://" . $ipAddress . ":" . $port . "/ring?sound={'exp': 'central','cmd': 'start'}";

        $client = HttpClient::create();
        //$response = $client->request('GET', 'https://api.github.com/repos/symfony/symfony-docs');
        $response = $client->request('GET', $url[$on]);

        $statusCode = $response->getStatusCode();
        // $statusCode = 200
        $contentType = $response->getHeaders()['content-type'][0];
        // $contentType = 'application/json'
        $content = $response->getContent();
        // $content = '{"id":521583, "name":"symfony-docs", ...}'
        $content = $response->toArray();
        // $content = ['id' => 521583, 'name' => 'symfony-docs', ...]

        return $response;
        //return $this->redirect($url, 301);
        //return $this->redirect('http://stackoverflow.com', 301); - for changing HTTP status code from 302 Found to 301 Moved Permanently

    }
}
