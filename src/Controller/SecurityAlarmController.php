<?php

namespace App\Controller;

use stdClass;
use App\Repository\UserRepository;
use App\Repository\DevicesRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\SecuritySystemRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Notifier\TexterInterface;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class SecurityAlarmController extends ApplicationController
{
    /**
     * @Route("/security/alarm", name="security_alarm")
     * 
     */
    public function index(
        Request $request,
        EntityManagerInterface $manager,
        DevicesRepository $devicesRepo,
        UserRepository $userRepo,
        SecuritySystemRepository $securitySystemRepo,
        MailerInterface $mailer,
        TexterInterface $texter
    ) { // {modId<[a-zA-Z0-9]+>}  , methods={"GET","POST"}, schemes={"http"}

        /*return $this->json([
            'code' => 200,
            //'received' => $paramJSON

        ], 200);*/
        //Récupération et vérification des paramètres au format JSON contenu dans la requête
        $paramJSON = $this->getJSONRequest($request->getContent());

        /*
        $devicesRepo        = $manager->getRepository(DevicesRepository::class);
        $userRepo           = $manager->getRepository(UserRepository::class);
        $securitySystemRepo = $manager->getRepository(SecuritySystemRepository::class);
        */

        //Recherche du device dans la BDD
        $modId  = $paramJSON['moduleId'];
        $device = $devicesRepo->findOneBy(['moduleId' => $modId]);

        //dump($device);
        $adminUser = [];
        $tabIP     = [];
        $tabResp   = [];

        if ($device != null) { // Test si le device existe dans notre BDD
            //1- On récupère l'état d'activation, le type d'alerte et le message de notification du device 
            $deviceState        = $device->getActivation();
            $notifMessage       = $device->getNotificationMessage();
            $deviceAlertType    = $device->getAlerte();

            //2- On récupère l'état d'activation du système de sécurity
            $tabSecuritySystem   = $securitySystemRepo->findAll();
            //dump($tabSecuritySystem);
            //die();
            $securitySystem      = $tabSecuritySystem[0];
            $securitySystemState = $securitySystem->getActivation();

            //3-3 On récupère les numéro de téléphone et adresses mail des user de rôle ADMIN
            $_user = $userRepo->findAll();
            foreach ($_user as $user) {
                //$roles = $user->getRoles()[0];
                if ($user->getRoles()[0] == 'ROLE_ADMIN') {
                    $adminUser[] = $user;
                }
                //dump($roles);
            }
            //dump($adminUser);
            if ($paramJSON['detect'] == 1) {
                $object = 'Alerte de sécurité';
                if ($device->getType() == 'Sensor') { //Si le device est de type Sensor

                    //3- On vérifie si le système de sécurité et le device sont armés
                    if ($deviceState && $securitySystemState) {
                        //3-1 On récupère les adresses IP des devices de type alarme et dont le champ alerte est soit "All Default"
                        //soit du même type d'alerte que le device ----
                        $alarmDevice = $devicesRepo->findBy(['type' => 'Alarm', 'alerte' => $deviceAlertType]);

                        //3-2 On envoie à chacune de ces adresses IP un message de retentissement ----
                        $port = 80;
                        foreach ($alarmDevice as $alarm) {
                            // code...
                            $ipAddress = $alarm->getIpaddress();
                            if (!empty($ipAddress)) {
                                $response = $this->forward('App\Controller\AlarmController::alarmSound', [
                                    'ipAddress'  => $ipAddress,
                                    'port'       => $port,
                                    'on'         => 1
                                ]);
                                $tabResp[] = $response;
                                $tabIP[]   = $ipAddress;
                            }
                        }

                        //3-4 On les envoi par mail(Objet: Alerte de sécurité) et sms à chacun d'eux le message de notification du device
                        if (!empty($notifMessage)) {
                            foreach ($adminUser as $user) {
                                // Envoi de mail
                                //$this->sendEmail($mailer, $user->getEmail(), $object, $notifMessage);

                                // Envoi de SMS
                                //$this->sendSMS($texter, $user->getFullPhone(), $notifMessage);
                            }
                        }
                    } else { //Si le système de sécurité du device n'est pas activé
                        return $this->json([
                            'code'          => 200,
                            'Error message' => 'device security is not activated',
                            'received'      => $paramJSON

                        ], 200);
                    }
                } else if ($device->getType() == 'Emergency') {

                    // On les envoi par mail(Objet: Alerte de sécurité) et sms à chacun d'eux le message de notification du device
                    if (!empty($notifMessage)) {
                        foreach ($adminUser as $user) {
                            // Envoi de mail
                            //$this->sendEmail($mailer, $user->getEmail(), $object, $notifMessage);

                            // Envoi de SMS
                            //$this->sendSMS($texter, $user->getFullPhone(), $notifMessage);
                        }
                    }
                }
            } else {
                return $this->json([
                    'code'          => 200,
                    'Error message' => 'param is incorret',
                    'received'      => $paramJSON,

                ], 200);
            }
            return $this->json([
                'code'     => 200,
                'received' => $paramJSON,
                'tabIP'    => $tabIP,
                'Response' => $tabResp

            ], 200);
            /*return $this->render("security_alarm/security_alarm.html.twig", [
                'device' => $device,
                'param'  => $paramJSON['detect']
            ]);*/
        } else if ($paramJSON['alarm'] == "stop") {
            $alarmDevice = $devicesRepo->findBy(['type' => 'Alarm']);

            // On envoie à chacune de ces adresses IP un message de retentissement ----
            $port = 80;
            foreach ($alarmDevice as $alarm) {
                // code...
                $ipAddress = $alarm->getIpaddress();
                if (!empty($ipAddress)) {
                    $response = $this->forward('App\Controller\AlarmController::alarmSound', [
                        'ipAddress'  => $ipAddress,
                        'port'       => $port,
                        'on'         => 0
                    ]);
                    $tabResp[] = $response;
                    $tabIP[]   = $ipAddress;
                }
            }
            return $this->json([
                'code'     => 200,
                'received' => $paramJSON,
                'tabIP'    => $tabIP,
                'Response' => $tabResp

            ], 200);
        }
        return $this->json([
            'code' => 403,
            'message' => "Device don't exist",
            'received' => $paramJSON

        ], 403);
    }

    /**
     * Mise à jour de l'état d'activation des équipements de sécurité dans la BDD
     * 
     * @Route("/security/state", name="security_state")
     *
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @param DevicesRepository $devicesRepo
     * @return void
     */
    public function securityState(Request $request, EntityManagerInterface $manager, DevicesRepository $devicesRepo, SecuritySystemRepository $houseSecurityRepo)
    {
        //Récupération et vérification des paramètres au format JSON contenu dans la requête
        $paramJSON = $this->getJSONRequest($request->getContent());

        $modId   = $paramJSON['moduleId'];
        $state = $paramJSON['state'] ? true : false;

        $houseSecurity = $houseSecurityRepo->findOneBy(['id' => 1]);

        if ($modId == "house") {
            if ($houseSecurity->getActivation() != $state) {
                $houseSecurity->setActivation($state);

                $manager->persist($houseSecurity);
                $manager->flush();

                return $this->json([
                    'code' => 200,
                    'received' => $paramJSON

                ], 200);
                /*return $this->render("security_alarm/security_alarm.html.twig", [
                    'device' => $device
                ]);*/
            } else {
                return $this->json([
                    'code' => 200,
                    'message' => 'the state is already update',
                    'received' => $paramJSON

                ], 200);
            }
        } else {
            //Recherche du device dans la BDD
            $device = $devicesRepo->findOneBy(['moduleId' => $modId]);

            dump($device);
            if ($device != null) { // Test si le device existe dans notre BDD

                if ($device->getActivation() != $state) {
                    $device->setActivation($state);

                    $manager->persist($device);
                    $manager->flush();

                    return $this->json([
                        'code' => 200,
                        'received' => $paramJSON

                    ], 200);
                    /*return $this->render("security_alarm/security_alarm.html.twig", [
                    'device' => $device
                ]);*/
                } else {
                    return $this->json([
                        'code' => 200,
                        'message' => 'the state is already update',
                        'received' => $paramJSON

                    ], 200);
                }
            }
        }
        return $this->json([
            'code' => 403,
            'message' => "Device don't exist",
            'modId'   => $modId,
            'received' => $paramJSON

        ], 403);
        /*return $this->render('accueil/index.html.twig', [
            'controller_name' => 'AccueilController',
        ]);*/
    }

    /**
     * Permet la mise à jour des états des boutons d'activation des devices de sécurité
     * 
     * @Route("/security/update/button/state", name="security_update_buttton")
     *
     * @param Request $request
     * @param UserRepository $userRepo
     * @param DevicesRepository $deviceRepo
     * @param SecuritySystemRepository $houseSecurityRepo
     * @return Json
     */
    public function getSecurityState(Request $request, UserRepository $userRepo, DevicesRepository $deviceRepo, SecuritySystemRepository $houseSecurityRepo)
    {
        //Récupération et vérification des paramètres au format JSON contenu dans la requête
        $paramJSON = $this->getJSONRequest($request->getContent());

        $userId   = $paramJSON['userId'];
        $userMail   = $paramJSON['userMail'];
        $user     = $userRepo->findOneBy(['id' => $userId, 'email' => $userMail]);
        if ($user != null) {
            $houseSecurity = $houseSecurityRepo->findOneBy(['id' => 1]);
            $motionDevices = $deviceRepo->findBy(['type' => 'Sensor', 'alerte' => 'Intrusion']);
            $fireDevices   = $deviceRepo->findBy(['type' => 'Sensor', 'alerte' => 'Fire']);
            $floodDevices  = $deviceRepo->findBy(['type' => 'Sensor', 'alerte' => 'Flood']);


            $myObj = new stdClass();
            $str  = 'swicth-house-lock';
            $myObj->$str = "John";
            $myObj->age = 30;
            $myObj->city = "New York";

            $myJSON = json_encode($myObj);

            $json = '{"switch-house-lock":' . ($houseSecurity->getActivation() ? 1 : 0);
            $str  = '';
            foreach ($motionDevices as $motion) {
                $str  = ',"switch-motion-' . $motion->getModuleId() . '":' . ($motion->getActivation() ? 1 : 0);
                $json = $json . $str;
            }
            foreach ($fireDevices as $fire) {
                $str  = ',"switch-fire-' . $fire->getModuleId() . '":' . ($fire->getActivation() ? 1 : 0);
                $json = $json . $str;
            }
            foreach ($floodDevices as $flood) {
                $str  = ',"switch-flood-' . $flood->getModuleId() . '":' . ($flood->getActivation() ? 1 : 0);
                $json = $json . $str;
            }

            $json = $json . '}';

            return $this->json([
                'data' => $json,
                'json' => $myJSON
            ], 200);
        }

        return $this->json([
            'code' => 403,
            'user' => "User don't exist",
            'received' => $paramJSON

        ], 403);
    }
}
