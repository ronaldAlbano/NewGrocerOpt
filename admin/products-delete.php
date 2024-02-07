<?php

require '../config/function.php';

$paraRestultId = checkParamId('id');
if(is_numeric($paraRestultId)){

    $productId = validate($paraRestultId);

    $product = getById('products',$productId);
    if($product['status'] == 200)
    {
        $productDeleteRes = delete('products', $productId);
        if($productDeleteRes)
        {
            $deleteImage ="../".$product['data']['image'];
            if(file_exists($deleteImage)){
                unlink($deleteImage);
            }
            redirect('products.php','Product Deleted Successfully.');
        }
        else
        {
            redirect('products.php','Something Went Wrong.');
        }
    }
    else
    {
        redirect('products.php', $category['message']);
    }

}else{
    redirect('products.php', 'Something Went Wrong.');
}

?>