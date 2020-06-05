<?php
//--------------------get form data------------
    //--image upload
    if(file_exists($_FILES['inputImage']['tmp_name']) || is_uploaded_file($_FILES['inputImage']['tmp_name'])) {
        $target_dir = "../images/";
        $img = $target_dir . basename($_FILES["inputImage"]["name"]);
        
        /*if(file_exists($img)){}
        else{                         -move_uploaded_file will replace old file*/
            if (move_uploaded_file($_FILES['inputImage']['tmp_name'], $img)) {} 
            else {
                mysqli_close($dbc);
                die("Sorry, there was an error uploading your image.");
            }
        /*}*/
    }else{ 
        $img = null; 
    }
    
//-------get id from stored article---------
    include 'database.php';

    $query = "SELECT id FROM articlesXML
              ORDER BY insertDateTime DESC
              LIMIT 1";

    $result = mysqli_query($dbc, $query);
    
    if($result == false){
        //mysqli_close($dbc);
        die("select article id query error" . mysqli_error($dbc));
    }else {
        $row = mysqli_fetch_array($result);
        $articleId = $row['id'];
    }

//-------------------set article image-------------
    $query = "  UPDATE articlesXML
                SET image = ?
                WHERE id = ?";

    $stmt = mysqli_stmt_init($dbc);

    //var_dump($img, $articleId);

    mysqli_stmt_prepare($stmt, $query);
    mysqli_stmt_bind_param($stmt, 'si', $img, $articleId);
    if(!mysqli_stmt_execute($stmt)){
        die("insert article query error");
    }


    mysqli_close($dbc);
    header("Location: ../pages/article.php?id=$articleId");
?>