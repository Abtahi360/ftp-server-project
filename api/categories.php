<?php

    header('Content-Type: application/json');
    require_once('/../models/categoryModel.php');


    $categories = getTopCategories();

    if ($categories !== false) {
    echo json_encode(['success'=>true, 'categories'=>$categories]);
    } else {
        echo json_encode(['success'=>false, 'error'=>'Failed to fetch categories..!']);
    }


?>
