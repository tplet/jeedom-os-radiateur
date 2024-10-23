<?php
class ScreenText
{
    /**
     * Content to display
     */
    protected string $text = "";

    /**
     * Selected statut (used to display content with inversed color)
     */
    protected bool $selected = false;

    /**
     * Flag to indicate if text is selectable
     */
    protected bool $selectable = false;

    public function __construct(bool $selectable = false, bool $selected = false, string $text = "")
    {
        $this->selectable = $selectable;
        $this->selected = $selected;
        $this->text = $text;
    }

    public function setText(string $text): void { $this->text = $text; }
    public function getText(): string { return $this->text; }
    public function setSelected(bool $flag): void { $this->selected = $flag; }
    public function isSelected(): bool { return $this->selected; }
    public function setSelectable(bool $flag): void { $this->selectable = $flag; }
    public function isSelectable(): bool { return $this->selectable; }
}
