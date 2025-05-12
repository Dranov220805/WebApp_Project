<?php
class NoteSharing {
    public string $noteSharingId;
    public string $noteId;
    public string $sharedEmail;
    public string $receivedEmail;
    public string $timeShared;
    public bool $canEdit;

    public function getreceivedEmail(): string
    {
        return $this->receivedEmail;
    }

    public function setreceivedEmail(string $receivedEmail): void
    {
        $this->receivedEmail = $receivedEmail;
    }

    public function getsharedEmail(): string
    {
        return $this->sharedEmail;
    }

    public function setsharedEmail(string $sharedEmail): void
    {
        $this->sharedEmail = $sharedEmail;
    }

    public function getNoteSharingId(): string
    {
        return $this->noteSharingId;
    }

    public function setNoteSharingId(string $noteSharingId): void
    {
        $this->noteSharingId = $noteSharingId;
    }

    public function getNoteId(): string
    {
        return $this->noteId;
    }

    public function setNoteId(string $noteId): void
    {
        $this->noteId = $noteId;
    }

    public function getTimeShared(): string
    {
        return $this->timeShared;
    }

    public function setTimeShared(string $timeShared): void
    {
        $this->timeShared = $timeShared;
    }

    public function isCanEdit(): bool
    {
        return $this->canEdit;
    }

    public function setCanEdit(bool $canEdit): void
    {
        $this->canEdit = $canEdit;
    }

    public function __construct($noteSharingId, $noteId, $sharedEmail, $receivedEmail, $timeShared, $canEdit) {
        $this->noteSharingId = $noteSharingId;
        $this->noteId = $noteId;
        $this->sharedEmail = $sharedEmail;
        $this->receivedEmail = $receivedEmail;
        $this->timeShared = $timeShared;
        $this->canEdit = $canEdit;
    }
}
