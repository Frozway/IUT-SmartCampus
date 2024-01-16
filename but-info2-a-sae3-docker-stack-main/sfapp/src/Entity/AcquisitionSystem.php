<?php

namespace App\Entity;

use App\Repository\AcquisitionSystemRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: AcquisitionSystemRepository::class)]
#[UniqueEntity(fields: "name", message: "Un système d'acquisition avec ce nom existe déjà.")]
class AcquisitionSystem
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 10, unique: true)]
    private ?string $name = null;

    #[ORM\OneToOne(inversedBy: 'acquisitionSystem', cascade: ['persist', 'remove'])]
    private ?Room $room = null;

    #[ORM\Column]
    private ?bool $isInstalled = null;

    // 0 = OK, 1 = doit être installé, 2 = doit être désinstallé
    #[ORM\Column]
    private ?int $state = null;

    /**
     * AcquisitionSystem constructor.
     * Initialise la propriété isInstalled à false.
     */
    public function __construct()
    {
        // Initialiser isInstalled avec la valeur 0 (false) lors de la création de l'instance
        $this->isInstalled = false;

        // Initialiser state avec la valeur 0 lors de la création de l'instance
        $this->state = 0;
    }

    /**
     * Obtient l'identifiant du système d'acquisition.
     *
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Obtient le nom du système d'acquisition.
     *
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * Définit le nom du système d'acquisition.
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
     * Obtient la salle associée à ce système d'acquisition.
     *
     * @return Room|null
     */
    public function getRoom(): ?Room
    {
        return $this->room;
    }

    /**
     * Définit la salle associée à ce système d'acquisition.
     *
     * @param Room|null $room
     * @return $this
     */
    public function setRoom(?Room $room): static
    {
        $this->room = $room;

        return $this;
    }

    public function isIsInstalled(): ?bool
    {
        return $this->isInstalled;
    }

    public function setIsInstalled(bool $isInstalled): static
    {
        $this->isInstalled = $isInstalled;

        return $this;
    }

    public function getState(): ?int
    {
        return $this->state;
    }

    public function setState(int $state): static
    {
        $this->state = $state;

        return $this;
    }
}
