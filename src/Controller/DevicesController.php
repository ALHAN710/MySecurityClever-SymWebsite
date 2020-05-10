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
     * @IsGranted("ROLE_ADMIN")
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
     * @Security("is_granted('ROLE_ADMIN')", message = "Vous n'avez pas le droit d'accéder à cette ressource")
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
}
