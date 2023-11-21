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
            $manager->persist($room);
        }

        $manager->flush();

        $allRooms = $manager->getRepository(Room::class)->findAll();

        foreach ($allRooms as $room) {
            // Ajout d'un système d'acquisition associé à chaque salle avec des données fictives
            $system = new AcquisitionSystem();
            $system->setName('SA-' . substr($room->getName(), -3));
            $system->setRoom($room);
            $system->setTemperature(mt_rand(18, 25));
            $system->setHumidity(mt_rand(30, 70));
            $system->setCo2(mt_rand(300, 800));
            $manager->persist($system);
        }

        $admin = new User();
        $admin->setUsername('admin');
        $admin->setPassword('admin');
        $admin->setRoles(['ROLE_ADMIN']);
        $manager->persist($admin);

        $manager->flush();
    }
}
