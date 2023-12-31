<header>
    <!-- Logo clicable qui retourne vers l'accueil connecté -->
    <a href="connected.php"><img alt="mon Logo" src="./media/image/bleu_texte_fonce.png"></a>

    <!-- Liens de redirection (selon si connecté ou non) -->
    <?php
        // Si pas de userID dans la session
        if(!isset($_SESSION['userID'])){
            echo('
                <a href="index.php">Se connecter</a>
                <a href="create_account.php">Créer un compte</a>
            ');
        }

        // TODO :Si userID présent dans la session
    ?>
</header>