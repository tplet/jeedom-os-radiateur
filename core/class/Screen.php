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
     * Flag to indicate if screen is initialized
     * @var bool
     */
    protected bool $initialized = false;

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
        $count = count($list);
        if ($count > 0) {
            $this->setSelectedIndex(($this->selectedIndex + 1) % $count);

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
        $count = count($list);
        if ($count > 0) {
            $this->setSelectedIndex(($this->selectedIndex - 1 + $count) % $count);
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
     * @return ScreenComponent[]
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
            // Previous
            if ($this->currentScreenText !== null) {
                $this->currentScreenText->setSelected(false);
            }
            // Next
            $this->currentScreenComponent = $list[$selectedIndex][0];
            $this->currentScreenText = $list[$selectedIndex][1];
            $this->currentScreenText->setSelected(true);
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

    /**
     * @return bool
     */
    public function isInitialized(): bool
    {
        return $this->initialized;
    }

    /**
     * @param bool $initialized
     */
    public function setInitialized(bool $initialized): void
    {
        $this->initialized = $initialized;
    }

    public function __toString(): string
    {
        return str_replace('"', '\'', json_encode([
            'selectedIndex' => $this->selectedIndex,
            'initialized' => $this->initialized,
            'on' => $this->on,
        ]));
    }


}
