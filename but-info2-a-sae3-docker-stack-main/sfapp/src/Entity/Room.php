<?php

namespace App\Entity;

use App\Repository\RoomRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: RoomRepository::class)]
#[UniqueEntity(fields: "name", message: "Une salle avec ce nom existe déjà.")]
class Room
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 10, unique : true)]
    private ?string $name = null;

    #[ORM\Column]
    private ?int $floor = null;

    #[ORM\OneToOne(mappedBy: 'room', cascade: ['persist', 'remove'])]
    private ?AcquisitionSystem $acquisitionSystem = null;

    #[ORM\Column(length: 255)]
    private ?string $department = null;

    /**
     * Obtient l'identifiant de la salle.
     *
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Obtient le nom de la salle.
     *
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * Définit le nom de la salle.
     *
     * @param string $name
     * @return $this
     */
    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Obtient l'étage de la salle.
     *
     * @return int|null
     */
    public function getFloor(): ?int
    {
        return $this->floor;
    }

    /**
     * Définit l'étage de la salle.
     *
     * @param int $floor
     * @return $this
     */
    public function setFloor(int $floor): static
    {
        $this->floor = $floor;

        return $this;
    }

    /**
     * Obtient le système d'acquisition associé à la salle.
     *
     * @return AcquisitionSystem|null
     */
    public function getAcquisitionSystem(): ?AcquisitionSystem
    {
        return $this->acquisitionSystem;
    }

    /**
     * Définit le système d'acquisition associé à la salle.
     *
     * @param AcquisitionSystem|null $acquisitionSystem
     * @return $this
     */
    public function setAcquisitionSystem(?AcquisitionSystem $acquisitionSystem): static
    {
        // Désactive le côté propriétaire de la relation si nécessaire
        if ($acquisitionSystem === null && $this->acquisitionSystem !== null) {
            $this->acquisitionSystem->setRoom(null);
        }

        // Active le côté propriétaire de la relation si nécessaire
        if ($acquisitionSystem !== null && $acquisitionSystem->getRoom() !== $this) {
            $acquisitionSystem->setRoom($this);
        }

        $this->acquisitionSystem = $acquisitionSystem;

        return $this;
    }

    public function getDepartment(): ?string
    {
        return $this->department;
    }

    public function setDepartment(string $department): static
    {
        $this->department = $department;

        return $this;
    }
}
