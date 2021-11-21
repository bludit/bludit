<?php

class blogx extends Plugin {

    public function init()
    {
        $this->dbFields = array(
            'googleFonts'=>false
        );
    }

    public function form()
    {
        global $L;

        $html  = '<div class="mb-3">';
        $html .= '<label class="form-label" for="googleFonts">'.$L->get('Google Fonts').'</label>';
        $html .= '<select class="form-select" id="googleFonts" name="googleFonts">';
        $html .= '<option value="true" '.($this->getValue('googleFonts')===true?'selected':'').'>'.$L->get('Enabled').'</option>';
        $html .= '<option value="false" '.($this->getValue('googleFonts')===false?'selected':'').'>'.$L->get('Disabled').'</option>';
        $html .= '</select>';
        $html .= '<div class="form-text">'.$L->get('Enable or disable Google fonts.').'</div>';
        $html .= '</div>';

        return $html;
    }

    public function googleFonts()
    {
        return $this->getValue('googleFonts');
    }

}