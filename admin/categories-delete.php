<?php

require '../config/function.php';

$paraRestultId = checkParamId('id');
if(is_numeric($paraRestultId)){

    $categoryId = validate($paraRestultId);

    $category = getById('categories',$categoryId);
    if($category['status'] == 200)
    {
        $categoryDeleteRes = delete('categories', $categoryId);
        if($categoryDeleteRes)
        {
            redirect('categories.php','Category Deleted Successfully.');
        }
        else
        {
            redirect('categories.php','Something Went Wrong.');
        }
    }
    else
    {
        redirect('categories.php', $category['message']);
    }

}else{
    redirect('categories.php', 'Something Went Wrong.');
}

?>