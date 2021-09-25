<?php

class pluginHTMLCode extends Plugin {

    public function init() {
        $this->dbFields = array(
            'head' => '',
            'header' => '',
            'footer' => '',
            'adminHead' => '',
            'adminHeader' => '',
            'adminFooter' => ''
        );
    }

    public function form() {
        global $L;

        $html  = '<h2>' . $L->g('Website') . '</h2>';

        $html .= '<div class="mb-3">';
        $html .= '<label class="form-label" for="head">'.$L->get('Head').'</label>';
        $html .= '<textarea class="form-control" rows="3" name="head" id="head">'.$this->getValue('head').'</textarea>';
        $html .= '<div class="form-text">'.$L->get('insert-code-in-the-website-inside-the-tag-head').'</div>';
        $html .= '</div>';

        $html .= '<div class="mb-3">';
        $html .= '<label class="form-label" for="header">'.$L->get('Header').'</label>';
        $html .= '<textarea class="form-control" rows="3" name="header" id="header">'.$this->getValue('header').'</textarea>';
        $html .= '<div class="form-text">'.$L->get('insert-code-in-the-website-at-the-top').'</div>';
        $html .= '</div>';

        $html .= '<div class="mb-3">';
        $html .= '<label class="form-label" for="footer">'.$L->get('Footer').'</label>';
        $html .= '<textarea class="form-control" rows="3" name="footer" id="footer">'.$this->getValue('footer').'</textarea>';
        $html .= '<div class="form-text">'.$L->get('insert-code-in-the-website-at-the-bottom').'</div>';
        $html .= '</div>';

        $html .= '<h2 class="mt-4">' . $L->g('Admin area') . '</h2>';

        $html .= '<div class="mb-3">';
        $html .= '<label class="form-label" for="adminHead">'.$L->get('Head').'</label>';
        $html .= '<textarea class="form-control" rows="3" name="adminHead" id="adminHead">'.$this->getValue('adminHead').'</textarea>';
        $html .= '<div class="form-text">'.$L->get('insert-code-in-the-admin-area-inside-the-tag-head').'</div>';
        $html .= '</div>';

        $html .= '<div class="mb-3">';
        $html .= '<label class="form-label" for="adminHeader">'.$L->get('Header').'</label>';
        $html .= '<textarea class="form-control" rows="3" name="adminHeader" id="adminHeader">'.$this->getValue('adminHeader').'</textarea>';
        $html .= '<div class="form-text">'.$L->get('insert-code-in-the-admin-area-at-the-top').'</div>';
        $html .= '</div>';

        $html .= '<div class="mb-3">';
        $html .= '<label class="form-label" for="adminFooter">'.$L->get('Header').'</label>';
        $html .= '<textarea class="form-control" rows="3" name="adminFooter" id="adminFooter">'.$this->getValue('adminFooter').'</textarea>';
        $html .= '<div class="form-text">'.$L->get('insert-code-in-the-admin-area-at-the-bottom').'</div>';
        $html .= '</div>';

        return $html;
    }

    public function siteHead()
    {
        return html_entity_decode($this->getValue('head'));
    }

    public function siteBodyBegin()
    {
        return html_entity_decode($this->getValue('header'));
    }

    public function siteBodyEnd()
    {
        return html_entity_decode($this->getValue('footer'));
    }

    public function adminHead()
    {
        return html_entity_decode($this->getValue('adminHead'));
    }

    public function adminBodyBegin()
    {
        return html_entity_decode($this->getValue('adminHeader'));
    }

    public function adminBodyEnd()
    {
        return html_entity_decode($this->getValue('adminFooter'));
    }
}
