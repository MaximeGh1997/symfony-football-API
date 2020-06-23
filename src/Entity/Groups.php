<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinTable;
use App\Services\SortByFieldExtension;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiSubresource;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups as Groupes;

/**
 * @ORM\Entity(repositoryClass="App\Repository\GroupsRepository")
 * @ApiResource(
 *  normalizationContext={
 *      "groups"={"groups_read"}
 *  },
 *  subresourceOperations={
 *      "matchs_get_subresource"={"path"="/groupes/{id}/matchs"}
 *  },
 *  collectionOperations={"GET"},
 *  itemOperations={"GET"}
 * )
 */
class Groups
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groupes({"groups_read"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groupes({"groups_read", "stades_read", "matchs_subresource", "match_read"})
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Teams", mappedBy="groupName")
     * @ORM\OrderBy({"points" = "DESC"})
     * @Groupes({"groups_read"})
     */
    private $teams;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Matchs", mappedBy="groupName")
     * @ApiSubresource
     */
    private $matchs;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Stades", mappedBy="groups")
     */
    private $stades;

    public function __construct()
    {
        $this->teams = new ArrayCollection();
        $this->matchs = new ArrayCollection();
        $this->stades = new ArrayCollection();
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
     * @return Collection|Teams[]
     */
    public function getTeams(): Collection
    {
        return $this->teams;
    }

    public function addTeam(Teams $team): self
    {
        if (!$this->teams->contains($team)) {
            $this->teams[] = $team;
            $team->setGroupName($this);
        }

        return $this;
    }

    public function removeTeam(Teams $team): self
    {
        if ($this->teams->contains($team)) {
            $this->teams->removeElement($team);
            // set the owning side to null (unless already changed)
            if ($team->getGroupName() === $this) {
                $team->setGroupName(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Matchs[]
     */
    public function getMatchs(): Collection
    {
        return $this->matchs;
    }

    public function addMatch(Matchs $match): self
    {
        if (!$this->matchs->contains($match)) {
            $this->matchs[] = $match;
            $match->setGroupName($this);
        }

        return $this;
    }

    public function removeMatch(Matchs $match): self
    {
        if ($this->matchs->contains($match)) {
            $this->matchs->removeElement($match);
            // set the owning side to null (unless already changed)
            if ($match->getGroupName() === $this) {
                $match->setGroupName(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Stades[]
     */
    public function getStades(): Collection
    {
        return $this->stades;
    }

    public function addStade(Stades $stade): self
    {
        if (!$this->stades->contains($stade)) {
            $this->stades[] = $stade;
            $stade->setGroups($this);
        }

        return $this;
    }

    public function removeStade(Stades $stade): self
    {
        if ($this->stades->contains($stade)) {
            $this->stades->removeElement($stade);
            // set the owning side to null (unless already changed)
            if ($stade->getGroups() === $this) {
                $stade->setGroups(null);
            }
        }

        return $this;
    }
}
