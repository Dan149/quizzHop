<?php

namespace App\Domain\Quizz\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Domain\Quizz\Repository\QuizzRepository;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\HttpFoundation\File\File;
use Doctrine\Common\Collections\ArrayCollection;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Vich\UploaderBundle\Entity\File as EmbeddedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

/**
 * @ApiResource()
 * @ORM\Entity(repositoryClass=QuizzRepository::class)
 * @Vich\Uploadable
 */
class Quizz
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="integer")
     */
    private $players;

    /**
     * @ORM\OneToMany(targetEntity=Question::class, mappedBy="quizz", orphanRemoval=true)
     */
    private $questions;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $slug;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isPrivate = false;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="quizzes")
     */
    private $creator;

    /**
     * @ORM\ManyToOne(targetEntity=Category::class, inversedBy="quizzes")
     */
    private $category;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $note;

    /**
     * @Vich\UploadableField(mapping="quizz_image", fileNameProperty="image.name", size="image.size", mimeType="image.mimeType", originalName="image.originalName", dimensions="image.dimensions")
     *
     * @var File|null
     */
    private $imageFile;

    /**
     * @ORM\Embedded(class="Vich\UploaderBundle\Entity\File")
     *
     * @var EmbeddedFile
     */
    private $image;

    /**
     * @ORM\Column(type="datetime")
     *
     * @var \DateTimeInterface|null
     */
    private $updatedAt;

    public function __construct()
    {
        $this->questions = new ArrayCollection();
        $this->image = new EmbeddedFile();
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getPlayers(): ?int
    {
        return $this->players;
    }

    public function setPlayers(int $players): self
    {
        $this->players = $players;

        return $this;
    }

    /**
     * @return Collection|Question[]
     */
    public function getQuestions(): Collection
    {
        return $this->questions;
    }

    public function addQuestion(Question $question): self
    {
        if (!$this->questions->contains($question)) {
            $this->questions[] = $question;
            $question->setQuizz($this);
        }

        return $this;
    }

    public function removeQuestion(Question $question): self
    {
        if ($this->questions->removeElement($question)) {
            // set the owning side to null (unless already changed)
            if ($question->getQuizz() === $this) {
                $question->setQuizz(null);
            }
        }

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function computeSlug(SluggerInterface $slugger)
    {
        if (!$this->slug || '-' === $this->slug) {
            $this->slug = (string) $slugger->slug((string) $this)->lower();
        }
    }

    public function __toString()
    {
        return $this->name;
    }

    public function getIsPrivate(): ?bool
    {
        return $this->isPrivate;
    }

    public function setIsPrivate(bool $isPrivate): self
    {
        $this->isPrivate = $isPrivate;

        return $this;
    }

    public function getCreator(): ?User
    {
        return $this->creator;
    }

    public function setCreator(?User $creator): self
    {
        $this->creator = $creator;

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

    public function getNote(): ?int
    {
        return $this->note;
    }

    public function setNote(?int $note): self
    {
        $this->note = $note;

        return $this;
    }

    /**
     * @param File|UploadedFile|null $imageFile
     */
    public function setImageFile(?File $imageFile = null)
    {
        $this->imageFile = $imageFile;

        if (null !== $imageFile) {
            $this->updatedAt = new \DateTimeImmutable();
        }
    }

    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    public function setImage(EmbeddedFile $image): void
    {
        $this->image = $image;
    }

    public function getImage(): ?EmbeddedFile
    {
        return $this->image;
    }
}
