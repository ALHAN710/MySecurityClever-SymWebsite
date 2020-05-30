<?php

namespace App\DataFixtures;

use App\Entity\Devices;
use DateTime;
use Faker\Factory;
use App\Entity\Role;
use App\Entity\SecuritySystem;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private $encoder;

    //Constructeur pour utiliser la fonction d'encodage de mot passe
    //encodePassword($entity, $password)
    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('fr_FR');

        $superAdminRole = new Role();
        $superAdminRole->setTitre('ROLE_SUPER_ADMIN');
        $manager->persist($superAdminRole);

        $adminRole = new Role();
        $adminRole->setTitre('ROLE_ADMIN');
        $manager->persist($adminRole);

        $genders = ['male', 'female'];

        $adminUser1 = new User();
        $adminUser2 = new User();
        $adminUser3 = new User();
        $date = new DateTime(date('Y-m-d H:i:s'));
        $adminUser1->setFirstName('Pascal')
            ->setLastName('ALHADOUM')
            ->setEmail('alhadoumpascal@gmail.com')
            ->setHash($this->encoder->encodePassword($adminUser1, 'password'))
            ->setCountryCode('+237')
            ->setPhoneNumber('690442311')
            ->setCreatedAt($date)
            ->setVerified(true)
            ->addUserRole($superAdminRole);

        $manager->persist($adminUser1);

        $date = new DateTime(date('Y-m-d H:i:s'));
        $adminUser2->setFirstName('Cabrel')
            ->setLastName('MBAKAM')
            ->setEmail('cabrelmbakam@gmail.com')
            ->setHash($this->encoder->encodePassword($adminUser2, 'password'))
            ->setCountryCode('+237')
            ->setPhoneNumber('690304593')
            ->setCreatedAt($date)
            ->setVerified(true)
            ->addUserRole($superAdminRole);

        $manager->persist($adminUser2);

        $date = new DateTime(date('Y-m-d H:i:s'));
        $adminUser3->setFirstName('Naomi')
            ->setLastName('DINAMONA')
            ->setEmail('dinamonanaomi@gmail.com')
            ->setHash($this->encoder->encodePassword($adminUser3, 'password'))
            ->setCountryCode('+237')
            ->setPhoneNumber('690304593')
            ->setCreatedAt($date)
            ->setVerified(true)
            ->addUserRole($adminRole);

        $manager->persist($adminUser3);

        /*$RoleUser = new Role();
        $RoleUser->setTitre('ROLE_USER');
        $manager->persist($RoleUser);*/


        //Nous gérons les utilisateurs
        $users = [];

        for ($i = 1; $i <= 3; $i++) {
            $user = new User();

            $hash = $this->encoder->encodePassword($user, 'password');
            $date = new DateTime(date('Y-m-d H:i:s'));

            $user->setFirstName($faker->unique()->firstName($faker->randomElement($genders)))
                ->setLastName($faker->unique()->lastName)
                ->setEmail($faker->unique()->companyEmail)
                ->setCreatedAt($date)
                ->setHash($hash)
                ->setVerified(true)
                ->setPhoneNumber($faker->phoneNumber)
                ->setCountryCode('+237');
            //->addUserRole($RoleUser);

            $manager->persist($user);
            $users[] = $user;
        }

        //Nous gérons les équipementss
        $devicesTypes = ['Camera', 'Sensor', 'Alarm', 'Emergency'];
        $alerteTypes  = ['Intrusion', 'Fire', 'Flood', 'Opening'];
        $names        = ['Salon', 'Boutique Bali', 'Garage', 'Jardin', 'Cuisine', 'Chambre bb', 'Magasin'];
        foreach ($devicesTypes as $deviceType) {

            for ($i = 1; $i <= 2; $i++) {
                $device = new Devices();
                if ($deviceType != 'Sensor') {

                    $device->setModuleId('' . $faker->unique()->randomNumber($nbDigits = 8, $strict = false))
                        ->setType($deviceType)
                        ->setName($faker->randomElement($names));

                    if ($device->getType() != 'Camera' && $device->getType() != 'Alarm' && $device->getType() != 'Emergency') {
                        $device->setAlerte($faker->randomElement($alerteTypes));
                    } else if ($device->getType() == 'Camera') {
                        $device->setStreamingUrl($faker->unique()->url)
                            ->setNotificationMessage('Alerte Intrusion dans ' . $device->getName());
                    }
                    $manager->persist($device);
                }
            }
        }

        foreach ($alerteTypes as $alerteType) {
            for ($i = 1; $i <= 2; $i++) {
                $device = new Devices();

                $device->setModuleId('' . $faker->unique()->randomNumber($nbDigits = 8, $strict = false))
                    ->setType('Sensor')
                    ->setName($faker->randomElement($names))
                    ->setAlerte($alerteType);

                if ($device->getAlerte() == 'Intrusion') {
                    $device->setNotificationMessage('Alerte Intrusion dans la pièce suivante : ' . $device->getName());
                } else if ($device->getAlerte() == 'Fire') {
                    $device->setNotificationMessage("URGENT URGENT URGENT !!!" . "\r\n" . "Alerte Incendie dans la pièce suivante : " . $device->getName());
                } else if ($device->getAlerte() == 'Flood') {
                    $device->setNotificationMessage("URGENT URGENT URGENT !!!" . "\r\n" . "Alerte Inondation dans la pièce suivante : " . $device->getName());
                } else if ($device->getAlerte() == 'Opening') {
                    $device->setNotificationMessage('URGENT URGENT URGENT !!!\nAlerte Effraction de l\'ouverture suivante : ' . $device->getName());
                }

                $device->setActivation(true);

                $manager->persist($device);
            }
        }

        //Gestion du système de sécurité 
        $securitySystem = new SecuritySystem();
        $securitySystem->setActivation(true);
        $manager->persist($securitySystem);

        $manager->flush();
    }
}
