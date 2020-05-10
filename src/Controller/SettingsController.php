<?php

namespace App\Controller;

use App\Entity\Devices;
use App\Entity\User;
use App\Repository\DevicesRepository;
use App\Service\Pagination;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SettingsController extends ApplicationController
{
    /**
     * @Route("/admin/settings/{tabpanel<\d>?0}/{page<\d+>?1}", name="super_admin_settings")
     */
    public function index($page, $tabpanel, Pagination $pagination, DevicesRepository $repoDevices)
    {
        //Pagination du tableau des users par ordre de prénom croissant et du tableau des devices par ordre de nom croissant
        $pagination->setEntityClass([User::class, Devices::class])
            ->setCurrentPage([$page, $page])
            ->setTabPanel([0, 1])
            ->setLabelOrder(['firstName', 'name'])
            ->setOrder(['ASC', 'ASC']);


        dump($pagination);


        return $this->render('settings/index_settings.html.twig', [
            'pagination' => $pagination,
            'tabpanel'   => $tabpanel,
            'devicesNb'  => $this->getRepoDevice($repoDevices)['devicesNb'],
            'devicesTab' => $this->getRepoDevice($repoDevices)['devicesTab']
        ]);
    }
}
