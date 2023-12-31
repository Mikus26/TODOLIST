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
        // On a déjà $userID pour l'id de l'auteur
        $creation_date = date('Y-m-d');

        // Remplissage du tableau concernant les erreurs possibles
        if(!$title){
            $errors['title'] = ERROR_REQUIRED;
        }
        if(!$content){
            $errors['content'] = ERROR_REQUIRED;
        }

        // Execution de la requète INSERT into
        // TODO : Ajouter la condition de 10 caractères sur le mdp
        if(($title) && ($content)){ 
            // exécution de la requète INSERT INTO
            $rqt = "INSERT INTO article VALUES (DEFAULT, :title, :content, :author, :creation_date)";
            $db_statement = $db_connexion->prepare($rqt);
            $db_statement->execute(
                array(
                    ':title' => $title,
                    ':content' => $content,
                    ':author' => $userID,
                    ':creation_date' => $creation_date
                )    
             );
            $message = "<span class='message'>Votre article a bien été créé !</span>";
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
    <meta name="description" content="Créer ici un nouvel article !">
    <title>Créer un nouvel article</title>
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
            <h1>Créez votre nouvel article !</h1>
            <!-- Insérer les messages d'erreur/succès -->
            <div class="form-control">
                <?= $message ?>        
            </div>
            <form action="#" method="POST">
                <div class="form-control">
                    <input type="text" name="title" id="title" placeholder="Votre titre ici...">
                    <!-- Insérer les messages d'erreur/succès -->
                    <?= $errors['title'] ? '<p class="text-error">'. $errors['title'] .'</p>' : '' ?>
                </div>
                <div class="form-control">
                    <textarea rows="5" name="content" id="content" placeholder="Votre contenu détaillé ici..."></textarea>
                    <!-- Insérer les messages d'erreur/succès -->
                    <?= $errors['content'] ? '<p class="text-error">'. $errors['content'] .'</p>' : "" ?>
                </div>
                <div class="form-control">
                    <input type="submit" value="VALIDER" class="btn-primary">
                </div>
            </form>

        </div>
    </section>

</body>
</html>