<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups as Groupes;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CommentsRepository")
 * @ORM\HasLifecycleCallbacks
 * @ApiResource(
 *  attributes={
 *      "pagination_enabled"=false,
 *      "order"={"createdAt":"desc"}
 *  },
 *  subresourceOperations={
 *      "api_matchs_comments_get_subresource"={
 *          "normalization_context"={"groups"={"comments_subresource"}}
 *      },
 *      "api_users_comments_get_subresource"={
 *          "normalization_context"={"groups"={"comments_subresource"}}          
 *      }
 * },
 * collectionOperations={"GET","POST"},
 * itemOperations={"GET","DELETE"},
 * denormalizationContext={"disable_type_enforcement"=true}
 * )
 */
class Comments
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groupes({"comments_subresource"})
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     * @Groupes({"comments_subresource"})
     */
    private $createdAt;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Groupes({"comments_subresource"})
     */
    private $rating;

    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank(message="Ce champ est obligatoire !")
     * @Groupes({"comments_subresource"})
     */
    private $content;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Matchs", inversedBy="comments")
     * @ORM\JoinColumn(nullable=false)
     * @Groupes({"comments_subresource"})
     */
    private $matchNbr;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Users", inversedBy="comments")
     * @ORM\JoinColumn(nullable=false)
     * @Groupes({"comments_subresource"})
     */
    private $author;

    /**
     * Permet d'intialiser la date de création
     * 
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function initializeCreatedAt(){
        if(empty($this->createdAt)){
            $this->createdAt = new \DateTime('Europe/Brussels');
        }
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getRating(): ?int
    {
        return $this->rating;
    }

    public function setRating($rating): self
    {
        $this->rating = $rating;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent($content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getMatchNbr(): ?Matchs
    {
        return $this->matchNbr;
    }

    public function setMatchNbr(?Matchs $matchNbr): self
    {
        $this->matchNbr = $matchNbr;

        return $this;
    }

    public function getAuthor(): ?Users
    {
        return $this->author;
    }

    public function setAuthor(?Users $author): self
    {
        $this->author = $author;

        return $this;
    }
}
