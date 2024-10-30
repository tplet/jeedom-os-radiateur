<?php

require_once __DIR__ . '/OSRadiator.class.php';
require_once __DIR__ . '/OSRadiatorCmd.php';


class OSRadiatorService
{
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
        foreach (self::generateDefaultCommandsConfig() as $key => $config) {
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
     * @return array[]
     */
    static protected function generateDefaultCommandsConfig(): array
    {
        return [
            'backlog' => [
                'name' => 'Backlog',
                'type' => 'action',
                'subtype' => 'other',
            ],
            'mode' => [
                'name' => 'Mode',
                'subtype' => 'string',
            ],
            'submode' => [
                'name' => 'Sous-mode',
                'subtype' => 'string',
            ],
            'onoff' => [
                'name' => 'On/Off',
                'subtype' => 'string',
            ],
            'temperature' => [
                'name' => 'Température',
                'subtype' => 'numeric',
                'unit' => '°C',
            ],
            'target' => [
                'name' => 'Consigne',
                'subtype' => 'numeric',
                'unit' => '°C',
            ],
            'radiator-state' => [
                'name' => 'Etat radiateur',
                'subtype' => 'string',
            ],
            'joystick-UP' => [
                'name' => 'Joystick-UP',
                'subtype' => 'string',
            ],
            'joystick-DOWN' => [
                'name' => 'Joystick-DOWN',
                'subtype' => 'string',
            ],
            'joystick-LEFT' => [
                'name' => 'Joystick-LEFT',
                'subtype' => 'string',
            ],
            'joystick-RIGHT' => [
                'name' => 'Joystick-RIGHT',
                'subtype' => 'string',
            ],
            'joystick-CLICK' => [
                'name' => 'Joystick-CLICK',
                'subtype' => 'string',
            ],
        ];
    }

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