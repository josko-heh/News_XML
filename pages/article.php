<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>AMS</title>
    <link rel="icon" href="../favicon.png" type="image/png" sizes="16x16 32x32">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.2/css/all.css" integrity="sha384-oS3vJWv+0UjzBfQzYUhtDYW+Pj2yciDJxpsK1OYPAYjqT085Qq/1cq5FLXAZQ7Ay" crossorigin="anonymous">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <link rel="stylesheet" href="article.css">
    <link rel="stylesheet" href="style.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>

    <?php
    include '../data/database.php';

    $articleId = $_GET['id'];

    $query =   "SELECT articlesXML.*, categories.name AS cName
                FROM articlesXML
                    JOIN categories ON articlesXML.categoryId = categories.id
                WHERE articlesXML.id = ?";

    $stmt = mysqli_stmt_init($dbc);
    mysqli_stmt_prepare($stmt, $query);
    mysqli_stmt_bind_param($stmt, 'i', $articleId);
    mysqli_stmt_execute($stmt);
    if(!($result = mysqli_stmt_get_result($stmt))){
        die("select article id query error");
    }else {
        $row = mysqli_fetch_array($result);

        $headline = $row['headline'];
        $summary = $row['summary'];
        $story = $row['story'];
        $img = $row['image'];
        $categoryName = $row['cName'];
        $author = $row['author'];
    }

    ?>
</head>
<body>
    <header>
        <div class="wrapper">
            <h1><a id="BBClink" href="index.php"><i class="fas fa-globe-europe"></i>AMS</a></h1>
            <nav>
                <ul>
                    <li><a href="category.php?name=Sport"><i class="fas fa-running"></i><span>Sport</span></a></li>
                    <li><a href="category.php?name=Show"><i class="fas fa-money-bill-alt"></i><span>Show</span></a></li>
                    <li><a href="category.php?name=Science"><i class="fas fa-flask"></i><span>Science</span></a></li>
                    <li><a href="administration.php"><i class="fas fa-file-alt"></i><span>Admin</span></a></li>
                    <li><a href="login.html"><i class="fas fa-sign-in-alt"></i><span>Sign in</span></a></li>
                    <li><a href="newArticle.html"><i class="fas fa-file-alt"></i><span>New article</span></a></li>
                </ul>
            </nav>
        </div>
    </header>
    <div class="color" id="blue"></div>
    <main>
        <div class="wrapper">
            <h2><?php echo strtoupper($categoryName)?></h2>
            <section>
                <h3><?php echo $headline?></h3>
                <?php
                    if(!is_null($img)){ echo '<img src='.$img. ' alt="'.$headline.'">'; }
                ?>
                <summary><?php echo $summary ?></summary>
                <p><?php echo $story?></p>
                <address><?php echo $author?></address>
            </section>
        </div>
    </main>    
    <footer>
        <div class="line-hr wrapper"></div>
        <div class="text wrapper">
            <b>Copyright © <?php echo date("Y")?> AMS.</b>
            <span>
                The AMS is fictional news site. 
                <a href="#">Our approach...</a>
            </span>
        </div>
    </footer>

    <script src="setMainHeight.js"></script>
    <?php mysqli_close($dbc)?>
</body>
</html>