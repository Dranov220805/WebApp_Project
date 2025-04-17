<?php
class Modification {
    public string $modifyId;
    public string $noteId;

    public function getModifyId(): string
    {
        return $this->modifyId;
    }

    public function setModifyId(string $modifyId): void
    {
        $this->modifyId = $modifyId;
    }

    public function getNoteId(): string
    {
        return $this->noteId;
    }

    public function setNoteId(string $noteId): void
    {
        $this->noteId = $noteId;
    }

    public function isPinned(): bool
    {
        return $this->isPinned;
    }

    public function setIsPinned(bool $isPinned): void
    {
        $this->isPinned = $isPinned;
    }

    public function getPinnedTime(): ?string
    {
        return $this->pinnedTime;
    }

    public function setPinnedTime(?string $pinnedTime): void
    {
        $this->pinnedTime = $pinnedTime;
    }

    public function isShared(): bool
    {
        return $this->isShared;
    }

    public function setIsShared(bool $isShared): void
    {
        $this->isShared = $isShared;
    }
    public bool $isPinned;
    public ?string $pinnedTime;
    public bool $isShared;

    public function __construct($modifyId, $noteId, $isPinned, $pinnedTime, $isShared) {
        $this->modifyId = $modifyId;
        $this->noteId = $noteId;
        $this->isPinned = $isPinned;
        $this->pinnedTime = $pinnedTime;
        $this->isShared = $isShared;
    }
}
