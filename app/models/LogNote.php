<?php
class LogNote {
    public string $logNoteId;
    public string $noteId;

    public function getLogNoteId(): string
    {
        return $this->logNoteId;
    }

    public function setLogNoteId(string $logNoteId): void
    {
        $this->logNoteId = $logNoteId;
    }

    public function getNoteId(): string
    {
        return $this->noteId;
    }

    public function setNoteId(string $noteId): void
    {
        $this->noteId = $noteId;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(string $content): void
    {
        $this->content = $content;
    }

    public function getProcess(): string
    {
        return $this->process;
    }

    public function setProcess(string $process): void
    {
        $this->process = $process;
    }

    public function getUpdateTime(): string
    {
        return $this->updateTime;
    }

    public function setUpdateTime(string $updateTime): void
    {
        $this->updateTime = $updateTime;
    }

    public function getFlag(): string
    {
        return $this->flag;
    }

    public function setFlag(string $flag): void
    {
        $this->flag = $flag;
    }
    public string $content;
    public string $process;
    public string $updateTime;
    public string $flag;

    public function __construct($logNoteId, $noteId, $content, $process, $updateTime, $flag) {
        $this->logNoteId = $logNoteId;
        $this->noteId = $noteId;
        $this->content = $content;
        $this->process = $process;
        $this->updateTime = $updateTime;
        $this->flag = $flag;
    }
}
