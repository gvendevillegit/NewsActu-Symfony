<?php

namespace App\Entity;

use App\Repository\ArticleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

//#[ORM\Entity(repositoryClass: ArticleRepository::class)]
/**
 * @ORM\Entity(repositoryClass=ArticleRepository::class)
 */
class Article
{
    // #[ORM\Id]
    // #[ORM\GeneratedValue]
    // #[ORM\Column(type: 'integer')]
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    //#[ORM\Column(type: 'string', length: 255)]
    /**
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    //#[ORM\Column(type: 'string', length: 255)]
    /**
     * @ORM\Column(type="string", length=255)
     */
    private $alias;

    //#[ORM\Column(type: 'string', length: 255)]
    /**
     * @ORM\Column(type="string", length=255)
     */
    private $subtitle;

    //#[ORM\Column(type: 'text')]
    /**
     * @Assert\NotBlank(message="Ce champ ne peut être vide")
     * @ORM\Column(type="text")
     */
    private $content;

    //#[ORM\Column(type: 'string', length: 255, nullable: true)]
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $photo;

    //#[ORM\Column(type: 'datetime')]
    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    //#[ORM\Column(type: 'datetime')]
    /**
     * @ORM\Column(type="datetime")
     */
    private $updateAt;

    //#[ORM\Column(type: 'datetime', nullable: true)]
    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $deletedAt;

    //#[ORM\ManyToOne(targetEntity: Categorie::class, inversedBy: 'articles')]
    //#[ORM\JoinColumn(nullable: false)]
    /**
     * @ORM\ManyToOne(targetEntity=Categorie::class, inversedBy="articles")
     * @ORM\JoinColumn(nullable=false)
     */
    private $category;

    //#[ORM\OneToMany(mappedBy: 'article', targetEntity: Commentary::class)]
    /**
     * @ORM\OneToMany(targetEntity=Commentary::class, mappedBy="article")
     */
    private $commentaries;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="articles")
     */
    private $author;

    public function __construct()
    {
        $this->commentaries = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getAlias(): ?string
    {
        return $this->alias;
    }

    public function setAlias(string $alias): self
    {
        $this->alias = $alias;

        return $this;
    }

    public function getSubtitle(): ?string
    {
        return $this->subtitle;
    }

    public function setSubtitle(string $subtitle): self
    {
        $this->subtitle = $subtitle;

        return $this;
    }

    // en PHP 8 :
    //#[Assert\NotBlank(message:"Ce champ ne peut être vide")]
    /**
     * @Assert\NotBlank(message="Ce champ ne paut être vide")
     */
    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getPhoto(): ?string
    {
        return $this->photo;
    }

    public function setPhoto(?string $photo): self
    {
        $this->photo = $photo;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdateAt(): ?\DateTimeInterface
    {
        return $this->updateAt;
    }

    public function setUpdateAt(\DateTimeInterface $updateAt): self
    {
        $this->updateAt = $updateAt;

        return $this;
    }

    public function getDeletedAt(): ?\DateTimeInterface
    {
        return $this->deletedAt;
    }

    public function setDeletedAt(?\DateTimeInterface $deletedAt = null): self
    {
        $this->deletedAt = $deletedAt;

        return $this;
    }

    public function getCategory(): ?Categorie
    {
        return $this->category;
    }

    public function setCategory(?Categorie $category): self
    {
        $this->category = $category;

        return $this;
    }

    /**
     * @return Collection<int, Commentary>
     */
    public function getCommentaries(): Collection
    {
        return $this->commentaries;
    }

    public function addCommentary(Commentary $commentary): self
    {
        if (!$this->commentaries->contains($commentary)) {
            $this->commentaries[] = $commentary;
            $commentary->setArticle($this);
        }

        return $this;
    }

    public function removeCommentary(Commentary $commentary): self
    {
        if ($this->commentaries->removeElement($commentary)) {
            // set the owning side to null (unless already changed)
            if ($commentary->getArticle() === $this) {
                $commentary->setArticle(null);
            }
        }

        return $this;
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    // Si on a un problème de typeage faire :
    // public function setAuthor(?UserInterface $author): self
    public function setAuthor(?User $author): self
    {
        $this->author = $author;

        return $this;
    }
}
