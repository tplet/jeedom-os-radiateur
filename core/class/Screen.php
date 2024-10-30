<?php

require_once __DIR__ . '/ScreenComponent.php';
require_once __DIR__ . '/ScreenText.php';
require_once __DIR__ . '/TasmotaDisplayService.php';
class Screen
{
    /**
     * Component list to display
     *
     * @var array
     */
    protected array $screenComponents = [];

    /**
     * Current selection index on screen
     * @var int
     */
    protected int $selectedIndex = 0;

    /**
     * Current selected screen component
     *
     * @var ScreenComponent|null
     */
    protected ?ScreenComponent $currentScreenComponent = null;

    /**
     * Current selected screen text
     *
     * @var ScreenText|null
     */
    protected ?ScreenText $currentScreenText = null;

    /**
     * Display on/off
     *
     * @var bool
     */
    protected bool $on = false;

    /**
     * Remove selection
     *
     * @return void
     */
    public function unselect(): void
    {
        $this->currentScreenComponent = null;
        $this->currentScreenText = null;
    }

    /**
     * Select next selectable screen text
     *
     * @return void
     */
    public function selectNext(): void
    {
        $list = $this->getSelectableScreenTexts();
        if (count($list) > 0) {
            $this->setSelectedIndex(($this->selectedIndex + 1) % count($list));
        }
    }

    /**
     * Select previous selectable screen text
     *
     * @return void
     */
    public function selectPrev(): void
    {
        $list = $this->getSelectableScreenTexts();
        if (count($list) > 0) {
            $this->setSelectedIndex(($this->selectedIndex - 1 + count($list)) % count($list));
        }
    }

    /**
     * Get all selectable screen texts
     *
     * @return array[][ScreenComponent, ScreenText]
     */
    public function getSelectableScreenTexts(): array
    {
        $list = [];
        foreach ($this->screenComponents as $screenComponent) {
            foreach ($screenComponent->getScreenTexts() as $screenText) {
                if ($screenText->isSelectable()) {
                    $list[] = [$screenComponent, $screenText];
                }
            }
        }

        return $list;
    }

    /**
     * Get screen components
     *
     * @return array
     */
    public function getScreenComponents(): array
    {
        return $this->screenComponents;
    }

    /**
     * Add a screen component
     *
     * @param ScreenComponent $screenComponent
     * @return void
     */
    public function addScreenComponent(ScreenComponent $screenComponent): void
    {
        $this->screenComponents[] = $screenComponent;
    }

    /**
     * @return int
     */
    public function getSelectedIndex(): int
    {
        return $this->selectedIndex;
    }

    /**
     * @param int $selectedIndex
     */
    public function setSelectedIndex(int $selectedIndex): void
    {
        $list = $this->getSelectableScreenTexts();
        if ($selectedIndex >= 0 && $selectedIndex < count($list)) {
            $this->currentScreenComponent = $list[$selectedIndex][0];
            $this->currentScreenText = $list[$selectedIndex][1];
            $this->selectedIndex = $selectedIndex;
        }
    }

    /**
     * Flag to indicate is selection is active
     *
     * @return bool
     */
    public function hasSelection(): bool
    {
        return $this->currentScreenText !== null;
    }

    /**
     * @return ScreenComponent|null
     */
    public function getCurrentScreenComponent(): ?ScreenComponent
    {
        return $this->currentScreenComponent;
    }

    /**
     * @return ScreenText|null
     */
    public function getCurrentScreenText(): ?ScreenText
    {
        return $this->currentScreenText;
    }

    /**
     * @return bool
     */
    public function isOn(): bool
    {
        return $this->on;
    }

    /**
     * @param bool $on
     */
    public function setOn(bool $on): void
    {
        $this->on = $on;
    }
}
