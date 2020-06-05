<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>AMS</title>
    <link rel="icon" href="../favicon.png" type="image/png" sizes="16x16 32x32">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.2/css/all.css" integrity="sha384-oS3vJWv+0UjzBfQzYUhtDYW+Pj2yciDJxpsK1OYPAYjqT085Qq/1cq5FLXAZQ7Ay" crossorigin="anonymous">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <link rel="stylesheet" href="index.css">
    <link rel="stylesheet" href="style.css">

    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    
    <?php
    include '../data/database.php';
    /**
     * Prints out Categories(sections) and their articles in html.
     * @param integer $showCategories Determines how many categories(sections) will be displayed.
     */
    function displayCategories($dbc, $showCategories){
        $showArticles = 3; //number of articles per section

        function tab($num){
            $tabs = "";
            for ($i = 0; $i < $num; $i++) {
                $tabs .= "\t";
            }
            return $tabs;
        }


        $categoriesNames = getRandomCategoriesNames($dbc, $showCategories);

        for ($i=0; $i < $showCategories; $i++) {
            //---vertical line colors
            if ($categoriesNames[$i] == "Sport") {
                $color = "red";
            } else if ($categoriesNames[$i] == "Show") {
                $color = "yellow";
            } else if ($categoriesNames[$i] == "Science") {
                $color = "green";
            } else {
                $color = "blue";
            }

            $articles = getArticles($dbc, $categoriesNames[$i], $showArticles);

            if ($i == 0) {
                echo '<hr/>' . "\r\n";
            } else {
                echo tab(3) . '<hr/>' . "\r\n";
            }
            echo
                tab(3) . '<section>' . "\r\n" .
                tab(4) . '<div class="line-vert ' . $color . '"></div>' . "\r\n" .
                tab(4) . '<a href="category.php?name=' . $categoriesNames[$i] . '"><h3>' . $categoriesNames[$i] . '</h3></a>' . "\r\n" .
                tab(4) . '<div class="row">' . "\r\n";
            //--print articles--
            for ($j=0; $j<$showArticles; $j++) { 
                if($article = mysqli_fetch_array($articles)){   //if there are articles left in $articles
                    echo 
                        tab(5) . '<article class="col">' . "\r\n" .
                            tab(6) . '<a href="article.php?id=' . $article['id'] . '">' . "\r\n" .
                                tab(7) . '<div>' . "\r\n" .
                                    tab(8) . '<div>' . "\r\n" .
                                        tab(9) . '<img src="' . $article['image'] . '" class="initial">' . "\r\n" .
                                    tab(8) . '</div>' . "\r\n" .
                                tab(7) . '</div>' . "\r\n" .
                                tab(7) . '<h4>' . $article['headline'] . '</h4>' . "\r\n" .
                                tab(7) . '<p>' . $article['summary'] . '</p>' . "\r\n" .
                            tab(6) . '</a>' . "\r\n" .
                        tab(5) . '</article>' . "\r\n";            
                }
            }
            echo
                    tab(4) . '</div>' . "\r\n" .
                tab(3) . '</section>' . "\r\n";
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
                    <li><a href="administration.php"><i class="fas fa-user-cog"></i><span>Admin</span></a></li>
                    <li><a href="login.html"><i class="fas fa-sign-in-alt"></i><span>Sign in</span></a></li>
                    <li><a href="newArticle.html"><i class="fas fa-file-alt"></i><span>New article</span></a></li>
                </ul>
            </nav>
        </div>
    </header>
    <main>
        <div class="wrapper">
            <div class="animated">
                <span>Welcome to AMS.com</span>
            </div>
            <time><?php echo date("l, j M") ?></time>
            <?php displayCategories($dbc, 2);?>
        </div>
    </main>
    <footer>
        <div class="line-hr wrapper"></div>
        <div class="text wrapper">
            <b>Copyright © <?php echo date("Y") ?> AMS.</b>
            <span>
                The AMS is fictional news site.
                <a href="#">Our approach...</a>
            </span>
        </div>
    </footer>

    <script>
    $(window).on("load", function() {
        let j = 0;
        let section = null;
        while(section = document.querySelector("main section:nth-of-type("+(j+1)+")")){

            let i=0;
            let img = null;
            while (img = section.querySelector("article:nth-of-type("+(i+1)+") img")) {
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