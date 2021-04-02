<?php

namespace App\Entity;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\SerieRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass=SerieRepository::class)
 */
class Serie
{
    /**
     * @var int
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     */
    private string $name;

    /**
     * @var Collection<Library>
     * @ORM\OneToMany(targetEntity=Library::class, mappedBy="serie")
     * @ORM\OrderBy({"season" = "ASC", "episode" = "ASC"})
     */
    private Collection $library;

    /**
     * @var bool
     * @ORM\Column(type="boolean")
     */
    private bool $active;

    /**
     * @var DateTimeImmutable
     * @ORM\Column(type="datetime_immutable")
     */
    private DateTimeImmutable $createdAt;

    /**
     * @var DateTimeImmutable
     * @ORM\Column(type="datetime_immutable")
     */
    private DateTimeImmutable $updatedAt;

    /**
     * @var Category
     * @ORM\ManyToOne(targetEntity=Category::class, inversedBy="series")
     */
    private Category $category;

    public function __construct()
    {
        $this->library = new ArrayCollection();
        $this->createdAt = new DateTimeImmutable();
        $this->updatedAt = new DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return array
     */
    public function getVideos(): array
    {
        $library = [];

        foreach ($this->library as $video) {
            $library[$video->getSeason()][] = $video;
        }

        return $library;
    }

    public function getSeasons(): array
    {
        $seasons = [];

        foreach ($this->library as $video) {
            $seasons[] = $video->getSeason();
        }

        return array_unique($seasons);
    }

    public function addVideo(Library $library): self
    {
        if (!$this->library->contains($library)) {
            $this->library[] = $library;
            $library->setSerie($this);
        }

        return $this;
    }

    public function removeVideo(Library $library): self
    {
        if ($this->library->removeElement($library)) {
            // set the owning side to null (unless already changed)
            if ($library->getSerie() === $this) {
                $library->setSerie(null);
            }
        }

        return $this;
    }

    public function getActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(bool $active): self
    {
        $this->active = $active;

        return $this;
    }

    public function getCreatedAt(): ?DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(DateTimeImmutable $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }
}
