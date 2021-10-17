<?php

namespace Gibbon\Module\HelpDesk\Data;

/**
 * Helper Class to rendering and processing of settings.
 */
class Setting
{
    protected $name;
    protected $row = true;
    protected $renderCallback;
    protected $processCallback;

    public function __construct($name) {
        $this->name = $name;
    }

    /**
     * Get Setting name.
     * @return Setting name.
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Get row flag.
     * @return True if render as row, false if column.
     */
    public function isRow() {
        return $this->row;
    }

    /**
     * Set row flag.
     * @param boolean  $row (optional) Set to True if render as row, False if render as column.
     * @return self
     */
    public function setRow($row = true) {
        $this->row = $row;

        return $this;
    }

    /**
     * Render setting into form row using gibbonSetting data.
     * @param array  $data Row data retrieved from gibbonSetting.
     * @param object  $row Form row object
     */
    public function render($data, $row) {
        if (!$this->isRow()) {
            $row = $row->addColumn();
        }

        $row->addLabel($data['name'], __($data['nameDisplay']))
            ->description($data['description']);
        
        if (!empty($this->renderCallback) && is_callable($this->renderCallback)) {
            call_user_func($this->renderCallback, $data, $row);
        }
    }

    /**
     * Set the renderer for this setting.
     * @param callable  $callable Renderer callable
     * @return self
     */
    public function setRenderer(callable $callable) {
        $this->renderCallback = $callable;

        return $this;
    }
    
    /**
     * Process setting data for entry into database.
     * @param object  $data Data from POST
     * @return String to insert into database, false if error.
     */
    public function process($data) {
        return !empty($this->processCallback) && is_callable($this->processCallback) ? call_user_func($this->processCallback, $data) : false;
    }

    /**
     * Set the processor for this setting.
     * @param callable  $callable Processor callable.
     * @return self
     */
    public function setProcessor(callable $callable) {
        $this->processCallback = $callable;

        return $this;
    }
}