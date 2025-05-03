<?php
class NoteLabel
{
    public string $noteLabelId;
    public string $noteId;
    public string $labelId;

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

    public function getLabelId(): string
    {
        return $this->labelId;
    }

    public function setLabelId(string $labelId): void
    {
        $this->labelId = $labelId;
    }

    public function __construct(string $noteLabelId, string $noteId, string $labelId)
    {
        $this->noteLabelId = $noteLabelId;
        $this->noteId = $noteId;
        $this->labelId = $labelId;
    }
}
