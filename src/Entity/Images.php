<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use App\Repository\ImagesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;


#[ApiResource()]
#[ApiFilter(OrderFilter::class, properties: ['id'])]
#[ORM\Entity(repositoryClass: ImagesRepository::class)]
class Images
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $url = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column]
    private ?int $nb_download = null;

    #[ORM\Column]
    private ?int $nb_opened = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $last_time_viewed = null;

    #[ORM\Column]
    private ?bool $is_deleted = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $creation_date = null;

    /**
     * @var Collection<int, Logs>
     */
    #[ORM\OneToMany(targetEntity: Logs::class, mappedBy: 'images')]
    private Collection $logs;

    public function __construct()
    {
        $this->logs = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): static
    {
        $this->url = $url;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getNbDownload(): ?int
    {
        return $this->nb_download;
    }

    public function setNbDownload(int $nb_download): static
    {
        $this->nb_download = $nb_download;

        return $this;
    }

    public function getNbOpened(): ?int
    {
        return $this->nb_opened;
    }

    public function setNbOpened(int $nb_opened): static
    {
        $this->nb_opened = $nb_opened;

        return $this;
    }

    public function getLastTimeViewed(): ?\DateTimeInterface
    {
        return $this->last_time_viewed;
    }

    public function setLastTimeViewed(\DateTimeInterface $last_time_viewed): static
    {
        $this->last_time_viewed = $last_time_viewed;

        return $this;
    }

    public function isDeleted(): ?bool
    {
        return $this->is_deleted;
    }

    public function setIsDeleted(bool $is_deleted): static
    {
        $this->is_deleted = $is_deleted;

        return $this;
    }

    public function getCreationDate(): ?\DateTimeInterface
    {
        return $this->creation_date;
    }

    public function setCreationDate(\DateTimeInterface $creation_date): static
    {
        $this->creation_date = $creation_date;

        return $this;
    }

    /**
     * @return Collection<int, Logs>
     */
    public function getLogs(): Collection
    {
        return $this->logs;
    }

    public function addLog(Logs $log): static
    {
        if (!$this->logs->contains($log)) {
            $this->logs->add($log);
            $log->setImages($this);
        }

        return $this;
    }

    public function removeLog(Logs $log): static
    {
        if ($this->logs->removeElement($log)) {
            // set the owning side to null (unless already changed)
            if ($log->getImages() === $this) {
                $log->setImages(null);
            }
        }

        return $this;
    }
}
