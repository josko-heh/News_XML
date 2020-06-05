<?php
$config = require 'config.php';

$dbc = mysqli_connect($config["host"], $config["user"], $config["password"], $config["database"]) 
or die("Error connecting to MySQL server." . mysqli_connect_error());



/**
 * Gets newest articles from database
 * @param string $categoryname Articles from that category will be returned.
 * @param integer $numberOfArticles Number of articles return object will have.
 * @return resultset containing articles from $cateogryName category(case insensitive), there may be zero articles
 */
function getArticles($dbc, $categoryName, $numberOfArticles){
    $categoryName = strtoupper($categoryName);

    $query = "SELECT articlesXML.* FROM articlesXML
              JOIN categories ON articlesXML.categoryId = categories.id
              WHERE UPPER(categories.name) = ? AND isArchived = 0
              ORDER BY date DESC
              LIMIT $numberOfArticles";

    $stmt = mysqli_stmt_init($dbc);
    mysqli_stmt_prepare($stmt, $query);
    mysqli_stmt_bind_param($stmt, 's', $categoryName);
    mysqli_stmt_execute($stmt);
    if(!($articles = mysqli_stmt_get_result($stmt))){
        die("getArticles query error\n" . mysqli_error($dbc));
    }
    
    return $articles;
}

function getAllArticles($dbc, $numberOfArticles){
    $query = "SELECT articlesXML.* FROM articlesXML
              ORDER BY insertDateTime DESC
              LIMIT $numberOfArticles";

    $articles = mysqli_query($dbc, $query);
    
    if($articles == false) {
        mysqli_close($dbc);
        die("getAllArticles query error");
    }else {
        return $articles;
    }
}

/**
 * @param integer $numberOfCategories Number of categories that function will return.
 */
function getRandomCategoriesNames($dbc, $numberOfCategories){
    $query = "SELECT name FROM categories
              ORDER BY RAND()
              LIMIT $numberOfCategories";
    
    $result = mysqli_query($dbc, $query);

    $categoriesNames = array();

    if($result) {
        while($row = mysqli_fetch_array($result)){
            $categoryName = $row['name'];
            array_push($categoriesNames, $categoryName);
        }
    }else{
        mysqli_close($dbc);
        die("getRandomCategoriesNames query error");
    };

    return $categoriesNames;
}

function getCategoryName($dbc, $articleId){
    $query = "  SELECT categories.name 
                FROM categories
                    JOIN articlesXML ON categoryId = categories.id
                WHERE articlesXML.id = ?";
                
    $stmt = mysqli_stmt_init($dbc);
    mysqli_stmt_prepare($stmt, $query);
    mysqli_stmt_bind_param($stmt, 'i', $articleId);
    mysqli_stmt_execute($stmt);
    if(!($result = mysqli_stmt_get_result($stmt))){
        die("getCategoryName query error");
    }

    while($row = mysqli_fetch_array($result)){
        $categoryName = $row['name'];
    }

    return $categoryName;
}


function deleteArticle($dbc, $articleId){
    $query = "  DELETE FROM articlesXML
                WHERE id = ?";

    $stmt = mysqli_stmt_init($dbc);
    mysqli_stmt_prepare($stmt, $query);
    mysqli_stmt_bind_param($stmt, 'i', $articleId);
    if(!mysqli_stmt_execute($stmt)){
        die("deleteArticle query error");
    }
}

function changeArticle($dbc, $id, $headline, $summary, $story, $categoryName, $isArchived){
    date_default_timezone_set('Europe/Zagreb');

    //-----set categoryId------
    $categoryId = NULL;

    $query = "SELECT * FROM categories";
    $result = mysqli_query($dbc, $query);
    
    if($result) {
        $found = false;
        while($row = mysqli_fetch_array($result)){
            if(strcasecmp($categoryName,  $row['name']) == 0){      //tries to find article category(sent from form) in database by name; case insensitive
                $categoryId = $row['id'];
                $found = true; 
            }
        }
        if($found == false){ die("couldn't find category in database"); }
    }else{
        mysqli_close($dbc);
        die("set categoryId query error");
    }
    //------/categoryId-------

    $dateTime = date('Y-m-d H:i:s');


    $query = "  UPDATE articlesXML
                SET headline = ?, summary = ?,
                    story = ?, categoryId = ?,
                    isArchived = ?, insertDateTime = ?
                WHERE id = ?";

    $stmt = mysqli_stmt_init($dbc);
    mysqli_stmt_prepare($stmt, $query);
    mysqli_stmt_bind_param($stmt, 'sssiisi', $headline, $summary, $story, $categoryId, $isArchived, $dateTime, $id);
    if(!mysqli_stmt_execute($stmt)){
        die("changeArticle query error");
    }
}

function changePicture($dbc, $id, $tmpImgFile, $imgFile){

    if(file_exists($tmpImgFile) || is_uploaded_file($tmpImgFile)) {
        $target_dir = "../images/";
        $img = $target_dir . basename($imgFile);
        
        /*if(file_exists($img)){}
        else{                         -move_uploaded_file will replace old file*/
            if (move_uploaded_file($tmpImgFile, $img)) {} 
            else {
                mysqli_close($dbc);
                die("Sorry, there was an error uploading your file.");
            }
        /*}*/
    }else{ 
        $img = null; 
    }


    $query = "  UPDATE articlesXML
                SET image = ?
                WHERE id = ?";

    $stmt = mysqli_stmt_init($dbc);
    mysqli_stmt_prepare($stmt, $query);
    mysqli_stmt_bind_param($stmt, 'si', $img, $id);
    if(!mysqli_stmt_execute($stmt)){
        die("change article image query error");
    }
}

function getPermissionId($dbc, $level){
    $level = strtoupper($level);
    $query = "SELECT id FROM permissions
              WHERE UPPER(level) = ?";

    $stmt = mysqli_stmt_init($dbc);
    mysqli_stmt_prepare($stmt, $query);
    mysqli_stmt_bind_param($stmt, 's', $level);
    mysqli_stmt_execute($stmt);
    if(!($result = mysqli_stmt_get_result($stmt))){
        die("getPermissionId query error");
    }else {
        $row = mysqli_fetch_array($result);
        return $row['id'];
    }
}
function getLevel($dbc, $permissionId){
    $query = "SELECT level FROM permissions
              WHERE id = ?";

    $stmt = mysqli_stmt_init($dbc);
    mysqli_stmt_prepare($stmt, $query);
    mysqli_stmt_bind_param($stmt, 'i', $permissionId);
    mysqli_stmt_execute($stmt);
    if(!($result = mysqli_stmt_get_result($stmt))){
        die("getLevel query error");
    }else {
        $row = mysqli_fetch_array($result);
        return $row['level'];
    }
}
?>