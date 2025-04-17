<?php
class NoteLabel {
    public string $noteLabelId;
    public string $noteId;

    public function getNoteLabelId(): string
    {
        return $this->noteLabelId;
    }

    public function setNoteLabelId(string $noteLabelId): void
    {
        $this->noteLabelId = $noteLabelId;
    }

    public function getNoteId(): string
    {
        return $this->noteId;
    }

    public function setNoteId(string $noteId): void
    {
        $this->noteId = $noteId;
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
    public string $labelName;
    public bool $isDeleted;

    public function __construct($noteLabelId, $noteId, $labelName, $isDeleted) {
        $this->noteLabelId = $noteLabelId;
        $this->noteId = $noteId;
        $this->labelName = $labelName;
        $this->isDeleted = $isDeleted;
    }
}
