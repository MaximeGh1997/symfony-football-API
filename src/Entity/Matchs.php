<?php

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiSubresource;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups as Groupes;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\BooleanFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\ExistsFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;


/**
 * @ORM\Entity(repositoryClass="App\Repository\MatchsRepository")
 * @ORM\HasLifecycleCallbacks
 * @ApiResource(
 *  attributes={
 *      "order"={"date.date":"asc"}
 *  },
 *  normalizationContext={
 *      "groups"={"match_read"}
 *  },
 *  subresourceOperations={
 *      "api_groups_matchs_get_subresource"={
 *          "normalization_context"={"groups"={"matchs_subresource"}}
 *      },
 *      "api_stages_matchs_get_subresource"={
 *          "normalization_context"={"groups"={"matchs_subresource"}}
 *      }
 *  },
 *  collectionOperations={"GET"},
 *  itemOperations={"GET"}
 * )
 * @ApiFilter(DateFilter::class, properties={"date.date"})
 * @ApiFilter(OrderFilter::class, properties={"date.date"}, arguments={"orderParameterName"="order"})
 * @ApiFilter(BooleanFilter::class, properties={"isPlayed"})
 * @ApiFilter(ExistsFilter::class, properties={"isPlayed"})
 */
class Matchs
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groupes({"match_read", "matchs_subresource"})
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Teams", inversedBy="homeMatchs")
     * @ORM\JoinColumn(nullable=false)
     * @Groupes({"matchs_subresource", "match_read", "comments_subresource"})
     */
    private $team1;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Teams", inversedBy="awayMatchs")
     * @ORM\JoinColumn(nullable=false)
     * @Groupes({"matchs_subresource", "match_read", "comments_subresource"})
     */
    private $team2;

    /**
     * @ORM\Column(type="float", nullable=true)
     * @Assert\Type(
     *     type="float",
     *     message="Le score entré est invalide."
     * )
     * @Groupes({"matchs_subresource", "match_read"})
     */
    private $scoreT1;

    /**
     * @ORM\Column(type="float", nullable=true)
     * @Assert\Type(
     *     type="float",
     *     message="Le score entré est invalide."
     * )
     * @Groupes({"matchs_subresource", "match_read"})
     */
    private $scoreT2;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Teams", inversedBy="wins")
     */
    private $winner;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Teams", inversedBy="defeats")
     */
    private $looser;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $draw;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Stades", inversedBy="matchs")
     * @ORM\JoinColumn(nullable=false)
     * @Groupes({"matchs_subresource", "match_read"})
     */
    private $stade;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Groups", inversedBy="matchs")
     * @Groupes({"matchs_subresource", "match_read"})
     */
    private $groupName;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Stages", inversedBy="matchs")
     * @Groupes({"matchs_subresource", "match_read"})
     */
    private $stage;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     * @Groupes({"matchs_subresource", "match_read"})
     */
    private $isPlayed;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $slug;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Comments", mappedBy="matchNbr", orphanRemoval=true)
     * @ApiSubresource
     */
    private $comments;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Dates", mappedBy="matchNbr", cascade={"persist", "remove"})
     * @Groupes({"matchs_subresource", "match_read"})
     */
    private $date;

    public function __construct()
    {
        $this->comments = new ArrayCollection();
    }

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

    /**
     * Permet de récuperer la note globale du match
     * @Groupes({"match_read"})
     * @return float
     */
    public function getGlobalRating()
    {   
        $comments = $this->comments->toArray();
        $sum = 0;
        foreach ($comments as $comment) {
            if ($comment->getRating() !== null){
                $sum = $sum + $comment->getRating();
            }
        }

        if($sum > 0){
            $moy = $sum / count($comments);

            return round($moy,1);
        }
        else{
            return 0;
        }
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTeam1(): ?Teams
    {
        return $this->team1;
    }

    public function setTeam1(?Teams $team1): self
    {
        $this->team1 = $team1;

        return $this;
    }

    public function getTeam2(): ?Teams
    {
        return $this->team2;
    }

    public function setTeam2(?Teams $team2): self
    {
        $this->team2 = $team2;

        return $this;
    }

    public function getScoreT1(): ?float
    {
        return $this->scoreT1;
    }

    public function setScoreT1(?float $scoreT1): self
    {
        $this->scoreT1 = $scoreT1;

        return $this;
    }

    public function getScoreT2(): ?float
    {
        return $this->scoreT2;
    }

    public function setScoreT2(?float $scoreT2): self
    {
        $this->scoreT2 = $scoreT2;

        return $this;
    }

    public function getWinner(): ?Teams
    {
        return $this->winner;
    }

    public function setWinner(?Teams $winner): self
    {
        $this->winner = $winner;

        return $this;
    }

    public function getLooser(): ?Teams
    {
        return $this->looser;
    }

    public function setLooser(?Teams $looser): self
    {
        $this->looser = $looser;

        return $this;
    }

    public function getDraw(): ?bool
    {
        return $this->draw;
    }

    public function setDraw(?bool $draw): self
    {
        $this->draw = $draw;

        return $this;
    }

    public function getStade(): ?Stades
    {
        return $this->stade;
    }

    public function setStade(?Stades $stade): self
    {
        $this->stade = $stade;

        return $this;
    }

    public function getGroupName(): ?Groups
    {
        return $this->groupName;
    }

    public function setGroupName(?Groups $groupName): self
    {
        $this->groupName = $groupName;

        return $this;
    }

    public function getStage(): ?Stages
    {
        return $this->stage;
    }

    public function setStage(?Stages $stage): self
    {
        $this->stage = $stage;

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

    public function getIsPlayed(): ?bool
    {
        return $this->isPlayed;
    }

    public function setIsPlayed(?bool $isPlayed): self
    {
        $this->isPlayed = $isPlayed;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(?string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * @return Collection|Comments[]
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comments $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setMatchNbr($this);
        }

        return $this;
    }

    public function removeComment(Comments $comment): self
    {
        if ($this->comments->contains($comment)) {
            $this->comments->removeElement($comment);
            // set the owning side to null (unless already changed)
            if ($comment->getMatchNbr() === $this) {
                $comment->setMatchNbr(null);
            }
        }

        return $this;
    }

    public function getDate(): ?Dates
    {
        return $this->date;
    }

    public function setDate(?Dates $date): self
    {
        $this->date = $date;

        // set (or unset) the owning side of the relation if necessary
        $newMatchNbr = null === $date ? null : $this;
        if ($date->getMatchNbr() !== $newMatchNbr) {
            $date->setMatchNbr($newMatchNbr);
        }

        return $this;
    }
}
