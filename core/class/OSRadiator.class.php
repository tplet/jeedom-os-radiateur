<?php

require_once __DIR__ . '/../../../../core/php/core.inc.php';
require_once __DIR__ . '/OSRadiatorCmd.php';
require_once __DIR__ . '/OSRadiatorService.php';
require_once __DIR__ . '/TasmotaDisplayService.php';

class OSRadiator extends eqLogic
{
    const KEY_SCREEN_SELECTION_INDEX = 'screenSelectionIndex';
    const KEY_SCREEN_ON = 'screenOn';

    /**
     * Virtual screen associated
     *
     * @var Screen|null
     */
    protected ?Screen $screen = null;

    protected ?ScreenText $screenTextTemperature = null;
    protected ?ScreenText $screenTextTarget = null;
    protected ?ScreenText $screenTextHeatMode = null;
    protected ?ScreenText $screenTextHeatSubMode = null;
    protected ?ScreenText $screenTextHeatBrut = null;

    /**
     * @var cmd[string]
     */
    protected array $configCmd = [];

    /**
     * Call when cmd value listened is updated
     * Proxy
     *
     * @param array $options
     * @return void
     */
    static public function dispatchCmdListened(array $options = []): void
    {
        OSRadiatorService::dispatchCmdListened($options);
    }

    /**
     * Post save
     *
     * @return void
     */
    public function postSave()
    {
        OSRadiatorService::subscribeListenerFromConfiguration($this);
    }


    public function getScreen(): Screen
    {
        if ($this->screen === null) {
            $this->screen = OSRadiatorService::provideScreen($this);
        }

        return $this->screen;
    }

    public function getScreenSelectionIndex(): int
    {
        return (int)$this->getCache(self::KEY_SCREEN_SELECTION_INDEX, 0);
    }

    public function setScreenSelectionIndex(int $index): void
    {
        $this->setCache(self::KEY_SCREEN_SELECTION_INDEX, $index);
        $this->save(true);

        OSRadiatorService::logInfo($this->getHumanName() . ': Set screen selection index: ' . $index);
    }

    public function isScreenOn(): bool
    {
        return (bool)$this->getCache(self::KEY_SCREEN_ON, false);
    }

    public function setScreenOn(bool $on, bool $refresh = false): void
    {
        $this->setCache(self::KEY_SCREEN_ON, $on);
        $this->save(true);
        $this->getScreen()->setOn($on);

        if ($refresh) {
            OSRadiatorService::refreshScreen($this);
        }
    }

    /**
     * Get configuration cmd by key
     *
     * @param string $key
     * @param bool $force
     * @return cmd|null
     */
    public function getConfigurationCmd(string $key, bool $force = false): ?cmd
    {
        if ($force || !isset($this->configCmd[$key])) {
            $config = OSRadiatorService::getConfig();
            $cmdString = isset($config[$key]['key']) ? $this->getConfiguration($config[$key]['key']) : null;

            $this->configCmd[$key] = !empty($cmdString) ? cmd::byString($cmdString) : null;
        }

        return $this->configCmd[$key];
    }

    /**
     * Get configuration cmd value by key
     *
     * @param string $key
     * @return string|null
     */
    public function getConfigurationCmdValue(string $key): ?string
    {
        $cmd = $this->getConfigurationCmd($key);

        return $cmd ? $cmd->execCmd() : null;
    }

    /**
     * Flag to indicate has sub mode
     *
     * @return bool
     */
    public function hasSubMode(): bool
    {
        return $this->getConfigurationCmd(OSRadiatorService::KEY_SUB_MODE) !== null;
    }

    public function getScreenTextTemperature(): ?ScreenText
    {
        if ($this->screenTextTemperature === null) {
            $this->screenTextTemperature = new ScreenText(false);
        }

        return $this->screenTextTemperature;
    }

    public function getScreenTextTarget(): ?ScreenText
    {
        if ($this->screenTextTarget === null) {
            $this->screenTextTarget = new ScreenText(true);
        }

        return $this->screenTextTarget;
    }

    public function getScreenTextHeatMode(): ?ScreenText
    {
        if ($this->screenTextHeatMode === null) {
            $this->screenTextHeatMode = new ScreenText(true);
        }

        return $this->screenTextHeatMode;
    }

    public function getScreenTextHeatSubMode(): ?ScreenText
    {
        if ($this->screenTextHeatSubMode === null) {
            $this->screenTextHeatSubMode = new ScreenText(true);
        }

        return $this->screenTextHeatSubMode;
    }

    public function getScreenTextHeatBrut(): ?ScreenText
    {
        if ($this->screenTextHeatBrut === null) {
            $this->screenTextHeatBrut = new ScreenText(false);
        }

        return $this->screenTextHeatBrut;
    }
}