<?php

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use http\Exception\InvalidArgumentException;

/**
 * @ORM\Entity
 */
class Prediction
{
    //Market type constants
    const THREE_WAY_RESULT = '1x2';
    const CORRECT_SCORE = 'correct_score';

    //Status constants
    const WIN = 'win';
    const LOST = 'lost';
    const UNRESOLVED = 'unresolved';

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $event_id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $market_type;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $prediction;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $status;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $created_at;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $updated_at;

    public function __construct()
    {
        $this->market_type = self::THREE_WAY_RESULT;
        $this->status = self::UNRESOLVED;
        $this->created_at = (new DateTime)->getTimestamp();
        $this->updated_at = (new DateTime)->getTimestamp();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function getEventId(): ?int
    {
        return $this->event_id;
    }

    /**
     * Set event_id and check if event_id is correct
     * @param int $event_id
     * @return $this
     */
    public function setEventId(int $event_id): self
    {
        if($event_id < 1) {
            throw new \InvalidArgumentException("Invalid event id");
        }
        $this->event_id = $event_id;

        return $this;
    }

    public function getMarketType(): ?string
    {
        return $this->market_type;
    }

    /**
     * Set market_type and check if market_type is a three-way result or a correct score
     * @param string $market_type
     * @return $this
     */
    public function setMarketType(string $market_type): self
    {
        if(!in_array($market_type,array(self::THREE_WAY_RESULT,self::CORRECT_SCORE))) {
            throw new \InvalidArgumentException("Invalid market type");
        }
        $this->market_type = $market_type;

        return $this;
    }

    public function getPrediction(): ?string
    {
        return $this->prediction;
    }

    /**
     * Set prediction and check if prediction is correct
     * For market_type three-way result values : 1, X or 2
     * For market_type correct score values : integer:integer
     * @param string $prediction
     * @return $this
     */
    public function setPrediction(string $prediction): self
    {
        switch ($this->market_type) {
            case self::THREE_WAY_RESULT:
                if(!in_array($prediction,["1","X","2"])){
                    throw new InvalidArgumentException("Invalid prediction");
                }
                break;
            case self::CORRECT_SCORE:
                if(!preg_match("/[0-9]+:[0-9]+/",$prediction)){
                    throw new InvalidArgumentException("Invalid prediction");
                }
                break;
            default:
                throw new InvalidArgumentException("Invalid prediction");
        }

        $this->prediction = $prediction;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    /**
     * Set status and check if status is either win, lost, unresolved
     * @param string $status
     * @return $this
     */
    public function setStatus(string $status): self
    {
        if(!in_array($status,array(self::WIN,self::LOST, self::UNRESOLVED))) {
            throw new \InvalidArgumentException("Invalid status");
        }
        $this->status = $status;

        return $this;
    }

    public function getCreatedAt(): ?string
    {
        return $this->created_at;
    }

    public function setCreatedAt(string $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getUpdatedAt(): ?string
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(string $updated_at): self
    {
        $this->updated_at = $updated_at;

        return $this;
    }
}
