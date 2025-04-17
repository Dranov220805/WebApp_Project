<?php
class NoteProtect {
    public string $noteProtectId;

    public function getNoteId(): string
    {
        return $this->noteId;
    }

    public function setNoteId(string $noteId): void
    {
        $this->noteId = $noteId;
    }

    public function getNoteProtectId(): string
    {
        return $this->noteProtectId;
    }

    public function setNoteProtectId(string $noteProtectId): void
    {
        $this->noteProtectId = $noteProtectId;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    public function isEnabled(): bool
    {
        return $this->isEnabled;
    }

    public function setIsEnabled(bool $isEnabled): void
    {
        $this->isEnabled = $isEnabled;
    }

    public function isDeleted(): bool
    {
        return $this->isDeleted;
    }

    public function setIsDeleted(bool $isDeleted): void
    {
        $this->isDeleted = $isDeleted;
    }
    public string $noteId;
    public string $password;
    public bool $isEnabled;
    public bool $isDeleted;

    public function __construct($noteProtectId, $noteId, $password, $isEnabled, $isDeleted) {
        $this->noteProtectId = $noteProtectId;
        $this->noteId = $noteId;
        $this->password = $password;
        $this->isEnabled = $isEnabled;
        $this->isDeleted = $isDeleted;
    }
}
