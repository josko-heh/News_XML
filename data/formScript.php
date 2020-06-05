<?php 
include 'database.php';

//check if username exists
if (isset($_POST['username_check'])) {
	$username = json_decode($_POST['usernameData'], true)["usernameKey"];


	$query = "SELECT * FROM users WHERE username = ?";

	$stmt = mysqli_stmt_init($dbc);
    mysqli_stmt_prepare($stmt, $query);
    mysqli_stmt_bind_param($stmt, 's', $username);
    mysqli_stmt_execute($stmt);
    if(!($result = mysqli_stmt_get_result($stmt))){
		echo 'error';
        die("username check query error");
    }
	else if(mysqli_num_rows($result) > 0) {
		echo "taken";	
	}else{
		echo 'not_taken';
	}
}

//register user
else if (isset($_POST['save'])) {
	$regArr = json_decode($_POST['regData'], true);
	$username = $regArr["usernameKey"];


	$query = "SELECT * FROM users WHERE username = ?";

	$stmt = mysqli_stmt_init($dbc);
    mysqli_stmt_prepare($stmt, $query);
    mysqli_stmt_bind_param($stmt, 's', $username);
    mysqli_stmt_execute($stmt);
    if(!($result = mysqli_stmt_get_result($stmt))){
		echo 'error';
        die("save->username query error");
    }
	else if (mysqli_num_rows($result) > 0) {
		echo "exists";	
	}
	else{
		$permissionId = getPermissionId($dbc, "user");
		$name =  $regArr["nameKey"];
		$surname = $regArr["surnameKey"];
		$hashedPassword =  password_hash($regArr["passwordKey"], CRYPT_BLOWFISH);

		$query = "INSERT INTO users (name, surname, username, password, permissionId) VALUES (?, ?, ?, ?, ?)";

		$stmt = mysqli_stmt_init($dbc);
		
		mysqli_stmt_prepare($stmt, $query);
		mysqli_stmt_bind_param($stmt, 'ssssd', $name, $surname, $username, $hashedPassword, $permissionId);
		if(mysqli_stmt_execute($stmt)){
			echo 'Saved!';
		}else {
			echo 'error';
			die("insert user query error");
		}
	}
}

else if (isset($_POST['login'])) {
	$loginArr = json_decode($_POST['loginData'], true);
	$username = $loginArr["usernameKey"];

	$query = "SELECT * FROM users WHERE username = ?";

	$stmt = mysqli_stmt_init($dbc);
    mysqli_stmt_prepare($stmt, $query);
    mysqli_stmt_bind_param($stmt, 's', $username);
    mysqli_stmt_execute($stmt);
    if(!($result = mysqli_stmt_get_result($stmt))){
		echo 'error';
        die("login->username query error");
    }
	else if (mysqli_num_rows($result) == 0) {
		echo "failed_login";
		exit();	
	}
	else{
		$hashedPassword =  password_hash($loginArr["passwordKey"], CRYPT_BLOWFISH);

		$query = "	SELECT username, password, permissionId FROM users 
					WHERE username = ?";

		$stmt = mysqli_stmt_init($dbc);
		mysqli_stmt_prepare($stmt, $query);
		mysqli_stmt_bind_param($stmt, 's', $username);
		mysqli_stmt_execute($stmt);
		if(!($result = mysqli_stmt_get_result($stmt))){
			echo 'error';
			die("select user query error");
		}
		else if(mysqli_num_rows($result) > 0){
			$row = mysqli_fetch_array($result);
			$username = $row['username'];
			$passwordDB = $row['password'];
			$permissionId = $row['permissionId'];
		}else{
			echo "failed_login";
			exit();
		}

		if (password_verify($loginArr["passwordKey"], $passwordDB)) {
			$uspjesnaPrijava = true;

			if (session_status() == PHP_SESSION_NONE) {
				session_start();
			}
			$_SESSION['username'] = $username;
			$_SESSION['level'] = getLevel($dbc, $permissionId);
			
			echo 'logged-in,' . $username;
		}else {
			echo 'failed_login';
		}
	}
}

else if (isset($_POST['newArticle'])) {
	$xml = html_entity_decode($_POST['articleData'], ENT_XML1);

	//---For more detailed feedback from DOMDocument::schemaValidate, disable libxml errors and fetch error information
		function libxml_display_error($error){
			$return = trim($error->message);
			/*if ($error->file) {
				$return .=    " in <b>$error->file</b>";
			}
			$return .= " on line <b>$error->line</b>\n";*/

			return $return;
			}

		function libxml_display_errors() {
			$errors = libxml_get_errors();
			foreach ($errors as $error) {
				echo strstr(libxml_display_error($error), " The value");
			}
			libxml_clear_errors();
		}

		// Enable user error handling
		libxml_use_internal_errors(true);

	$xmlArticleDOM = new DOMDocument();
	$xmlArticleDOM->loadXML($xml);
	if(!$xmlArticleDOM->schemaValidate('article.xsd')){
		libxml_display_errors();
		exit();
	}

	libxml_use_internal_errors(false);

	$xmlArticle = simplexml_load_string($xml) or die("Error: Cannot create xml object");
	$author = $xmlArticle -> Author;
	$headline = $xmlArticle -> Headline;
    $summary = $xmlArticle -> Summary;
	$story = $xmlArticle -> Story;
	$date = $xmlArticle -> Date;
	$archived = $xmlArticle -> attributes() -> archived;
	$categoryName = $xmlArticle -> attributes()-> category;

//-------------------storing article in database-------------
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

    //------set isArchived------
    if($archived == "yes"){
        $isArchived = true;
	}else { $isArchived = false; }
	
	date_default_timezone_set('Europe/Zagreb');
	$insertDateTime = date('Y-m-d H:i:s');

    $query = "INSERT INTO articlesXML (headline, summary, story, categoryId, author, isArchived, date, insertDateTime) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = mysqli_stmt_init($dbc);

    mysqli_stmt_prepare($stmt, $query);
    mysqli_stmt_bind_param($stmt, 'sssisiss', $headline, $summary, $story, $categoryId, $author, $isArchived, $date, $insertDateTime);
    if(!mysqli_stmt_execute($stmt)){
        die("insert article query error\n" . mysqli_error($dbc));
    }
//-------get id from stored article and open it in new page---------
    $query = "SELECT id FROM articlesXML
              ORDER BY insertDateTime DESC
              LIMIT 1";

    $result = mysqli_query($dbc, $query);
    
    if($result == false){
        //mysqli_close($dbc);
        die("select article id query error\n" . mysqli_error($dbc));
    }else {
        $row = mysqli_fetch_array($result);
        $articleId = $row['id'];
    }

	mysqli_close($dbc);
	echo $articleId;
}
?>