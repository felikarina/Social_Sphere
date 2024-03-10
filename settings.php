<?php include 'repeat.php';?>
<!doctype html>
<html lang="fr">
    <head>
        <meta charset="utf-8">
        <title>ReSoC - Paramètres</title> 
        <meta name="author" content="Julien Falconnet">
        <link rel="stylesheet" href="style.css"/>
    </head>
    <body>
        <?php include 'header.php' ?>
        <?php
        $id_avatar = $_SESSION['connected_id']; 
        if (isset($_POST['avatar'])) {
            if($_POST['avatar'] == 'Dog') {
                $lInstructionSql = "UPDATE users SET avatar='Dog.jpg' WHERE id='$id_avatar'";
                $good = $mysqli->query($lInstructionSql);
                }
            elseif($_POST['avatar'] == 'Dog 2') {
                $lInstructionSql = "UPDATE users SET avatar='Dog 2.jpg' WHERE id='$id_avatar'";
                $good = $mysqli->query($lInstructionSql);
                }
            elseif($_POST['avatar'] == 'horse') {
                $lInstructionSql = "UPDATE users SET avatar='horse.jpg' WHERE id='$id_avatar'";
                $good = $mysqli->query($lInstructionSql);
                }
            elseif($_POST['avatar'] == 'Jaguar') {
                $lInstructionSql = "UPDATE users SET avatar='Jaguar.jpg' WHERE id='$id_avatar'";
                $good = $mysqli->query($lInstructionSql);
                }
            elseif($_POST['avatar'] == 'sheep') {
                $lInstructionSql = "UPDATE users SET avatar='sheep.jpg' WHERE id='$id_avatar'";
                $good = $mysqli->query($lInstructionSql);
                }
            elseif($_POST['avatar'] == 'tigre') {
                $lInstructionSql = "UPDATE users SET avatar='tigre.jpg' WHERE id='$id_avatar'";
                $good = $mysqli->query($lInstructionSql);
                }
            elseif($_POST['avatar'] == 'fox') {
                $lInstructionSql = "UPDATE users SET avatar='fox.jpg' WHERE id='$id_avatar'";
                $good = $mysqli->query($lInstructionSql);
                }
            elseif($_POST['avatar'] == 'Girafe') {
                $lInstructionSql = "UPDATE users SET avatar='Girafe.jpg' WHERE id='$id_avatar'";
                $good = $mysqli->query($lInstructionSql);
                }
            elseif($_POST['avatar'] == 'default') {
                $lInstructionSql = "UPDATE users SET avatar='default.jpg' WHERE id='$id_avatar'";
                $good = $mysqli->query($lInstructionSql);
                }
            else {
                $_SESSION['avatar'] = 'default.jpg';
                }     
            }   
            $tag_avatar = $_SESSION['connected_id'];
            $laQuestionEnSql = "SELECT * FROM users WHERE id='$tag_avatar'";
            $lesInformations = $mysqli->query($laQuestionEnSql);
            $img = $lesInformations->fetch_assoc();
            ?>
        <div id="wrapper" class='profile'>
            <aside>
                <img src="<?php echo $img['avatar']?>">
                <form action="avatar.php" method="post">
                <input type="hidden">
                <input type="submit" value="Changer d'avatar">
                </form>
                <section>
                    <h3>Présentation</h3>
                    <p>Sur cette page vous trouverez les informations de l'utilisatrice
                        n° <?php echo intval($_GET['user_id']) ?>
                    </p>
                </section>
            </aside>
            <main>
                <?php
                /**
                 * Etape 1: Les paramètres concernent une utilisatrice en particulier
                 * La première étape est donc de trouver quel est l'id de l'utilisatrice
                 * Celui ci est indiqué en parametre GET de la page sous la forme user_id=...
                 * Documentation : https://www.php.net/manual/fr/reserved.variables.get.php
                 * ... mais en résumé c'est une manière de passer des informations à la page en ajoutant des choses dans l'url
                 */
                $userId = intval($_GET['user_id']);
                /**
                 * Etape 2: se connecter à la base de donnée
                 * Etape 3: récupérer le nom de l'utilisateur
                 */
                $laQuestionEnSql = "
                    SELECT users.*, 
                    count(DISTINCT posts.id) as totalpost, 
                    count(DISTINCT given.post_id) as totalgiven, 
                    count(DISTINCT recieved.user_id) as totalrecieved 
                    FROM users 
                    LEFT JOIN posts ON posts.user_id=users.id 
                    LEFT JOIN likes as given ON given.user_id=users.id 
                    LEFT JOIN likes as recieved ON recieved.post_id=posts.id 
                    WHERE users.id = '$userId' 
                    GROUP BY users.id
                    ";
                $lesInformations = $mysqli->query($laQuestionEnSql);
                if ( ! $lesInformations)
                {
                    echo("Échec de la requete : " . $mysqli->error);
                }
                $user = $lesInformations->fetch_assoc();
                /**
                 * Etape 4: à vous de jouer
                 */
                //@todo: afficher le résultat de la ligne ci dessous, remplacer les valeurs ci-après puiseffacer la ligne ci-dessous
                //echo "<pre>" . print_r($user, 1) . "</pre>";
                ?>                
                <article class='parameters'>
                    <h3>Mes paramètres</h3>
                    <dl>
                        <dt>Pseudo</dt>
                        <dd><?php echo $user['alias'] ?></dd>
                        <dt>Email</dt>
                        <dd><?php echo $user['email'] ?></dd>
                        <dt>Nombre de message</dt>
                        <dd><?php echo $user['totalpost'] ?></dd>
                        <dt>Nombre de "J'aime" donnés </dt>
                        <dd><?php echo $user['totalgiven'] ?></dd>
                        <dt>Nombre de "J'aime" reçus</dt>
                        <dd><?php echo $user['totalrecieved'] ?></dd>
                    </dl>
                </article>
            </main>
        </div>
    </body>
</html>
