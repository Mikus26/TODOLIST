<?php 
    session_start();
    
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
        /** TODO elseif = if */
        if(mb_strlen($passwd) < 10){
            $errors['passwd'] = ERROR_PASSWORD_NUMBER_OF_CHARACTERS;
        }

        // TODO : Faire requète SELECT 
        if(!empty($login) && !empty($passwd)){
            /**
             * On vérifie si le login et le mdp existent dans la base de données
             */
            $rqt = 'SELECT * FROM user WHERE login = :login';
            $db_statement = $db_connexion->prepare($rqt);
            $db_statement->execute(
                array(
                    ':login' => $login
                )
            );
            /**
             * Je récupère un tableau associatif
             */
            $data = $db_statement->fetch(PDO::FETCH_ASSOC);
            /**
             * Vérification du mot de passe
             */
            if(password_verify($passwd, $data['passwd'])){
                $_SESSION['userID'] = $data['id'];
                header('location:connected.php');
            }
            else{
                $message = "<span class='message'>Mot de passe incorrect !</span>";
            }
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
    <meta name="description" content="Ceci est la description de la page d'accueil pour mon SEO">
    <title>Accueil TodoList</title>
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
            <h1>Connectez-vous !</h1>

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