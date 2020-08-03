<?php

// On démarre la session
session_start();

if (isset($_POST['player1'], $_POST['player2']))
{
    if (!empty($_POST['player1']) && !empty($_POST['player2']))
    {
        if (strlen($_POST['player1']) < 16 && strlen($_POST['player2']) < 16)
        {
            // On ajoute les noms des joueurs dans la sessions
            $_SESSION['player1'] = $_POST['player1'];
            $_SESSION['player2'] = $_POST['player2'];
            $_SESSION['login'] = true;
        }
        else
        {
            $message = "Le nom de vos joueurs sont trop long !";
        }
    }
    else
    {
        $message = "Veuillez remplir tous les champs requis !";
    }
}

// Si il n'y a aucun joueur actuel, on définis le premier joueurs
if (!isset($_SESSION['currentPlayer']))
{
    $_SESSION['currentPlayer'] = "X";
}

?>
<!DOCTYPE html>
<html lang="fr">
    <head>
        <title>Gomoku</title>

        <meta charset="UTF-8">
        <meta name="description" content="Un gomoku, rien de plus simple.">
        <meta name="keywords" content="Gomoku, Skytech">
        <meta name="author" content="Skytech">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <link rel="stylesheet" href="public/css/style.css">
        <link rel="icon" type="image/png" href="public/img/icon.png">
    </head>
    
    <body>
        <h1 class="title">Gomoku</h1>
        
        <?php
        // Si les noms des joueurs et le login n'existe pas
        // Alors on affiche le formulaire
        if (!isset($_SESSION['player1'], $_SESSION['player2'], $_SESSION['login']))
        {
        ?>
        <div class="firstContainer">
            <p class="desc">Veuillez entrer le nom des différents joueurs.</p>
            <?= (isset($message) ? '<p class="error">/!\ ' . $message . ' /!\</p>' : "") ?>

            <form method="POST" action="index.php">
                <p class="input"><label for="player1">Joueur 1 :</label> <input type="text" name="player1" id="player1" required></p>
                <p class="input"><label for="player2">Joueur 2 :</label> <input type="text" name="player2" id="player2" required></p>
                <button type="submit" class="button">Commencer !</button>
            </form>
        </div>
        <?php
        }
        else
        {
            $online = true;

            // Configuration du tableau
            $arrayX = 15;
            $arrayY = 15;
            $nbCase = $arrayX * $arrayY; // 15 x 15
            
            // Définition des joueurs
            $player1 = "X";
            $player2 = "O";

            // Création d'un tableau pour définir un ID aux cases
            // Pour connaître la case qui sera cochée
            $i = 1;
            $arrayBox = array();
            while($i <= $nbCase)
            {
                $arrayBox[] = $i;
                $i++;
            }

            // Ajout et sauvegarde des cases cochées dans une session (caseID => joueur)
            // 1 => string 'X'
            // 5 => string 'O'
            // 15 => string 'X'
            // etc ..
            if (isset($_POST['choice'], $_POST['currentPlayer']) && !empty($_POST['choice']) && !empty($_POST['currentPlayer']))
            {
                $_SESSION['saveGame'][$_POST['choice']] = $_POST['currentPlayer'];
            }
            //var_dump($_SESSION['saveGame']);

            // Système de tour par tour
            if (isset($_SESSION['saveGame']))
            {
                // Verifie si la fin du tableau est le joueur X
                if (end($_SESSION['saveGame']) == "X")
                {
                    $_SESSION['currentPlayer'] = "O";
                }
                else
                {
                    $_SESSION['currentPlayer'] = "X";
                }
            }

        ?>
        <div class="arrayContainer">
            <div class="players">
                <p>Joueur 1 (X) : <?= htmlspecialchars($_SESSION['player1']); ?></p>
                <p class="currentPlayer">Tour de : <?= (isset($_SESSION['currentPlayer']) && $_SESSION['currentPlayer'] == "O") ? htmlspecialchars($_SESSION['player2']) : htmlspecialchars($_SESSION['player1']); ?></p>
                <p>Joueur 2 (O) : <?= htmlspecialchars($_SESSION['player2']); ?></p>
            </div>

            <form method="POST" action="index.php">
                <table>
                    <?php

                    // On créer un tableau qui fait un retour à la ligne
                    // tout les X colonnes ($arrayX)
                    foreach(array_chunk($arrayBox, $arrayX, true) as $row)
                    {
                        echo '<tr>' . PHP_EOL;
                            // Insère toutes les numéros de la boucle dans $value
                            foreach($row as $value)
                            {
                                if (isset($_SESSION['saveGame']))
                                {
                                    if (array_key_exists($value, $_SESSION['saveGame']))
                                    {
                                        foreach ($_SESSION['saveGame'] as $pos => $player)
                                        {
                                            if ($value == $pos)
                                            {
                                                if ($player == "X")
                                                {
                                                    echo '<td class="x">X</td>';
                                                }
                                                else
                                                {
                                                    echo '<td class="o">O</td>';
                                                }
                                            }
                                        }
                                    }
                                    else
                                    {
                                        if ($online == true)
                                        {
                                            echo '<td><span class="small_number">' . $value . '</span><input type="radio" name="choice" value="' . $value . '" required></td>';
                                        }
                                        else
                                        {
                                            echo '<td><input type="radio" name="choice" value="' . $value . '" required></td>';
                                        }
                                        echo '<input type="hidden" name="currentPlayer" value="' . $_SESSION['currentPlayer'] . '">';
                                    }
                                }
                                else
                                {
                                    if ($online == true)
                                    {
                                        echo '<td><span class="small_number">' . $value . '</span><input type="radio" name="choice" value="' . $value . '" required></td>';
                                    }
                                    else
                                    {
                                        echo '<td><input type="radio" name="choice" value="' . $value . '" required></td>';
                                    }
                                    echo '<input type="hidden" name="currentPlayer" value="' . $_SESSION['currentPlayer'] . '">';
                                }
                            }
                        echo '</tr>' . PHP_EOL;
                    }
                
                    ?>
                </table>
                <br>
                <div class="buttons">
                    <button type="submit" class="button2">valider</button> <a href="logout.php" class="button">terminer la partie</a>
                </div>
            </form>
        </div>
        <?php
        }
        ?>
    </body>
</html>