<?php

namespace App\Entity;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\CategoryRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass=CategoryRepository::class)
 */
class Category
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     */
    private string $name;

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     */
    private string $path;

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     */
    private string $type;

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     */
    private string $icon;

    /**
     * @var DateTimeImmutable
     * @ORM\Column(type="datetime_immutable")
     */
    private $createdAt;

    /**
     * @var Collection<Library>
     * @ORM\OneToMany(targetEntity=Library::class, mappedBy="category")
     */
    private Collection $library;

    public function __construct()
    {
        $this->library = new ArrayCollection();
        $this->createdAt = new DateTimeImmutable();
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

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function setPath(string $path): self
    {
        $this->path = $path;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getIcon(): ?string
    {
        return $this->icon;
    }

    public function setIcon(string $icon): self
    {
        $this->icon = $icon;

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

    /**
     * @return Collection|Library[]
     */
    public function getVideos(): Collection
    {
        return $this->library;
    }

    public function addVideo(Library $library): self
    {
        if (!$this->library->contains($library)) {
            $this->library[] = $library;
            $library->setCategory($this);
        }

        return $this;
    }

    public function removeVideo(Library $library): self
    {
        if ($this->library->removeElement($library)) {
            // set the owning side to null (unless already changed)
            if ($library->getCategory() === $this) {
                $library->setCategory(null);
            }
        }

        return $this;
    }
}
