<?php

use PHPUnit\Framework\TestCase;
use App\Entity\AcquisitionSystem;
use App\Entity\Room;


class RoomUnitsTests extends TestCase
{
    public function testGetSetName()
    {
        $room = new Room();
        $room->setName('Salle 1');

        $this->assertEquals('Salle 1', $room->getName());
    }

    public function testGetSetFloor()
    {
        $room = new Room();
        $room->setFloor(1);

        $this->assertEquals(1, $room->getFloor());
    }

    public function testGetSetAcquisitionSystem()
    {
        $room = new Room();
        $acquisitionSystem = new AcquisitionSystem();
        $room->setAcquisitionSystem($acquisitionSystem);

        $this->assertEquals($acquisitionSystem, $room->getAcquisitionSystem());
    }
}