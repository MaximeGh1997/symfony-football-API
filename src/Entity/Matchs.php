<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\MatchsRepository")
 */
class Matchs
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Teams", inversedBy="homeMatchs")
     * @ORM\JoinColumn(nullable=false)
     */
    private $team1;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Teams", inversedBy="awayMatchs")
     * @ORM\JoinColumn(nullable=false)
     */
    private $team2;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $scoreT1;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $scoreT2;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date;

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
     */
    private $stade;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Groups", inversedBy="matchs")
     */
    private $groupName;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Stages", inversedBy="matchs")
     */
    private $stage;

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

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

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
}
