<?php

class Label {
    public string $labelId;
    public string $accountId;
    public string $labelName;
    public bool $isDeleted;

    public function __construct(string $labelId, string $accountId, string $labelName, bool $isDeleted)
    {
        $this->labelId = $labelId;
        $this->accountId = $accountId;
        $this->labelName = $labelName;
        $this->isDeleted = $isDeleted;
    }

    public function getLabelId(): string
    {
        return $this->labelId;
    }

    public function setLabelId(string $labelId): void
    {
        $this->labelId = $labelId;
    }

    public function getAccountId(): string
    {
        return $this->accountId;
    }

    public function setAccountId(string $accountId): void
    {
        $this->accountId = $accountId;
    }

    public function getLabelName(): string
    {
        return $this->labelName;
    }

    public function setLabelName(string $labelName): void
    {
        $this->labelName = $labelName;
    }

    public function isDeleted(): bool
    {
        return $this->isDeleted;
    }

    public function setIsDeleted(bool $isDeleted): void
    {
        $this->isDeleted = $isDeleted;
    }
}
