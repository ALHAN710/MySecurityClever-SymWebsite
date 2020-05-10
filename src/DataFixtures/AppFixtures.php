<?php

namespace App\DataFixtures;

use App\Entity\Devices;
use DateTime;
use Faker\Factory;
use App\Entity\Role;
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

        $adminRole = new Role();
        $adminRole->setTitre('ROLE_ADMIN');
        $manager->persist($adminRole);

        $genders = ['male', 'female'];

        $adminUser1 = new User();
        $adminUser2 = new User();
        $date = new DateTime(date('Y-m-d H:i:s'));
        $adminUser1->setFirstName('Pascal')
            ->setLastName('ALHADOUM')
            ->setEmail('alhadoumpascal@gmail.com')
            ->setHash($this->encoder->encodePassword($adminUser1, 'password'))
            ->setCountryCode('+237')
            ->setPhoneNumber('690442311')
            ->setCreatedAt($date)
            ->setVerified(true)
            ->addUserRole($adminRole);

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
            ->addUserRole($adminRole);

        $manager->persist($adminUser2);

        $RoleUser = new Role();
        $RoleUser->setTitre('ROLE_USER');
        $manager->persist($RoleUser);


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
                ->setCountryCode('+237')
                ->addUserRole($RoleUser);

            $manager->persist($user);
            $users[] = $user;
        }

        //Nous gérons les équipementss
        $devicesTypes = ['Camera', 'Sensor', 'Alarm', 'Emergency'];
        $alerteTypes = ['Intrusion', 'Fire', 'Flood'];
        $names = ['Salon', 'Boutique Bali', 'Garage', 'Jardin', 'Cuisine', 'Chambre bb', 'Magasin'];

        for ($i = 1; $i <= 10; $i++) {
            $device = new Devices();

            $device->setModuleId('' . $faker->unique()->randomNumber($nbDigits = 8, $strict = false))
                ->setType($faker->randomElement($devicesTypes))
                ->setName($faker->randomElement($names));

            if ($device->getType() != 'Camera' && $device->getType() != 'Alarm') {
                $device->setAlerte($faker->randomElement($alerteTypes));
            } else if ($device->getType() == 'Camera') {
                $device->setStreamingUrl($faker->unique()->url)
                    ->setNotificationMessage('Alerte Intrusion dans ' . $device->getName());
            }


            $manager->persist($device);
        }

        $manager->flush();
    }
}
