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

    // Je vérifie que j'ai bien récupéré l'id de l'article dans la super variable globale $_GET, sinon je redirige l'utilisateur vers la page connected.php
    if(isset($_GET['id']) && !empty($_GET['id'])){
        // Je stocke l'id de l'article dans une variable puis je fais ma requète SELECT
        $idArticle = htmlspecialchars($_GET['id']);

        $rqt = 'SELECT article.title, article.content, article.creation_date, user.login FROM article LEFT JOIN user ON article.author = user.id WHERE article.id = :idArticle';
        $db_statement = $db_connexion->prepare($rqt);
        $db_statement->execute(
            array(
                ':idArticle' => $idArticle
            )
        );
        $article = $db_statement->fetch(PDO::FETCH_ASSOC);
        // Si l'id est absent de la BDD, l'utilisateur est redirigé vers connected
        $nb = $db_statement->rowCount();
        if($nb <= 0){
            header('location:connected.php');
        }

        // $article['creation_date'] est de type String. Je stocke cette date dans la variable $date pour avoir un format JJ/MM/AAAA
        $date = new DateTimeImmutable($article['creation_date']);
        $date = date_format($date, "d/m/Y");
        
    }
    else{
        // Si l'id est absent de l'URL', l'utilisateur est redirigé vers connected
        header('location:connected.php');
    }


    // Suppression d'un article selon son id
    // if($_SERVER['REQUEST_METHOD'] === 'POST'){
        
    //     if(isset($_GET['id']) && !empty($_GET['id'])){ 
    //         $idArticle = htmlspecialchars($_GET['id']);

    //         // exécution de la requète UPDATE
    //         $rqt = "DELETE FROM article WHERE id = :idArticle";
    //         $db_statement = $db_connexion->prepare($rqt);
    //         $db_statement->execute(
    //             array(
    //                 ':idArticle' => $idArticle
    //             )    
    //         );
    //         header('location:connected.php');
    //     }
    //     else{
    //         $message = "<span class='message'>Erreur, votre article n'a pas été trouvé !</span>";
    //     }
    // }


    /*      VERSION NAIMA     */
    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        $rqt = 'DELETE FROM article WHERE id = :idArticle';
        $db_statement = $db_connexion->prepare($rqt);
        $db_statement->execute(
            array(
                ':idArticle' => $_GET['id']
            )    
        );
        header('Location:connected.php');
        exit;
    }

?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Retrouvez dans cette page le détail de votre article.">
    <title>Lire un article</title>
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

    <article class="container">
        <h1><?php echo(htmlspecialchars($article['title'])) ?></h1>

        <p class="todolist_content">
            <?php echo(htmlspecialchars($article['content'])) ?>
        </p>

        <div class="todolist_footer">
            <div class="todolist_footer_content">
                Auteur : <?php echo(htmlspecialchars($article['login'])) ?>
            </div>
            <div class="todolist_footer_content">
                Edité le : <?php echo(htmlspecialchars($date)) ?>
            </div>
        </div>
        <div class="button_zone">
            <a href="update_article.php?id=<?= $idArticle ?>" class="btn-primary">Modifier</a>
            <form action="#" method="POST">
                <input type="submit" class="btn-primary btn-warning" value="SUPPRIMER">
            </form>
        </div>
    </article>



</body>
</html>