<?php
include __DIR__ . '/inc/main.inc.php';

if ($id = $_POST['id'] ?? false) {
    $email = $_POST['email'] ?? false;
    if (!$email) {
        $email = sha1(time());
    }
    $participe = $_POST['participe'] ?? false;
    $nb = isset($_POST['nb']) ? $_POST['nb'] + 1 : 1;

    $participation = upsertParticipation($id, ['email' => $email, 'participe' => $participe, 'id_evenement' => $id, 'nb' => $nb]);
    rediriger(urlEvenement($id) . '?email=' . urlencode($email));
}

$id = $_GET['id'] ?? false;
$setNb = isset($_GET['set-nb']);
$changer = isset($_GET['changer']);
$email = $_GET['email'] ?? '';


if ($participe = $_GET['p'] ?? false) {
    $participation = upsertParticipation($id, ['email' => $email, 'participe' => $participe, 'id_evenement' => $id]);
    rediriger(urlEvenement($id) . '?email=' . urlencode($email));
}
$evenement = getEvenement($id);

$participation = getParticipation($email, $id);

$participe = $changer ? false : ($participation['participe'] ?? false);

$titre = htmlspecialchars($evenement['evenement'] . ' - Participez à cet évenement !');
$description = descriptionEvenement($evenement);
$href = urlEvenement($id) . '?email=' . urlencode($email);

$classes = [];

if ($setNb) {
    $classes[] = 'set-nb';
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title><?= $titre ?></title>
    <meta name="description" content="Evenement <?= $description ?>" />
    <base href="/events/">


    <link rel="apple-touch-icon" sizes="180x180" href="https://coworking-metz.fr/favicon/apple-touch-icon.png?v=2">
    <link rel="icon" type="image/png" sizes="32x32" href="https://coworking-metz.fr/favicon/favicon-32x32.png?v=2">
    <link rel="icon" type="image/png" sizes="16x16" href="https://coworking-metz.fr/favicon/favicon-16x16.png?v=2">
    <link rel="manifest" href="https://coworking-metz.fr/favicon/site.webmanifest?v=2">
    <link rel="mask-icon" href="https://coworking-metz.fr/favicon/safari-pinned-tab.svg?v=2" color="#f3af10">
    <link rel="shortcut icon" href="https://coworking-metz.fr/favicon/favicon.ico?v=2">
    <meta name="apple-mobile-web-app-title" content="<?= $titre; ?>">
    <meta name="application-name" content="<?= $titre; ?>">
    <meta name="msapplication-TileColor" content="#da532c">
    <meta name="msapplication-config" content="https://coworking-metz.fr/favicon/browserconfig.xml?v=2">
    <meta name="theme-color" content="#000000">




    <meta http-equiv="X-UA-Compatible" content="IE=Edge">
    <meta name="description" content="<?= $description; ?>">
    <meta name="msapplication-config" content="https://coworking-metz.fr/favicon/browserconfig.xml">
    <meta name="msapplication-TileColor" content="#f2af10">
    <meta name="theme-color" content="#f2af10">
    <meta name="title" content="<?= $titre; ?>">

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:description" content="<?= $description; ?>">
    <meta name="twitter:image" content="<?= $evenement['image_url']; ?>">

    <meta property="og:description" content="<?= $description; ?>">
    <meta property="og:image" content="<?= $evenement['image_url']; ?>">
    <meta property="og:image:alt" content="<?= $titre; ?>">
    <meta property="og:image:secure_url" content="<?= $evenement['image_url']; ?>">
    <meta property="og:image:type" content="image/jpeg">
    <meta property="og:image:url" content="<?= $evenement['image_url']; ?>">
    <meta property="og:locale" content="fr_FR">
    <meta property="og:site_name" content="<?= $titre; ?>">
    <meta property="og:title" content="<?= $titre; ?>">
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://rejoindre.coworking-metz.fr/">

    <meta name="description" content="Comment devenir coworker ?" />
    <link rel="shortcut icon" href="https://www.coworking-metz.fr/favicon.ico" />
    <!-- Pico.css -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@picocss/pico@1/css/pico.min.css" />

    <script src="js/scripts.js"></script>
    <!-- Custom styles for this example -->
    <link rel="stylesheet" href="css/style.css?<?=filemtime('./css/style.css');?>" />
    <style>
        body {
            --primary: <?=$evenement['couleur'] ? $evenement['couleur'] : '#e9b142';?>;
            --primary-rgb: <?=$evenement['couleur'] ? hexToRgb($evenement['couleur']) : '243, 175, 16';?>;
        }
    </style>
</head>

<body class="<?= implode(' ', $classes); ?>">

    <!-- Main -->
    <main class="container">
        <article class="grid">
            <div>
                <hgroup>
                    <h1><?= htmlspecialchars($evenement['evenement']); ?></h1>
                    <p style="font-size:smaller"><?= $evenement['description']; ?></p>
                    <p>Le <?= formatDateToFrench($evenement['date']); ?>
                        <?php if ($evenement['heure']) { ?>à <?= formatTimeToHHMM($evenement['heure']); ?><?php } ?>
                        <?php if ($evenement['lieu']) { ?><br>Lieu: <?= htmlspecialchars($evenement['lieu']); ?><?php } ?>
                            <br><small><b><?= participationEvenement($evenement) ?></b></small>
                    </p>
                </hgroup>
                <?php if (!$changer) { ?>
                    <h2><b><?= texteParticipation($participation); ?></b></h2>
                <?php } ?>
                <form method="post">
                    <input type="hidden" name="id" value="<?= htmlspecialchars($id); ?>">
                    <?php if ($participe) { ?>
                        <?php if ($participe == 'ok' && $participation['nb'] == 1) { ?>
                            <p>Vous venez à plusieurs ? <a href="<?= $href; ?>&changer&set-nb">Renseignez le nombre de personnes qui vont vous accompagner&hellip;</a></p>
                        <?php } ?>
                        <p>Changement de programme ?
                            <a href="<?= $href; ?>&changer" _role="button">Modifiez votre réponse</a>
                        </p>
                    <?php } else { ?>
                        <label>
                            <?php if ($participation) { ?>
                                <!-- <b>Modifiez votre choix</b> -->
                            <?php } else { ?>
                                <b>Vous participez ?</b>
                            <?php } ?>
                            <input type="<?= empty($email) || strstr($email, '@') ? 'email' : 'hidden'; ?>" name="email" placeholder="Votre email" value="<?= htmlspecialchars($email) ?>" />
                            <small><a href="#set-nb" class="if-not-set-nb">Je viendrai accompagné&hellip;</a></small>
                        </label>
                        <label class="if-set-nb">
                            <b>Combien de personnes vous accompagnent ?</b>
                            <input type="number" name="nb" value="<?= $participation['nb'] - 1 ?>" />
                        </label>
                        <?php if ($participe != 'ok') { ?>
                            <button type="submit" name="participe" value="ok">Je participe</button>
                        <?php } ?>
                        <?php if ($participe != 'maybe') { ?>
                            <button type="submit" name="participe" value="maybe" class="contrast">Je vais peut-être participer</button>
                        <?php } ?>
                        <?php if ($participe != 'ko') { ?>
                            <button type="submit" name="participe" value="ko" class="outline contrast">Je ne participe pas</button>
                        <?php } ?>
                    <?php } ?>
                </form>
            </div>
            <div>
                <div class="duotone"><img src="<?= $evenement['image_url']; ?>"></div>
                <img src="<?= $evenement['logo'] ? $evenement['logo'] :  './img/logo.png'; ?>">
            </div>
        </article>
    </main>
    <!-- ./ Main -->


</body>

</html>