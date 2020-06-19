<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiSubresource;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups as Groupes;

/**
 * @ORM\Entity(repositoryClass="App\Repository\StagesRepository")
 * @ApiResource(
 *  subresourceOperations={
 *      "matchs_get_subresource"={"path"="/stages/{id}/matchs"}
 *  },
 *  collectionOperations={"GET"},
 *  itemOperations={"GET"}
 * )
 */
class Stages
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groupes({"matchs_subresource", "match_read"})
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Matchs", mappedBy="stage")
     * @ApiSubresource
     */
    private $matchs;

    public function __construct()
    {
        $this->matchs = new ArrayCollection();
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
            $match->setStage($this);
        }

        return $this;
    }

    public function removeMatch(Matchs $match): self
    {
        if ($this->matchs->contains($match)) {
            $this->matchs->removeElement($match);
            // set the owning side to null (unless already changed)
            if ($match->getStage() === $this) {
                $match->setStage(null);
            }
        }

        return $this;
    }
}
