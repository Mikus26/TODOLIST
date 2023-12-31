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

    /************************************************************
    *   Création de constantes qui contiennent les erreurs possibles
    ***************************************************************/
    const ERROR_REQUIRED = 'Veuillez renseigner ce champs.';

    /************************************************************
    *   Initialisation d'un tableau contenant les erreurs possibles lors des saisies
    ***************************************************************/
    $errors = [
        'title' => '',
        'content' => '',
    ];
    $message = '';

    // Je vérifie que j'ai bien récupéré l'id de l'article dans la super variable globale $_GET, sinon je redirige l'utilisateur vers la page connected.php
    if(isset($_GET['id']) && !empty($_GET['id'])){
        $idArticle = htmlspecialchars($_GET['id']);

        $rqt = 'SELECT title, content FROM article WHERE id = :idArticle';
        $db_statement = $db_connexion->prepare($rqt);
        $db_statement->execute(
            array(
                ':idArticle' => $idArticle
            )
        );
        $article = $db_statement->fetch(PDO::FETCH_ASSOC);
    }
    else{
        // Si l'id est absent de l'URL', l'utilisateur est redirigé vers connected
        header('location:connected.php');
    }

    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        $_POST = filter_input_array(
            INPUT_POST,[
                'title' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
                'content' => FILTER_SANITIZE_FULL_SPECIAL_CHARS
            ]
        );

        // Initialisation des variables qui vont recevoir les champs du formulaire
        $title = $_POST['title'] ?? '';
        $content = $_POST['content'] ?? '';

        // Remplissage du tableau concernant les erreurs possibles
        if(!$title){
            $errors['title'] = ERROR_REQUIRED;
        }
        if(!$content){
            $errors['content'] = ERROR_REQUIRED;
        }

        if(($title) && ($content)){ 
            // exécution de la requète UPDATE
            $rqt = "UPDATE article SET title = :title, content = :content WHERE id = :idArticle";
            $db_statement = $db_connexion->prepare($rqt);
            $db_statement->execute(
                array(
                    ':title' => $title,
                    ':content' => $content,
                    ':idArticle' => $idArticle
                )    
            );
            header('location:connected.php');
        }
        else{
            $message = "<span class='message'>Veuillez renseigner tous les champs !</span>";
        }
    }



?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Modifiez votre article.">
    <title>Modifiez votre article</title>
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

    <!-- Formulaire pour ajouter un nouvel article -->
    <section class="container">
        <div class="form-container">
            <h1>Modifiez votre article !</h1>
            <!-- Insérer les messages d'erreur/succès -->
            <div class="form-control">
                <?= $message ?>        
            </div>
            <form action="#" method="POST">
                <div class="form-control">
                    <input type="text" name="title" id="title" value="<?= htmlspecialchars($article['title']) ?>">
                    <!-- Insérer les messages d'erreur/succès -->
                    <?= $errors['title'] ? '<p class="text-error">'. $errors['title'] .'</p>' : '' ?>
                </div>
                <div class="form-control">
                    <textarea rows="5" name="content" id="content"><?= htmlspecialchars($article['content']) ?></textarea>
                    <!-- Insérer les messages d'erreur/succès -->
                    <?= $errors['content'] ? '<p class="text-error">'. $errors['content'] .'</p>' : "" ?>
                </div>
                <div class="form-control">
                    <input type="submit" value="MODIFIER" class="btn-primary">
                </div>
            </form>

        </div>
    </section>
</body>
</html>