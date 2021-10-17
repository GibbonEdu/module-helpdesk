<?php

namespace Gibbon\Module\HelpDesk\Data;

use Gibbon\Domain\System\SettingGateway;
use Gibbon\Forms\Form;

/**
 * Settings Manager to easily create settings.
 */
class SettingManager
{

    protected $settingGateway;
    protected $scope;
    protected $settings = [];

    public function __construct(SettingGateway $settingGateway, $scope) {
        $this->settingGateway = $settingGateway;
        $this->scope = $scope;
    }

    public function form($processPage) {
        $form = Form::create('settings', $processPage);

        foreach ($this->settings as $setting) {
            $settingData = $this->settingGateway->getSettingByScope($this->scope, $setting->getName(), true);
            $row = $form->addRow();
            $setting->render($settingData, $row); 
        }

        $row = $form->addRow();
            $row->addFooter();
            $row->addSubmit();

        return $form;
    }

    public function process($formData) {
        $return = 'success0';

        foreach ($this->settings as $setting) {
            $data = $formData[$setting->getName()] ?? null;

            $data = $setting->process($data);

            if ($data === false) {
                $return = 'warning1';
                continue;
            }

            if (!$this->settingGateway->updateSettingByScope($this->scope, $setting->getName(), $data)) {
                $return = 'warning1';
            }
        }

        return $return;
    } 

    /**
     * Add a new Setting.
     * @param Setting name.
     * @return new Setting object.
     */
    public function addSetting($name) {
        $setting = new Setting($name);
        $this->settings[] = $setting;
        return $setting;
    }

    /**
     * Get Settings.
     * @return Array of Settings.
     */
    public function getSettings() {
        return $this->settings;
    }
}