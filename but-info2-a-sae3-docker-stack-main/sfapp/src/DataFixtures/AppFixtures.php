<?php
// src/DataFixtures/AppFixtures.php

namespace App\DataFixtures;

use App\Entity\Room;
use App\Entity\AcquisitionSystem;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $rooms = ['D001', 'D002', 'D003', 'D004', 'D005'];

        foreach ($rooms as $roomName) {
            $room = new Room();
            $room->setName($roomName);
            $room->setFloor(0);
            $room->setDepartment('Informatique');
            $manager->persist($room);
        }

        $manager->flush();

        $allRooms = $manager->getRepository(Room::class)->findAll();

        foreach ($allRooms as $room) {
            // Ajout d'un système d'acquisition associé à chaque salle avec des données fictives
            $system = new AcquisitionSystem();
            $system->setName('SA-' . substr($room->getName(), -3));
            $system->setRoom($room);
            $system->setTemperature(mt_rand(15, 25));
            $system->setHumidity(mt_rand(30, 80));
            $system->setCo2(mt_rand(300, 1700));
            $system->setIsInstalled(1);
            $manager->persist($system);
            
            // Override default values to create alerts
            if ($room->getName() == 'D001') {
                $system->setTemperature(16);
            }

            if ($room->getName() == 'D003') {
                $system->setCo2(1400);
            }

            if ($room->getName() == 'D004') {
                $system->setHumidity(75);
                $system->setTemperature(21);
            }

            if ($room->getName() == 'D005') {
                $system->setIsInstalled(0);
            }
        }

        

        $admin = new User();
        $admin->setUsername('admin');
        $admin->setPassword('admin');
        $admin->setRoles(['ROLE_ADMIN']);
        $manager->persist($admin);

        $technician = new User();
        $technician->setUsername('tech');
        $technician->setPassword('tech');
        $technician->setRoles(['ROLE_TECHNICIAN']);
        $manager->persist($technician);

        $manager->flush();
    }
}
