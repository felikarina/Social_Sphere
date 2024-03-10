<?php include 'repeat.php';?>
<!doctype html>
<html lang="fr">
    <head>
        <meta charset="utf-8">
        <title>ReSoC - Les message par mot-clé</title> 
        <meta name="author" content="Julien Falconnet">
        <link rel="stylesheet" href="style.css"/>
    </head>
    <body>
        <?php include 'header.php' ?>
        <div id="wrapper">
            <?php
            /**
             * Cette page est similaire à wall.php ou feed.php 
             * mais elle porte sur les mots-clés (tags)
             */
            /**
             * Etape 1: Le mur concerne un mot-clé en particulier
             */
            $tagId = intval($_GET['tag_id']);
            ?>
            <?php
            /**
             * Etape 2: se connecter à la base de donnée
             */
            ?>
            <aside>
                <?php
                /**
                 * Etape 3: récupérer le nom du mot-clé
                 */
                $laQuestionEnSql = "SELECT * FROM tags WHERE id= '$tagId' ";
                $lesInformations = $mysqli->query($laQuestionEnSql);
                $tag = $lesInformations->fetch_assoc();
                //@todo: afficher le résultat de la ligne ci dessous, remplacer XXX par le label et effacer la ligne ci-dessous
                ?>
                <img src="<?php echo $img['avatar']?>">
                <section>
                    <h3>Présentation</h3>
                    <p>Sur cette page vous trouverez les derniers messages comportant
                        le mot-clé <?php echo $tag['label'] ?>
                        (n° <?php echo $tagId ?>)
                    </p>
                </section>
            </aside>
            <main>
                <?php
                /**
                 * Etape 3: récupérer tous les messages avec un mot clé donné
                 */
                $laQuestionEnSql = "
                    SELECT posts.content,
                    posts.created,
                    users.alias as author_name,
                    users.id as author_id,  
                    count(likes.id) as like_number,  
                    GROUP_CONCAT(DISTINCT tags.label) AS taglist 
                    FROM posts_tags as filter 
                    JOIN posts ON posts.id=filter.post_id
                    JOIN users ON users.id=posts.user_id
                    LEFT JOIN posts_tags ON posts.id = posts_tags.post_id  
                    LEFT JOIN tags       ON posts_tags.tag_id  = tags.id 
                    LEFT JOIN likes      ON likes.post_id  = posts.id 
                    WHERE filter.tag_id = '$tagId' 
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