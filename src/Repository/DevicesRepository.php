<?php

namespace App\Repository;

use App\Entity\Devices;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Devices|null find($id, $lockMode = null, $lockVersion = null)
 * @method Devices|null findOneBy(array $criteria, array $orderBy = null)
 * @method Devices[]    findAll()
 * @method Devices[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DevicesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Devices::class);
    }

    // /**
    //  * @return Devices[] Returns an array of Devices objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('d.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Devices
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    /**
     * Numbers of Camera
     *
     * @return integer|null
     */
    public function getNumberOfCamera(): ?int
    {
        return count($this->findBy(['type' => 'Camera']));
    }

    public function getCameraDevice()
    {
        return $this->findBy(['type' => 'Camera']);
    }

    /**
     * Number of Motion Sensor Device
     *
     * @return integer|null
     */
    public function getNumberOfMotionSensor(): ?int
    {
        return count($this->findBy(['type' => 'Sensor', 'alerte' => 'Intrusion']));
    }

    public function getMotionSensorDevice()
    {
        return $this->findBy(['type' => 'Sensor', 'alerte' => 'Intrusion']);
    }

    /**
     * Number of Fire Device Sensor 
     *
     * @return integer|null
     */
    public function getNumberOfFireSensor(): ?int
    {
        return count($this->findBy(['type' => 'Sensor', 'alerte' => 'Fire']));
    }

    public function getFireSensorDevice()
    {
        return $this->findBy(['type' => 'Sensor', 'alerte' => 'Fire']);
    }

    /**
     * Number of Flood Device Senesor
     *
     * @return integer|null
     */
    public function getNumberOfFloodSensor(): ?int
    {
        return count($this->findBy(['type' => 'Sensor', 'alerte' => 'Flood']));
    }

    public function getFloodSensorDevice()
    {
        return $this->findBy(['type' => 'Sensor', 'alerte' => 'Flood']);
    }

    /**
     * Number of Alarm Devices 
     *
     * @return integer|null
     */
    public function getNumberOfAlarm(): ?int
    {
        return count($this->findBy(['type' => 'Alarm']));
    }

    public function getAlarmDevice()
    {
        return $this->findBy(['type' => 'Alarm']);
    }

    public function getNumberOfEmergencyBtn(): ?int
    {
        return count($this->findBy(['type' => 'Emergency']));
    }

    public function getEmergencyBtnDevice()
    {
        return $this->findBy(['type' => 'Emergency']);
    }

    public function getNumberOfEachDevice(): ?array
    {

        $tab = [];
        $tab['NbCamera']       = $this->getNumberOfCamera();
        $tab['NbMotionSensor'] = $this->getNumberOfMotionSensor();
        $tab['NbFireSensor']   = $this->getNumberOfFireSensor();
        $tab['NbFloodSensor']  = $this->getNumberOfFloodSensor();
        $tab['NbAlarm']        = $this->getNumberOfAlarm();
        $tab['NbEmergency']    = $this->getNumberOfEmergencyBtn();

        return $tab;
    }

    public function getDevices(): ?array
    {

        $tabDevices = [];

        $tabDevices['Camera'] = $this->getCameraDevice();
        $tabDevices['Motion'] = $this->getMotionSensorDevice();
        $tabDevices['Fire']   = $this->getFireSensorDevice();
        $tabDevices['Flood']  = $this->getFloodSensorDevice();
        $tabDevices['Alarm']  = $this->getAlarmDevice();
        $tabDevices['Emergency'] = $this->getEmergencyBtnDevice();

        return $tabDevices;
    }
}
