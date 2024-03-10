<?php include 'repeat.php';?>
<!doctype html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <title>ReSoC - Mur</title> 
        <meta name="author" content="Julien Falconnet">
        <link rel="stylesheet" href="style.css"/>
    </head>
    <body>
        <?php include 'header.php' ?>
        <div id="wrapper">
            <?php
            /**
             * Etape 1: Le mur concerne un utilisateur en particulier
             * La première étape est donc de trouver quel est l'id de l'utilisateur
             * Celui ci est indiqué en parametre GET de la page sous la forme user_id=...
             * Documentation : https://www.php.net/manual/fr/reserved.variables.get.php
             * ... mais en résumé c'est une manière de passer des informations à la page en ajoutant des choses dans l'url
             */
            $userId =intval($_GET['user_id']);
            ?>
            <aside>
                <?php
                // Etape 3: récupérer le nom de l'utilisateur               
                $laQuestionEnSql = "SELECT * FROM users WHERE id= '$userId' ";
                $lesInformations = $mysqli->query($laQuestionEnSql);
                $user = $lesInformations->fetch_assoc();
                //@todo: afficher le résultat de la ligne ci dessous, remplacer XXX par l'alias et effacer la ligne ci-dessous
                ?>
                <img src="<?php echo $img['avatar']?>">
                <section>
                    <h3>Présentation</h3>
                    <p>Sur cette page vous trouverez tous les message de l'utilisatrice : <?php echo $user['alias'] ?>
                        (n° <?php echo $userId ?>)
                    </p>
                </section>
                    <?php
                    //var_dump($_POST);
                    if ($userId != $_SESSION['connected_id']){
                    if (isset($_POST['abonnement'])) {
                    if ($_POST['abonnement'] == 'abo')
                    {
                        $lInstructionSql = "INSERT INTO followers "
                                . "(id, followed_user_id, following_user_id) " //attention voir le nom des colonnes dans la database
                                . "VALUES (NULL, "
                                . $userId . ", "
                                . $_SESSION['connected_id'] .")"
                                ;
                        $ok = $mysqli->query($lInstructionSql);
                        if ( ! $ok)
                        {
                            echo "Impossible de s'abonner " . $mysqli->error;
                        } else
                        {
                            echo "Tu es abonné à ". $user['alias'] . " !";
                        }
                    }
                    if ($_POST['abonnement'] == 'désabo')
                    {
                        $lInstructionSql = "DELETE FROM followers 
                        WHERE followed_user_id=" . $userId . " 
                        AND following_user_id=" . $_SESSION['connected_id'] ."";
                        $ok = $mysqli->query($lInstructionSql);
                        if ( ! $ok)
                        {
                            echo "Impossible de se désabonner " . $mysqli->error;
                        } else
                        {
                            echo "Tu n'es plus abonné à ". $user['alias'] . " !";
                        }}}
                    ?><article>
                        <form action="wall.php?user_id=<?php echo $userId ?>" method="post">
                            <input type="radio" name="abonnement" value="abo">s'abonner
                            <input type="radio" name="abonnement" value="désabo">se désabonner
                            <input type="submit">
                        </form>
                    </article>
                <?php
                }
                ?>
            </aside>
            <main>
                <?php if ($userId == $_SESSION['connected_id']){ ?>
            <article>
                    <h2>Poster un message</h2>
                    <?php
                    $laQuestionEnSql = "SELECT * FROM users";
                    $lesInformations = $mysqli->query($laQuestionEnSql);
                    while ($user = $lesInformations->fetch_assoc())
                    $enCoursDeTraitement = isset($_POST['message']);
                    if ($enCoursDeTraitement)
                    {
                        $postContent = $_POST['message'];
                        $postContent = $mysqli->real_escape_string($postContent);
                        $lInstructionSql = "INSERT INTO posts "
                                . "(id, user_id, content, created, parent_id) " //attention voir le nom des colonnes dans la database
                                . "VALUES (NULL, "
                                . $userId . ", "
                                . "'" . $postContent . "', "
                                . "NOW(), "
                                . "NULL);"
                                ;
                        $ok = $mysqli->query($lInstructionSql);
                        if ( ! $ok)
                        {
                            echo "Impossible d'ajouter le message: " . $mysqli->error;
                        } else
                        {
                            echo "Message publié !";
                        }
                    }
                    ?>                     
                    <form action="wall.php?user_id=<?php echo $_SESSION['connected_id']?>" method="post">
                        <input type='hidden' name='???' value='achanger'>
                        <dl>
                            <dt><label for='message'>Message</label></dt>
                            <dd><textarea name='message'></textarea></dd>
                        </dl>
                        <input type='submit'>
                    </form>               
                </article>
                <?php } 
                //Etape 3: récupérer tous les messages de l'utilisatrice
                $laQuestionEnSql = "
                    SELECT posts.content, posts.created, users.alias as author_name, 
                    COUNT(likes.id) as like_number, GROUP_CONCAT(DISTINCT tags.label) AS taglist 
                    FROM posts
                    JOIN users ON  users.id=posts.user_id
                    LEFT JOIN posts_tags ON posts.id = posts_tags.post_id  
                    LEFT JOIN tags       ON posts_tags.tag_id  = tags.id 
                    LEFT JOIN likes      ON likes.post_id  = posts.id 
                    WHERE posts.user_id='$userId' 
                    GROUP BY posts.id
                    ORDER BY posts.created DESC  
                    ";
                $lesInformations = $mysqli->query($laQuestionEnSql);
                if ( ! $lesInformations)
                {
                    echo("Échec de la requete : " . $mysqli->error);
                }
                /**
                 * Etape 4: @todo Parcourir les messsages et remplir correctement le HTML avec les bonnes valeurs php
                 */
                while ($post = $lesInformations->fetch_assoc())
                {                  
                    //echo "<pre>" . print_r($post, 1) . "</pre>";
                    ?>                
                    <article>
                        <h3>
                            <time><?php echo strftime('%e ', strtotime($post['created'])) . traduireMois(strftime('%B', strtotime($post['created']))) . strftime(' %Y à %Hh%M', strtotime($post['created'])) ?></time>
                        </h3>
                        <address>par <?php echo $post['author_name'] ?></address>
                        <div>
                            <p><?php echo $post['content'] ?></p>
                        </div>                                            
                        <footer>
                            <small><?php echo $post['like_number'] ?> ♥</small>
                            <?php 
                                $tags = explode(',', $post['taglist']);
                                foreach ($tags as $tag_name) {
                                    // Récupérer l'ID du tag à partir de son nom
                                    $tag_query = "SELECT id FROM tags WHERE label = '$tag_name'";
                                    $tag_result = $mysqli->query($tag_query);
                                    if ($tag_result && $tag_result->num_rows > 0) {
                                        $tag_row = $tag_result->fetch_assoc();
                                        $tag_id = $tag_row['id'];
                                        // Génération du lien avec l'ID du tag
                                        echo '<a href="tags.php?tag_id=' . $tag_id . '">#' . $tag_name . '</a>, ';
                                    }
                                } 
                            ?>
                        </footer>
                    </article>
                <?php } ?>
            </main>
        </div>
    </body>
</html>
