<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>AMS</title>
    <link rel="icon" href="../favicon.png" type="image/png" sizes="16x16 32x32">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.2/css/all.css" integrity="sha384-oS3vJWv+0UjzBfQzYUhtDYW+Pj2yciDJxpsK1OYPAYjqT085Qq/1cq5FLXAZQ7Ay" crossorigin="anonymous">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <link rel="stylesheet" href="category.css">
    <link rel="stylesheet" href="style.css">

    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>

    <?php
    include '../data/database.php';
    $categoryName = $_GET['name'];

    /**
     * Prints out articles from $categoryName category.
     */
    function displayArticles($dbc, $categoryName){
        $showArticles = 2; //number of articles per row

        function tab($num){
            $tabs = "";
            for ($i = 0; $i < $num; $i++) {
                $tabs .= "\t";
            }
            return $tabs;
        }

        $articles = getArticles($dbc, $categoryName, 20);

        $articleCounter = 0;
        for ($i=0; $i < mysqli_num_rows($articles); $i++) {
            
            if($articleCounter == 0){
                echo
                    tab(3) . '<div class="row">' . "\r\n";
            }

            if($article = mysqli_fetch_array($articles)){   //if there are articles left in $articles
                echo
                    tab(4) . '<article class="col">' . "\r\n" .
                        tab(5) . '<a href="article.php?id=' . $article['id'] . '">' . "\r\n" .
                            tab(6) . '<div>' . "\r\n" .
                                tab(7) . '<div>' . "\r\n" .
                                tab(8) . '<img src="' . $article['image'] . '" class="initial">' . "\r\n" .
                                tab(7) . '</div>' . "\r\n" .
                            tab(6) . '</div>' . "\r\n" .
                            tab(6) . '<h4>' . $article['headline'] . '</h4>' . "\r\n" .
                            tab(6) . '<p>' . $article['summary'] . '</p>' . "\r\n" .
                        tab(5) . '</a>' . "\r\n" .
                    tab(4) . '</article>' . "\r\n";
                    
                $articleCounter++;
            }

            if(mysqli_num_rows($articles) < $showArticles){
                if ($articleCounter == mysqli_num_rows($articles)) {
                    echo tab(3) . '</div>' . "\r\n";
                    break;
                }
            }else{
                if ($articleCounter == $showArticles) {
                    echo tab(3) . '</div>' . "\r\n";
                    $articleCounter = 0;
                }
            }
        }
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
            <?php displayArticles($dbc, $categoryName) ?>
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

    <script>
    $(window).on("load", function() {
        let j = 0;
        let row = null;
        while(row = document.querySelector("main div[class='row']:nth-of-type("+(j+1)+")")){

            let i=0;
            let img = null;
            while (img = row.querySelector("article:nth-of-type("+(i+1)+") img")) {
                if(img.naturalWidth >= img.naturalHeight){
                    img.classList.remove("initial");
                    img.classList.add("horizontal");

                    maxWidth = 200 * img.naturalWidth / img.naturalHeight;
                    maxWidth = maxWidth.toFixed(2);
                    img.style.maxWidth = maxWidth.toString() + "px";
                }else{
                    img.classList.remove("initial");
                    img.classList.add("vertical");
                }

                i++;
            }

            j++;
        }
    });
    </script>
    <?php mysqli_close($dbc);?>
</body>
</html>