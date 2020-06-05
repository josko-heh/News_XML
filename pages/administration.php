<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>AMS</title>
    <link rel="icon" href="../favicon.png" type="image/png" sizes="16x16 32x32">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.2/css/all.css" integrity="sha384-oS3vJWv+0UjzBfQzYUhtDYW+Pj2yciDJxpsK1OYPAYjqT085Qq/1cq5FLXAZQ7Ay" crossorigin="anonymous">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <link rel="stylesheet" href="administration.css">
    <link rel="stylesheet" href="category.css">
    <link rel="stylesheet" href="style.css">

    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>

    <?php
    session_start();
    if(isset($_SESSION['level'])){
        if($_SESSION['level'] != "admin"){
            $hasAccess = false;
        }else{
            $hasAccess = true;

            include '../data/database.php';
            include '../data/adminScript.php';

            /**
             * Prints out articles(archived articles included) in html.
             */
            function displayAllArticles($dbc){
                $showArticles = 3; //number of articles per row

                $articles = getAllArticles($dbc, 30);

                $articleCounter = 0;
                for ($i = 0; $i < mysqli_num_rows($articles); $i++) {

                    if ($articleCounter == 0) {
                        echo
                        tab(4) . '<div class="row">' . "\r\n";
                    }

                    if ($article = mysqli_fetch_array($articles)) { //if there are articles left in $articles
                        
                        $categoryName = getCategoryName($dbc, $article['id']);

                        echo
                            tab(4). '<article class="col">' . "\r\n" .
                            tab(5).    '<a href="article.php?id=' . $article['id'] . '">' . "\r\n" .
                            tab(6).        '<div>' . "\r\n" .
                            tab(7).            '<div>' . "\r\n" .
                            tab(8).                '<img src="' . $article['image'] . '" class="initial">' . "\r\n" .
                            tab(7).            '</div>' . "\r\n" .
                            tab(6).        '</div>' . "\r\n" .
                            tab(6).        '<h4>' . $article['headline'] . '</h4>' . "\r\n" .
                            tab(5).    '</a>' . "\r\n" .

                            tab(5).    '<div class="buttons">' . "\r\n" .
                            tab(6).         '<button type="button" class="btn btn-outline-primary btn-sm" data-toggle="modal" data-target=".modal'. $article['id']. '">Change content</button>' . "\r\n" .
                            tab(6).         '<form action="administration.php?id=' . $article['id'] . '&pic" method="POST" enctype="multipart/form-data">' . "\r\n" .
                            tab(7).             '<label class="btn btn-outline-primary btn-sm">' . "\r\n" .
                            tab(8).                 '<input type="file" name="changePic" accept="image/*" alt="Article image" onchange="form.submit()">' . "\r\n" .
                            tab(7).                 'Change picture' . "\r\n" .
                            tab(7).             '</label>' . "\r\n" .
                            tab(6).         '</form>' . "\r\n" .
                            tab(6).         '<form action="administration.php?id=' . $article['id'] . '" method="POST">' . "\r\n" .
                            tab(7).             '<button type="submit" name="deletebtn" class="btn btn-outline-primary btn-sm btn-block" onclick="return confirm(\'Do you really want to delete this article?\')">Delete</button>' . "\r\n" .
                            tab(6).         '</form>' . "\r\n" .
                            tab(5).    '</div>' . "\r\n\r\n" .

                            tab(5).    '<div class="modal fade modal'. $article['id']. '" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">' . "\r\n" .
                            tab(6).        '<div class="modal-dialog modal-lg">' . "\r\n" .
                            tab(7).            '<div class="modal-content">' . "\r\n" .
                            tab(8).                '<form action="administration.php?id=' . $article['id'] . '" method="post">' . "\r\n" .
                            tab(9).                    '<label for="headline">Headline</label>' . "\r\n" .
                            tab(9).                    '<input type="text" name="headline" class="form-control headline" aria-describedby="basic-addon1" value="'. $article['headline']. '">' . "\r\n\r\n" .
                            tab(9).                    '<div id="headlineAlert" class="alert alert-primary collapse" role="alert">' . "\r\n" .
                            tab(10).                        'Headline must be between 3 and 50 characters long!' . "\r\n" .
                            tab(9).                    '</div>' . "\r\n\r\n" .

                            tab(9).                    '<label for="summary">Brief summary</label>' . "\r\n" .
                            tab(9).                    '<textarea name="summary" class="form-control summary" rows="2" aria-describedby="basic-addon1">'. $article['summary']. '</textarea>' . "\r\n\r\n" .
                            tab(9).                    '<div id="summaryAlert" class="alert alert-primary collapse" role="alert">' . "\r\n" .
                            tab(10).                         'Summary must be between 3 and 200 characters long!' . "\r\n" .
                            tab(9).                    '</div>' . "\r\n\r\n" .

                            tab(9).                    '<label for="story">Story</label>' . "\r\n" .
                            tab(9).                    '<textarea name="story" class="form-control" rows="5" aria-describedby="basic-addon1">'. $article['story']. '</textarea>' . "\r\n\r\n" .
                            tab(9).                    '<div id="storyAlert" class="alert alert-primary collapse" role="alert">' . "\r\n" .
                            tab(10).                        'Story must be between 3 and 8000 characters long!' . "\r\n" .
                            tab(9).                    '</div>' . "\r\n\r\n" .

                            tab(9).                    '<label for="category">Choose category</label>' . "\r\n" .
                            tab(9).                    '<select class="custom-select" name="category" required>' . "\r\n";
                                                            if(strtoupper($categoryName) == "SPORT"){
                                                                echo
                                                                    tab(10). '<option value="Sport" selected>Sport</option>' . "\r\n" .
                                                                    tab(10). '<option value="Show">Show</option>' . "\r\n" .
                                                                    tab(10). '<option value="Science">Science</option>' . "\r\n";
                                                            }elseif(strtoupper($categoryName) == "SHOW"){
                                                                echo 
                                                                    tab(10). '<option value="Sport">Sport</option>' . "\r\n" .
                                                                    tab(10). '<option value="Show" selected>Show</option>' . "\r\n" .
                                                                    tab(10). '<option value="Science">Science</option>' . "\r\n";
                                                            }elseif(strtoupper($categoryName) == "SCIENCE"){
                                                                echo
                                                                    tab(10). '<option value="Sport">Sport</option>' . "\r\n" .
                                                                    tab(10). '<option value="Show">Show</option>' . "\r\n" .
                                                                    tab(10). '<option value="Science" selected>Science</option>' . "\r\n";
                                                            }else{
                                                                echo
                                                                    tab(10). '<option value="" selected disabled>Choose another category</option>' . "\r\n" .
                                                                    tab(10). '<option value="Sport">Sport</option>' . "\r\n" .
                                                                    tab(10). '<option value="Show">Show</option>' . "\r\n" .
                                                                    tab(10). '<option value="Science">Science</option>' . "\r\n";
                                                            }
                        echo
                            tab(9).                    '</select>'. "\r\n\r\n" .

                            tab(9).                    '<label class="form-check-label" data-toggle="tooltip" data-placement="right" title="Save article in database, but don\'t show it in news">' . "\r\n" .
                            tab(10).                        '<input type="checkbox" name="checkboxArchive" class="checkboxArchive" >' . "\r\n" .
                            tab(10).                        'Archive' . "\r\n" .
                            tab(9).                    '</label>' . "\r\n" .
                            tab(9).                    '<br>' . "\r\n\r\n" .

                            tab(9).                    '<input type="reset" name="reset" value="Reset" onclick="resetForm('. $article['id']. ')" class="btn btn-primary">' . "\r\n" .
                            tab(9).                    '<input type="submit" name="contentbtn" value="Submit" onclick="validate(event, '. $article['id']. ')" class="btn btn-primary">' . "\r\n" .
                            tab(8).                '</form>' . "\r\n" .
                            tab(7).            '</div>' . "\r\n" .
                            tab(6).        '</div>' . "\r\n" .
                            tab(5).    '</div>' . "\r\n\r\n" .

                            tab(4) . '</article>' . "\r\n";

                        $articleCounter++;
                    }

                    if (mysqli_num_rows($articles) < $showArticles) {
                        if ($articleCounter == mysqli_num_rows($articles)) {
                            echo tab(3) . '</div>' . "\r\n";
                            break;
                        }
                    } else {
                        if ($articleCounter == $showArticles) {
                            echo tab(3) . '</div>' . "\r\n";
                            $articleCounter = 0;
                        }
                    }
                }
            }
        }
    }else {
        $hasAccess = false;
    }


    function tab($num){
        $tabs = "";
        for ($i = 0; $i < $num; $i++) {
            $tabs .= "\t";
        }
        return $tabs;
    }

    function displayNoAccess(){
        if (isset($_SESSION['username'])) {
            echo
                        '<div id="noAccessAlert" class="alert alert-warning" role="alert">'. "\r\n".
                tab(4).     $_SESSION['username']. ' can\'t access Administration page! You must have admin permission level. <a href="login.html">Sign in.</a>'. "\r\n".
                tab(3). '</div>'. "\r\n";
        }else{
            echo
                        '<div id="noAccessAlert" class="alert alert-warning" role="alert">'. "\r\n".
                tab(4).     'You can\'t access Administration page! You must have admin permission level. <a href="login.html">Sign in.</a>'. "\r\n".
                tab(3). '</div>'. "\r\n";
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
            <h2>ADMINISTRATION</h2>
            <?php
                if($hasAccess){
                    displayAllArticles($dbc);
                }else {
                    displayNoAccess();
                }
            ?>
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
        let row = null;
        while (row = document.querySelector("main div[class='row']:nth-of-type(" + (j + 1) + ")")) {

            let i = 0;
            let img = null;
            while (img = row.querySelector("article:nth-of-type(" + (i + 1) + ") > a img")) {
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

    function sff(){
        console.log("sff");
    }
    function validate(event, id) {
        $('.alert').hide();

        var sendForm = true;

        var headline = document.querySelector(".modal" + id + " input[name='headline']").value.trim();
        if (headline.length < 3 || headline.length > 50) {
            sendForm = false;
            $('.modal' + id + ' div[id="headlineAlert"]').show();
        }

        var summary= document.querySelector(".modal" + id + " textarea[name='summary']").value.trim();
        if (summary.length < 3 || summary.length > 200) {
            sendForm = false;
            $('.modal' + id + ' div[id="summaryAlert"]').show();
        }

        var story = document.querySelector(".modal" + id + " textarea[name='story']").value.trim();
        if (story.length < 3 || story.length > 8000) {
            sendForm = false;
            $('.modal' + id + ' div[id="storyAlert"]').show();
        }


        if (sendForm == false) {
            event.preventDefault();
        }
    }

    function resetForm(id) {
        $('.modal' + id + ' .alert').hide();
    }
    </script>
    <?php 
        if($hasAccess == false){
            echo '<script src="setMainHeight.js"></script>'; 
        }
        if(isset($dbc)){
            mysqli_close($dbc); 
        }
    ?>
</body>
</html>