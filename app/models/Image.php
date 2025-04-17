<?php
class Image {
    public string $imageId;
    public string $noteId;
    public string $title;

    public function getImageId(): string
    {
        return $this->imageId;
    }

    public function setImageId(string $imageId): void
    {
        $this->imageId = $imageId;
    }

    public function isDeleted(): bool
    {
        return $this->isDeleted;
    }

    public function setIsDeleted(bool $isDeleted): void
    {
        $this->isDeleted = $isDeleted;
    }

    public function getImageLink(): string
    {
        return $this->imageLink;
    }

    public function setImageLink(string $imageLink): void
    {
        $this->imageLink = $imageLink;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getNoteId(): string
    {
        return $this->noteId;
    }

    public function setNoteId(string $noteId): void
    {
        $this->noteId = $noteId;
    }
    public string $imageLink;
    public bool $isDeleted;

    public function __construct($imageId, $noteId, $title, $imageLink, $isDeleted) {
        $this->imageId = $imageId;
        $this->noteId = $noteId;
        $this->title = $title;
        $this->imageLink = $imageLink;
        $this->isDeleted = $isDeleted;
    }
}
