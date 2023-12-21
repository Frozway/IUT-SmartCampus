<?php
// src/DataFixtures/AppFixtures.php

namespace App\DataFixtures;

use App\Entity\Room;
use App\Entity\AcquisitionSystem;
use App\Entity\User;
use App\Entity\Department;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        // Création des départements
        $departmentNames = ['Informatique', 'Réseaux et Télécommunications', 'Génie Biologique', 'Génie Civile', 'Techniques de commercialisation'];

        foreach ($departmentNames as $departmentName) {
            $department = new Department();
            $department->setName($departmentName);
            $manager->persist($department);
        }

        $manager->flush();

        // Récupération des départements
        $departments = $manager->getRepository(Department::class)->findAll();

        // Création des salles et association avec les départements
        $rooms = ['D001', 'D002', 'D003', 'D004', 'D005'];

        foreach ($rooms as $key => $roomName) {
            $room = new Room();
            $room->setName($roomName);
            $room->setFloor(0);
            $room->setDepartment($departments[$key]);
            $manager->persist($room);
        }

        $manager->flush();

        // Récupération de toutes les salles
        $allRooms = $manager->getRepository(Room::class)->findAll();

        // Création des systèmes d'acquisition associés à chaque salle avec des données fictives
        foreach ($allRooms as $room) {
            $system = new AcquisitionSystem();
            $system->setName('SA-' . substr($room->getName(), -3));
            $system->setRoom($room);
            $system->setTemperature(mt_rand(15, 25));
            $system->setHumidity(mt_rand(30, 80));
            $system->setCo2(mt_rand(300, 1700));
            $system->setIsInstalled(mt_rand(0, 1));
            $system->setState(mt_rand(0, 2));
            $manager->persist($system);

            // Override des valeurs par défaut pour créer des alertes
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

        // Création des utilisateurs
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
