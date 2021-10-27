<?php

interface PluginInterface
{
    public function save();

    public function includeCSS($filename);

    public function includeJS($filename);

    public function domainPath();

    public function htmlPath();

    public function phpPath();

    public function phpPathDB();

    public function getMetadata($key);

    public function setMetadata($key, $value);

    public function getValue($field, $html = true);

    public function label();

    public function name();

    public function description();

    public function author();

    public function email();

    public function type();

    public function website();

    public function position();

    public function version();

    public function releaseDate();

    public function className();

    public function formButtons();

    public function isCompatible();

    public function directoryName();

    public function install($position = 1);

    public function uninstall();

    /**
     * Returns True if the plugin is installed
     *
     * @return boolean
     */
    public function installed(): bool;

    public function workspace();

    public function init();

    public function prepare();

    public function post();

    public function configure($args);

    public function setField($field, $value);

    public function setPosition($position);

    public function webhook($URI = false, $returnsAfterURI = false, $fixed = true);
}