<?php
class Preference {
    public string $preferenceId;
    public string $accountId;
    public string $layout;
    public string $noteFont;
    public string $noteColor;
    public string $font;
    public bool $isDarkTheme;

    public function __construct($preferenceId, $accountId, $layout, $noteFont, $noteColor, $font, $isDarkTheme) {
        $this->preferenceId = $preferenceId;
        $this->accountId = $accountId;
        $this->layout = $layout;
        $this->noteFont = $noteFont;
        $this->noteColor = $noteColor;
        $this->font = $font;
        $this->isDarkTheme = $isDarkTheme;
    }

    public function getPreferenceId(): string
    {
        return $this->preferenceId;
    }

    public function setPreferenceId(string $preferenceId): void
    {
        $this->preferenceId = $preferenceId;
    }

    public function isDarkTheme(): bool
    {
        return $this->isDarkTheme;
    }

    public function setIsDarkTheme(bool $isDarkTheme): void
    {
        $this->isDarkTheme = $isDarkTheme;
    }

    public function getFont(): string
    {
        return $this->font;
    }

    public function setFont(string $font): void
    {
        $this->font = $font;
    }

    public function getNoteColor(): string
    {
        return $this->noteColor;
    }

    public function setNoteColor(string $noteColor): void
    {
        $this->noteColor = $noteColor;
    }

    public function getNoteFont(): string
    {
        return $this->noteFont;
    }

    public function setNoteFont(string $noteFont): void
    {
        $this->noteFont = $noteFont;
    }

    public function getLayout(): string
    {
        return $this->layout;
    }

    public function setLayout(string $layout): void
    {
        $this->layout = $layout;
    }

    public function getAccountId(): string
    {
        return $this->accountId;
    }

    public function setAccountId(string $accountId): void
    {
        $this->accountId = $accountId;
    }
}
