<?php

class Note {
    public string $noteId;
    public string $accountId;
    public string $title;
    public string $content;
    public string $createDate;
    public bool $isDeleted;
    public bool $isProtected;

    public function getNoteId(): string
    {
        return $this->noteId;
    }

    public function setNoteId(string $noteId): void
    {
        $this->noteId = $noteId;
    }

    public function getAccountId(): string
    {
        return $this->accountId;
    }

    public function setAccountId(string $accountId): void
    {
        $this->accountId = $accountId;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(string $content): void
    {
        $this->content = $content;
    }

    public function getCreateDate(): string
    {
        return $this->createDate;
    }

    public function setCreateDate(string $createDate): void
    {
        $this->createDate = $createDate;
    }

    public function isDeleted(): bool
    {
        return $this->isDeleted;
    }

    public function setIsDeleted(bool $isDeleted): void
    {
        $this->isDeleted = $isDeleted;
    }

    public function isProtected(): bool
    {
        return $this->isProtected;
    }

    public function setIsProtected(bool $isProtected): void
    {
        $this->isProtected = $isProtected;
    }

    public function __construct($noteId, $accountId, $title, $content, $createDate, $isDeleted, $isProtected) {
        $this->noteId = $noteId;
        $this->accountId = $accountId;
        $this->title = $title;
        $this->content = $content;
        $this->createDate = $createDate;
        $this->isDeleted = $isDeleted;
        $this->isProtected = $isProtected;
    }
}
