<?php

namespace Domain\Entities;

use DateTime;

class PointEntity
{

    private string $uuid;

    private UserEntity $user;

    private string $observation;

    private string $checked;

    private DateTime $updatedAt;

    private DateTime $createdAt;


    public function __construct(
        string $uuid,
        string $observation,
        string $checked,
        DateTime $updatedAt,
        DateTime $createdAt
    ) {
        $this->uuid = $uuid;
        $this->observation = $observation;
        $this->checked = $checked;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    public function getUuid()
    {
        return $this->uuid;
    }

    public function setUuid(string $uuid)
    {
        $this->uuid = $uuid;
        return $this;
    }

    public function getUser()
    {
        return $this->user;
    }

    public function setUser(UserEntity $user)
    {
        $this->user = $user;
        return $this;
    }

    public function getObservation()
    {
        return $this->observation;
    }

    public function setObservation(string $observation)
    {
        $this->observation = $observation;
        return $this;
    }

    public function getChecked()
    {
        return $this->checked;
    }

    public function setChecked(string $checked)
    {
        $this->checked = $checked;
        return $this;
    }

    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(DateTime $updatedAt)
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTime $createdAt)
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function presentation()
    {
        return [
            'name' => $this->user->getName(),
            'observation' => $this->getObservation(),
            'checked' => $this->getChecked(),
            'date' => $this->getCreatedAt()->format('Y-m-d H:i:s')
        ];
    }
}
