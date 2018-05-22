<?php

if(!defined('CANDY')){
    header('Location: /');
}

$title = get_text('site_name');
$heading = get_text('welcome');
$welcome = get_text('welcome_desc');

get_template('welcome.html');

