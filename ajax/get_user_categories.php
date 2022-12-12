<?php

if (defined('GLPI_USE_CSRF_CHECK')) {
    $old_GLPI_USE_CSRF_CHECK = GLPI_USE_CSRF_CHECK;
}
define("GLPI_USE_CSRF_CHECK", "0");

require('../../../inc/includes.php');
header("Content-Type: text/html; charset=UTF-8");
Html::header_nocache();

Session::checkLoginUser();

Toolbox::logInFile("GET_USER_CATEGORIES",print_r($_POST,true));
if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['requester_user_id'])) {
    $requester_user_id = (int) trim($_POST['requester_user_id']);
    $user_categories = PluginGroupcategoryGroupcategory::getUserCategories($requester_user_id);

    $results = [];
    foreach ($user_categories as $key => $categorie) {
        $results[] = [
          'id' => $key,
          'text' => $categorie
        ];
    }
    if (!empty($_POST['selectedItilcategoriesId'])) {
        $results = [['id'=>$_POST['selectedItilcategoriesId'],'text'=>$user_categories[$_POST['selectedItilcategoriesId']]]] + $results;
    }
    if (count($results)) {
        echo json_encode($results);
    }
}
Toolbox::logInFile("GET_USER_CATEGORIES",print_r($results,true));
if (isset($old_GLPI_USE_CSRF_CHECK)) {
    define("GLPI_USE_CSRF_CHECK", $old_GLPI_USE_CSRF_CHECK);
}
