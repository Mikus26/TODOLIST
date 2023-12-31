<?php
    // J'inclus une connexion vers la BDD
    include('./model/db_connexion.php');

    /************************************************************
    *   Création de constantes qui contiennent les erreurs possibles
    ***************************************************************/
    const ERROR_REQUIRED = 'Veuillez renseigner ce champs.';
    const ERROR_PASSWORD_NUMBER_OF_CHARACTERS = 'Le mot de passe doit contenir 10 caractères minimum.';

    /************************************************************
    *   Initialisation d'un tableau contenant les erreurs possibles lors des saisies
    ***************************************************************/
    $errors = [
        'login' => '',
        'passwd' => '',
    ];
    $message = '';

    /************************************************************
    *   Traitement des données SI la méthode est bien POST
    ***************************************************************/    
    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        $_POST = filter_input_array(
            INPUT_POST,[
                'login' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
                'passwd' => FILTER_SANITIZE_FULL_SPECIAL_CHARS
            ]
        );
        // Initialisation des variables qui vont recevoir les champs du formulaire
        $login = $_POST['login'] ?? '';
        $passwd = $_POST['passwd'] ?? '';

        // Remplissage du tableau concernant les erreurs possibles
        if(!$login){
            $errors['login'] = ERROR_REQUIRED;
        }
        if(!$passwd){
            $errors['passwd'] = ERROR_REQUIRED;
        }
        elseif(mb_strlen($passwd) < 10){
            $errors['passwd'] = ERROR_PASSWORD_NUMBER_OF_CHARACTERS;
        }

        // Execution de la requète INSERT into
        // TODO : Ajouter la condition de 10 caractères sur le mdp
        if(($login) && !empty($login) && ($passwd) && !empty($login) && (mb_strlen($passwd) >= 10)){
            $sql = 'SELECT login FROM user WHERE login = :login';
            $db_statement = $db_connexion->prepare($sql);
            $db_statement->execute(
                array(':login' => $login)
            );

            // Si l'exécution de la requète retourne une valeur <= 0, alors on traite la demande
            $nb = $db_statement->rowCount();
            if($nb <= 0){
                // exécution de la requète INSERT INTO
                $rqt = "INSERT INTO user VALUES (DEFAULT, :login, :passwd)";
                $db_statement = $db_connexion->prepare($rqt);
                $db_statement->execute(
                    array(
                        ':login' => $login,
                        ':passwd' => password_hash($passwd, PASSWORD_DEFAULT)
                    )    
                );
                $message = "
                    <span class='message'>
                        Votre compte est créé ! Aller sur la page \" Se connecter \" et connectez-vous avec vos identifiants !
                    </span>
                ";
            }
            else{
                $message = "
                    <span class='message'>
                        Le login existe déjà !
                    </span>
                ";
            }  
        }
        else{
            $message = "
                <span class='message'>
                Veuillez renseigner tous les champs !
                </span>
            ";
        }
    }
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Ceci est la page d'inscription">
    <title>Créer votre compte</title>
    <link rel="stylesheet" href="/styles/mainCss.css">
</head>
<body>
    <!-- Ma navBar est importée -->
    <?php 
        require_once('./includes/navBar.php') 
    ?>

    <!-- Formulaire de connexion -->
    <section class="container">
        <div class="form-container">
            <h1>Inscrivez-vous !</h1>
            <!-- Insérer les messages d'erreur/succès -->
            <div class="form-control">
                <?= $message ?>        
            </div>
            <form action="#" method="POST">
                <div class="form-control">
                    <input type="text" name="login" id="login" placeholder="Votre pseudo ici...">
                    <!-- Insérer les messages d'erreur/succès -->
                    <?= $errors['login'] ? '<p class="text-error">'. $errors['login'] .'</p>' : '' ?>
                </div>
                <div class="form-control">
                    <input type="password" name="passwd" id="passwd" placeholder="Votre mot de passe ici...">
                    <!-- Insérer les messages d'erreur/succès -->
                    <?= $errors['passwd'] ? '<p class="text-error">'. $errors['passwd'] .'</p>' : "" ?>
                </div>
                <div class="form-control">
                    <input type="submit" value="VALIDER" class="btn-primary">
                </div>
            </form>

        </div>
    </section>
</body>
</html>