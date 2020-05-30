<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Devices;
use App\Form\DevicesType;
use App\Repository\DevicesRepository;
use Cocur\Slugify\Slugify;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Contrôleur des Devices
 * 
 */
class DevicesController extends ApplicationController
{
    /**
     * @Route("/devices", name="devices")
     */
    public function index()
    {
        return $this->render('devices/index.html.twig', [
            'controller_name' => 'DevicesController',
        ]);
    }

    /**
     * Permet de créer un nouvel équipement
     *
     * @Route("/admin/devices/new", name = "super_admin_devices_create")
     * @IsGranted("ROLE_SUPER_ADMIN")
     * 
     * @return Response
     */
    public function create(Request $request, EntityManagerInterface $manager, DevicesRepository $repoDevices)
    {
        $device = new Devices();

        //Permet d'obtenir un constructeur de formulaire
        // Externaliser la création du formulaire avec la cmd php bin/console make:form

        //  instancier un form externe
        $form = $this->createForm(DevicesType::class, $device);
        $form->handleRequest($request);
        //dump($site);

        if ($form->isSubmitted() && $form->isValid()) {

            //$manager = $this->getDoctrine()->getManager();
            $manager->persist($device);
            $manager->flush();

            $this->addFlash(
                'success',
                "L'équipement <strong>{$device->getName()}</strong> a bien été créé !"
            );

            return $this->redirectToRoute('super_admin_settings', [
                'page'     => 1,
                'tabpanel' => 1
            ]);
        }


        return $this->render('devices/new.html.twig', [
            'form' => $form->createView(),
            'user' => $this->getUser(),
            'devicesNb'  => $this->getRepoDevice($repoDevices)['devicesNb'],
            'devicesTab' => $this->getRepoDevice($repoDevices)['devicesTab']
        ]);
    }

    /**
     * Permet d'afficher le formulaire d'édition d'un équipement
     *
     * @Route("/admin/devices/{id}/edit", name="super_admin_devices_edit")
     * @IsGranted("ROLE_SUPER_ADMIN")
     *
     * 
     * @return Response
     */
    public function edit(Devices $device, Request $request, EntityManagerInterface $manager, DevicesRepository $repoDevices)
    {
        dump($device);
        //  instancier un form externe
        $form = $this->createForm(DevicesType::class, $device);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $slugify = new Slugify();
            $device->setSlug($slugify->slugify($device->getName()));
            //$manager = $this->getDoctrine()->getManager();
            $manager->persist($device);
            $manager->flush();

            $this->addFlash(
                'success',
                "Les modifications de l'équipement <strong>{$device->getName()}</strong> ont  bien été enregistrées !"
            );

            return $this->redirectToRoute('super_admin_settings', [
                'page'     => 1,
                'tabpanel' => 1
            ]);
        }

        return $this->render('devices/edit.html.twig', [
            'form' => $form->createView(),
            'device' => $device,
            'devicesNb'  => $this->getRepoDevice($repoDevices)['devicesNb'],
            'devicesTab' => $this->getRepoDevice($repoDevices)['devicesTab']
        ]);
    }

    /**
     * Permet de supprimer un équipement
     * 
     * @Route("/admin/devices/{id}/delete", name="super_admin_devices_delete")
     * @IsGranted("ROLE_SUPER_ADMIN")
     *
     * @param Devices $device
     * @param EntityManagerInterface $manager
     * @return void
     */
    public function delete(Devices $device, EntityManagerInterface $manager)
    {
        $name = $device->getName();
        $manager->remove($device);
        $manager->flush();

        $this->addFlash(
            'success',
            "La suppression de l'équipement <strong>{$name}</strong> a été effectuées avec succès !"
        );

        return $this->redirectToRoute('super_admin_settings', [
            'page'     => 1,
            'tabpanel' => 1
        ]);
    }

    /**
     * Gère les requêtes de modification des adresses IP des devices
     * 
     * @Route("/devices/ipAddress", name="devices_set_ipAddress")
     *
     * @return Request
     */
    public function devicesIP(Request $request, EntityManagerInterface $manager, DevicesRepository $devicesRepo)
    {
        //Récupération et vérification des paramètres au format JSON contenu dans la requête
        $paramJSON = $this->getJSONRequest($request->getContent());

        $modId   = $paramJSON['moduleId'];
        $ipAddress = $paramJSON['IPaddress'];

        //$devicesRepo = $manager->getRepository(DevicesRepository::class);

        //Recherche du device dans la BDD
        $device = $devicesRepo->findOneBy(['moduleId' => $modId]);

        dump($device);
        if ($device != null) { // Test si le device existe dans notre BDD

            $device->setIpaddress($ipAddress);

            $manager->persist($device);
            $manager->flush();


            return $this->json([
                'code' => 200,
                'received' => $paramJSON

            ], 200);
            /*return $this->render("security_alarm/security_alarm.html.twig", [
                'device' => $device
            ]);*/
        }
        return $this->json([
            'code' => 403,
            'message' => "Device don't exist",
            'received' => $paramJSON

        ], 403);
    }

    /**
     * Gère les requêtes de connectivité des devices
     * 
     * @Route("/devices/connection", name="devices_connection")
     *
     * @return Request
     */
    public function devicesConnection(Request $request, EntityManagerInterface $manager)
    {
        //Récupération et vérification des paramètres au format JSON contenu dans la requête
        $paramJSON = $this->getJSONRequest($request->getContent());

        $modId   = $paramJSON['moduleId'];
        $connect = $paramJSON['connect'] ? true : false;

        $devicesRepo = $manager->getRepository(DevicesRepository::class);

        //Recherche du device dans la BDD
        $device = $devicesRepo->findOneBy(['moduleId' => $modId]);

        dump($device);
        if ($device != null) { // Test si le device existe dans notre BDD

            if ($device->getConnectionState() == false) {
                $device->setConnectionState(true);

                $manager->persist($device);
                $manager->flush();

                //Mise à jour des interfaces des clients connectés par "websocket"


                /*return $this->json([
                    'code' => 200,
                    'received' => $paramJSON
    
                ], 200);*/
                return $this->render("security_alarm/security_alarm.html.twig", [
                    'device' => $device
                ]);
            }
        }
        /*return $this->json([
            'code' => 403,
            'message' => "Device don't exist",
            'received' => $paramJSON

        ], 403);*/
    }
}
