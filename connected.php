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

    /** En BDD, je récupère les 5 articles les plus récents dont l'utilisateur * *   est l'auteur. Ils sont :
     * - Trier du plus récents au plus anciens
     * - l'utilisateur doit être l'auteur
    */
    $rqt = 'SELECT id, title FROM article WHERE author = :userID ORDER BY creation_date DESC LIMIT 5';
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
    <meta name="description" content="Bienvenue dans votre espace personnel. Retrouvez ici vos 5 articles les plus récents !">
    <title>Accueil connecté - 5 articles</title>
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

    <!-- Afficher les 5 articles que je viens de récupérer -->
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