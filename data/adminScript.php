<?php

if(isset($_POST['deletebtn'])) {
    deleteArticle($dbc, $_GET['id']);
}
else if(isset($_POST['contentbtn'])){
    //------set isArchived------
    if(isset($_POST['checkboxArchive'])){
        $isArchived = true;
    }else { $isArchived = false; }
    
    changeArticle($dbc, $_GET['id'], $_POST['headline'], $_POST['summary'], $_POST['story'], $_POST['category'], $isArchived);
}
/*else if(isset($_POST['picturebtn'])){
    changePicture($dbc, $_GET['id'], $_FILES['inputImage']['tmp_name'], $_FILES["inputImage"]["name"]);
}*/
else if(isset($_GET['pic'])){
    changePicture($dbc, $_GET['id'], $_FILES['changePic']['tmp_name'], $_FILES["changePic"]["name"]);
}
?>