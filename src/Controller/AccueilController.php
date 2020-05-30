<?php

namespace App\Controller;

use App\Repository\UserRepository;
use App\Repository\DevicesRepository;
use App\Repository\SecuritySystemRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class AccueilController extends ApplicationController
{
    /**
     * @Route("/accueil", name="accueil")
     */
    public function index()
    {
        return $this->render('accueil/index.html.twig', [
            'controller_name' => 'AccueilController',
        ]);
    }

    /**
     * Page d'accueil de l'application
     * 
     * @Route("/", name="home_page")
     * 
     */
    public function home(AuthenticationUtils $utils, SecuritySystemRepository $securityRepo, DevicesRepository $repoDevices, EntityManagerInterface $manager)
    {

        $user = $this->getUser();
        $error = $utils->getLastAuthenticationError();
        $username = $utils->getLastUsername();
        if ($user !== NULL) {
            //$devicesNb = $repoDevices->getNumberOfEachDevice();
            $devicesTab = $repoDevices->getDevices();
            //dump($devicesNb);
            //dump($devicesTab);
            //dump($securityRepo->findOneBy(['id' => 1]));
            //$userRepo = $manager->getRepository(UserRepository::class);
            //$userRepo = $this->getDoctrine()->getRepository(UserRepository::class);
            /*
            $_user = $userRepo->findAll();
            $adminUser = [];
            foreach ($_user as $user) {
                $roles = $user->getRoles();
                dump($roles);
                if ($user->getRoles()[0] == 'ROLE_ADMIN') {
                    $adminUser[] = $user;
                }
            }
            dump($adminUser);
            */
            return $this->render('accueil/home.html.twig', [
                'hasError'   => $error !== null,
                'username'   => $username,
                'devicesNb'  => $this->getRepoDevice($repoDevices)['devicesNb'],
                'devicesTab' => $this->getRepoDevice($repoDevices)['devicesTab'],
                'security'   => $securityRepo->findAll(),
            ]);
        } else {
            return $this->render('account/login.html.twig', [
                'hasError' => $error !== null,
                'username' => $username
            ]);
        }

        /*return $this->render('accueil/home.html.twig', [
            'hasError' => $error !== null,
            'username' => $username
        ]);*/
    }
}
