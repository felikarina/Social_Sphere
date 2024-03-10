<?php include 'repeat.php'?>

<head>
    <meta charset="utf-8">
    <title>ReSoC - Paramètres</title> 
    <meta name="author" content="Julien Falconnet">
    <link rel="stylesheet" href="style.css"/>
</head>

<form action="settings.php?user_id=<?php echo $_SESSION['connected_id']?>" method="post">
    <input type="radio" name="avatar" value="default"><img src="default.jpg" width="100px">
    <input type="radio" name="avatar" value="Dog"><img src="Dog.jpg" width="100px">
    <input type="radio" name="avatar" value="Dog 2"><img src="Dog 2.jpg" width="100px">
    <input type="radio" name="avatar" value="Girafe"><img src="Girafe.jpg" width="100px">
    <input type="radio" name="avatar" value="horse"><img src="horse.jpg" width="100px">
    <input type="radio" name="avatar" value="Jaguar"><img src="Jaguar.jpg" width="100px">
    <input type="radio" name="avatar" value="fox"><img src="fox.jpg" width="100px">
    <input type="radio" name="avatar" value="sheep"><img src="sheep.jpg" width="100px">
    <input type="radio" name="avatar" value="tigre"><img src="tigre.jpg" width="100px">
    <input type="submit" value="Enregistrer">
</form>

