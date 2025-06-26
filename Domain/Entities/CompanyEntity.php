<?php

namespace Domain\Entities;

use DateTime;

class CompanyEntity
{
    private string $uuid;

    private string $corporateReason;

    private string $fantasyName;

    private string $cnpj;

    private string $plan;

    private bool $active;

    private DateTime $createdAt;

    private DateTime $updatedAt;

    public function __construct(
        string $uuid,
        string $corporateReason,
        string $fantasyName,
        string $cnpj,
        string $plan,
        bool $active,
        DateTime $createdAt,
        DateTime $updatedAt
    ) {
        $this->uuid = $uuid;
        $this->corporateReason = $corporateReason;
        $this->fantasyName = $fantasyName;
        $this->cnpj = $cnpj;
        $this->plan = $plan;
        $this->active = $active;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    public function getUuid(): string
    {
        return $this->uuid;
    }

    public function setUuid(string $uuid): self
    {
        $this->uuid = $uuid;
        return $this;
    }

    public function getCorporateReason(): string
    {
        return $this->corporateReason;
    }

    public function setCorporateReason(string $corporateReason): self
    {
        $this->corporateReason = $corporateReason;
        return $this;
    }

    public function getFantasyName(): string
    {
        return $this->fantasyName;
    }

    public function setFantasyName(string $fantasyName): self
    {
        $this->fantasyName = $fantasyName;
        return $this;
    }

    public function getCnpj(): string
    {
        return $this->cnpj;
    }

    public function setCnpj(string $cnpj): self
    {
        $this->cnpj = $cnpj;
        return $this;
    }

    public function getPlan(): string
    {
        return $this->plan;
    }

    public function setPlan(string $plan): self
    {
        $this->plan = $plan;
        return $this;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function setActive(bool $active): self
    {
        $this->active = $active;
        return $this;
    }

    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTime $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getUpdatedAt(): DateTime
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(DateTime $updatedAt): self
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    public function toArray(): array
    {
        return [
            'uuid' => $this->getUuid(),
            'corporate_reason' => $this->getCorporateReason(),
            'fantasy_name' => $this->getFantasyName(),
            'cnpj' => $this->getCnpj(),
            'plan' => $this->getPlan(),
            'active' => $this->isActive(),
            "updated_at" => $this->getUpdatedAt()->format('Y-m-d H:i:s'),
            "created_at" => $this->getCreatedAt()->format('Y-m-d H:i:s')
        ];
    }
}
