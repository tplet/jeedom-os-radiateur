<?php

require_once __DIR__ . '/OSRadiator.class.php';
require_once __DIR__ . '/OSRadiatorCmd.php';


class OSRadiatorService
{
    const KEY_BACKLOG = 'backlog';
    const KEY_TEMPERATURE = 'temperature';
    const KEY_CONSIGNE = 'consigne';
    const KEY_MODE = 'mode';
    const KEY_SUB_MODE = 'subMode';
    const KEY_ON_OFF = 'onOff';
    const KEY_STATE = 'state';
    const KEY_BUTTON_UP = 'buttonUp';
    const KEY_BUTTON_DOWN = 'buttonDown';
    const KEY_BUTTON_LEFT = 'buttonLeft';
    const KEY_BUTTON_RIGHT = 'buttonRight';
    const KEY_BUTTON_CLICK = 'buttonClick';

    /**
     * Generate default commands for eqLogic
     *
     * @param OSRadiator $eqLogic
     * @return bool
     * @deprecated Use eqLogicAttr configuration instead
     */
    static public function generateDefaultCommands(OSRadiator $eqLogic): bool
    {
        return false;

        // Deprecated

        self::logDebug('Generate default commands for device "' . $eqLogic->getName() . '" (' . $eqLogic->getLogicalId() . ')');

        // Generate all default commands
        foreach (self::getConfig() as $key => $config) {
            $cmd = self::generateOSRadiatorCmd(
                $eqLogic,
                $key,
                $config['name'],
                $config['type'] ?? 'info',
                $config['subtype'],
                $config['unit'] ?? ''
            );

            self::logInfo('Create default info command "' . $cmd->getName() . '"');

            $cmd->save();
        }

        return true;
    }

    /**
     * Get all key configuration to listen
     *
     * @return string[]
     */
    static public function getKeyConfigToListen(): array
    {
        return [
            self::KEY_TEMPERATURE,
            self::KEY_CONSIGNE,
            self::KEY_MODE,
            self::KEY_SUB_MODE,
            self::KEY_ON_OFF,
            self::KEY_STATE,
            self::KEY_BUTTON_UP,
            self::KEY_BUTTON_DOWN,
            self::KEY_BUTTON_LEFT,
            self::KEY_BUTTON_RIGHT,
            self::KEY_BUTTON_CLICK,
        ];
    }
    /**
     * @return array[]
     */
    static public function getConfig(): array
    {
        return [
            self::KEY_BACKLOG => [
                'key' => 'screenBacklog0',
                ],
            self::KEY_TEMPERATURE => [
                'key' => 'screenTemperature',
            ],
            self::KEY_CONSIGNE => [
                'key' => 'screenConsigne',
            ],
            self::KEY_MODE => [
                'key' => 'screenChauffageMode',
            ],
            self::KEY_SUB_MODE => [
                'key' => 'screenChauffageSousMode',
            ],
            self::KEY_ON_OFF => [
                'key' => 'screenChauffageOnOff',
            ],
            self::KEY_STATE => [
                'key' => 'screenRadiatorState',
            ],
            self::KEY_BUTTON_UP => [
                'key' => 'screenButtonUP',
            ],
            self::KEY_BUTTON_DOWN => [
                'key' => 'screenButtonDOWN',
            ],
            self::KEY_BUTTON_LEFT => [
                'key' => 'screenButtonLEFT',
            ],
            self::KEY_BUTTON_RIGHT => [
                'key' => 'screenButtonRIGHT',
            ],
            self::KEY_BUTTON_CLICK => [
                'key' => 'screenButtonCLICK',
            ],
        ];
    }

    /**
     * @param OSRadiator $eqLogic
     * @param string $logicalId
     * @param string $name
     * @param string $type
     * @param string $subtype
     * @param string $unit
     * @return OSRadiatorCmd
     * @deprecated Use eqLogicAttr configuration instead
     */
    static protected function generateOSRadiatorCmd(
        OSRadiator $eqLogic,
        string $logicalId,
        string $name,
        string $type = 'info',
        string $subtype = '',
        string $unit = ''
    ): OSRadiatorCmd
    {
        $cmd = new OSRadiatorCmd();
        $cmd->setLogicalId($logicalId);
        $cmd->setEqLogic_id($eqLogic->getId());
        $cmd->setName($name);
        $cmd->setType($type);
        $cmd->setIsVisible(1);
        $cmd->setIsHistorized(0);
        $cmd->setSubType($subtype);
        $cmd->setUnite($unit);

        return $cmd;
    }

    /**
     * Call when cmd value listened is updated
     *
     * @param array $options
     * @return void
     */
    static public function dispatchCmdListened(array $options = []): void
    {
        $osRadiatorTarget = OSRadiator::byId($options['eqLogicTargetId']);
        $cmdUpdated = cmd::byId($options['event_id']);

        self::logInfo('Cmd ' . $cmdUpdated->getHumanName() . ' updated. Refresh radiator screen ' . $osRadiatorTarget->getHumanName());
    }

    static public function logDebug($message)
    {
        self::log('DEBUG', $message);
    }

    static public function logError($message)
    {
        self::log('ERROR', $message);
    }

    static public function logInfo($message)
    {
        self::log('INFO', $message);
    }

    static protected function log($level, $message): void
    {
        log::add('OSRadiator', $level, $message);
    }
}