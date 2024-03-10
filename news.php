<?php include 'repeat.php';?>
<!doctype html>
<html lang="fr">
    <head>
        <meta charset="utf-8">
        <title>ReSoC - Actualités</title> 
        <meta name="author" content="Julien Falconnet">
        <link rel="stylesheet" href="style.css"/>
    </head>
    <body>
    <?php include 'header.php' ?>
    <div style="visibility: hidden" class="heart">
        <img src="coeur.gif" id="heart">
    </div>
        <div id="wrapper">
            <aside>
            <img src="<?php echo $img['avatar']?>">
                <section>
                    <h3>Présentation</h3>
                    <p>Sur cette page vous trouverez les derniers messages de
                        tous les utilisatrices du site.</p>
                </section>
            </aside>
            <main>         
                <?php
                /*
                  // C'est ici que le travail PHP commence
                  // Votre mission si vous l'acceptez est de chercher dans la base
                  // de données la liste des 5 derniers messsages (posts) et
                  // de l'afficher
                  // Documentation : les exemples https://www.php.net/manual/fr/mysqli.query.php
                  // plus généralement : https://www.php.net/manual/fr/mysqli.query.php
                 */
                // Etape 1: Ouvrir une connexion avec la base de donnée.
                //verification
                if ($mysqli->connect_errno)
                {
                    echo "<article>";
                    echo("Échec de la connexion : " . $mysqli->connect_error);
                    echo("<p>Indice: Vérifiez les parametres de <code>new mysqli(...</code></p>");
                    echo "</article>";
                    exit();
                }
                // Etape 2: Poser une question à la base de donnée et récupérer ses informations
                // cette requete vous est donnée, elle est complexe mais correcte, 
                // si vous ne la comprenez pas c'est normal, passez, on y reviendra
                $laQuestionEnSql = "
                    SELECT posts.content,
                    posts.created,
                    users.id as author_id,
                    posts.id as post_id,
                    users.alias as author_name,  
                    count(likes.id) as like_number,  
                    GROUP_CONCAT(DISTINCT tags.label) AS taglist 
                    FROM posts
                    JOIN users ON  users.id=posts.user_id
                    LEFT JOIN posts_tags ON posts.id = posts_tags.post_id  
                    LEFT JOIN tags       ON posts_tags.tag_id  = tags.id 
                    LEFT JOIN likes      ON likes.post_id  = posts.id 
                    GROUP BY posts.id
                    ORDER BY posts.created DESC  
                    LIMIT 5
                    ";
                $lesInformations = $mysqli->query($laQuestionEnSql);
                // Vérification
                if ( ! $lesInformations)
                {
                    echo "<article>";
                    echo("Échec de la requete : " . $mysqli->error);
                    echo("<p>Indice: Vérifiez la requete  SQL suivante dans phpmyadmin<code>$laQuestionEnSql</code></p>");
                    exit();
                }
                // Etape 3: Parcourir ces données et les ranger bien comme il faut dans du html
                // NB: à chaque tour du while, la variable post ci dessous reçois les informations du post suivant.
                while ($post = $lesInformations->fetch_assoc())
                {
                    //echo "<pre>" . print_r($post, 1) . "</pre>";
                    // @todo : Votre mission c'est de remplacer les AREMPLACER par les bonnes valeurs
                    // ci-dessous par les bonnes valeurs cachées dans la variable $post 
                    // on vous met le pied à l'étrier avec created
                    // 
                    // avec le ? > ci-dessous on sort du mode php et on écrit du html comme on veut... mais en restant dans la boucle
                    ?>
                    <article>
                        <h3>
                            <time><?php echo strftime('%e ', strtotime($post['created'])) . traduireMois(strftime('%B', strtotime($post['created']))) . strftime(' %Y à %Hh%M', strtotime($post['created'])) ?></time>
                        </h3>
                        <address>par <a href="wall.php?user_id=<?php echo $post['author_id']; ?>"><?php echo $post['author_name']; ?></a></address>
                        <div>
                            <p><?php echo $post['content'] ?></p>
                        </div>
                        <footer>
                            <div id="like">
                            <?php
                            if (isset($_POST['like']) && $_POST['like_post_id'] == $post['post_id'])  {
                                $lInstructionSql = "INSERT INTO likes "
                                . "(id, user_id, post_id) " //attention voir le nom des colonnes dans la database
                                . "VALUES (NULL, "
                                . $_SESSION['connected_id'] . ", "
                                . $post['post_id'] .")"
                                ;
                                $ok = $mysqli->query($lInstructionSql);
                                echo "<script>
                                function show(){ document.getElementById('heart').style.visibility = 'visible'};
                                function hide(){ document.getElementById('heart').style.visibility = 'hidden'};
                                show();
                                setTimeout('hide()', 2000);
                                </script>";
                                }
                                $like_count_query = "SELECT COUNT(*) as like_count FROM likes WHERE post_id = " . $post['post_id'];
                                $like_count_result = $mysqli->query($like_count_query);
                                $like_count_row = $like_count_result->fetch_assoc();
                                $like_number = $like_count_row['like_count'];
                                echo $like_number;?>♥
                            
                            <form action="news.php" method="post">
                            <input type="hidden" name="like" value="like">
                            <input type="hidden" name="like_post_id" value="<?php echo $post['post_id']; ?>">
                            <input type="submit" value="Like">
                            </form></div>
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
