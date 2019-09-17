<?php

require('../../../inc/includes.php');

header("Content-Type: text/html; charset=UTF-8");
Html::header_nocache();

Session::checkLoginUser();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['requester_user_id'])
) {
    $requester_user_id = (int) trim($_POST['requester_user_id']);
    $user_categories = PluginGroupcategoryGroupcategory::getUserCategories($requester_user_id);

    $results = [];
    foreach ($user_categories as $key => $categorie) {
        $results[] = [
          'id' => $key,
          'text' => $categorie
        ];
    }
    if(count($results)){
        echo json_encode($results);
    }

}
