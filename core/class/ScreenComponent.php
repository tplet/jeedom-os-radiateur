<?php

require_once __DIR__ . '/ScreenText.php';

class ScreenComponent
{
    /**
     * Screen texts
     */
    protected array $screenTexts = [];

    /**
     * Coordinate
     */
    protected int $x = 0;
    protected int $y = 0;

    /**
     * Pad to write text and center (number of char)

     * @var int
     */
    protected int $pad = 0;

    public function __construct(int $x = 0, int $y = 0, int $pad = 0)
    {
        $this->x = $x;
        $this->y = $y;
        $this->pad = $pad;
    }

    /**
     * @return ScreenText[]
     */
    public function getScreenTexts(): array
    {
        return $this->screenTexts;
    }

    /**
     * @param ScreenText $screenText
     * @return void
     */
    public function addScreenText(ScreenText $screenText): void
    {
        $this->screenTexts[] = $screenText;
    }

    /**
     * @return int
     */
    public function getX(): int
    {
        return $this->x;
    }

    /**
     * @return int
     */
    public function getY(): int
    {
        return $this->y;
    }

    /**
     * @param int $x
     */
    public function setX(int $x): void
    {
        $this->x = $x;
    }

    /**
     * @param int $y
     */
    public function setY(int $y): void
    {
        $this->y = $y;
    }

    public function getPad(): int
    {
        return $this->pad;
    }

    public function setPad(int $pad): void
    {
        $this->pad = $pad;
    }

    public function hasPad(): bool
    {
        return $this->pad > 0;
    }
}
