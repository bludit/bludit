<?php
$GITHUB_BASE_URL = '__GITHUB_BASE_URL__';
$HOW_TO_EDIT_LINK = '__HOW_TO_EDIT_LINK__';

if (!$Site->homepage()) {
        echo 'This theme need a home page defined, please select one page on <b>Admin panel->Settings->Advanced->Home page</b>';
        exit;
}
