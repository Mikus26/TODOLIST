<?php
    session_start();

    // J'inclus une connexion vers la BDD
    include('./model/db_connexion.php');

    /**
     * Je vérifie si j'ai bien un userID dans la session, sinon je redirige vers index.php
     */
    if(!isset($_SESSION['userID'])){
        header('location:index.php');
        exit;
    }
    else{
        $userID = $_SESSION['userID'];
    }

    /** En BDD, je récupère tous les articles triés du plus récent au plus ancien dont l'utilisateur est l'auteur. Ils sont :
     * - Trier du plus récent au plus ancien
     * - l'utilisateur doit être l'auteur
    */
    $rqt = 'SELECT id, title FROM article WHERE author = :userID ORDER BY creation_date DESC';
    $db_statement = $db_connexion->prepare($rqt);
    $db_statement->execute(
        array(
            ':userID' => $userID
        )
    );
    $datas = $db_statement->fetchAll();

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Ceci est la page où vous retouverez la liste de tous vos articles.">
    <title>Retouver la liste de tous vos articles</title>
    <link rel="stylesheet" href="/styles/mainCss.css">
</head>
<body>
    <!-- Ma navBar est importée -->
    <?php 
        require_once('./includes/navBar.php'); 
    ?>

    <!-- Ma todoList_navBar est importée -->
    <?php 
        require_once('./includes/todoList_navBar.php'); 
    ?>

    <!-- Afficher tous les articles du plus récent au plus ancien dont l'utilisateur est l'auteur -->
    <?php 
        foreach($datas as $data){
            echo('
                <div class="card">
                    <a href="read_article.php?id='. $data['id'] .'" class="article">
                        <div class="info">
                            '. $data['title'] .'
                        </div>
                    </a>
                </div>
            ');
        }
    ?>



</body>
</html>