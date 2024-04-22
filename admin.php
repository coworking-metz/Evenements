<?php

include __DIR__ . '/inc/main.inc.php';

$form = $_POST['form'] ?? [];

if ($form) {
    $evenement = upsertEvenement($form);

    rediriger('admin.php?id=' . $evenement['id']);
}
$evenements = getEvenements();
$new = isset($_GET['new']);

$id = $_GET['id'] ?? false;
if ($evenement = getEvenement($id)) {
    $participations = getParticipations($id);
    $titre = $evenement['evenement'];
} else {
    $titre = 'Nouvel évenement';
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($titre); ?> - Evenements</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@picocss/pico@1/css/pico.min.css" />
    <!-- Add this script at the end of your body or in the head -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const imageInput = document.querySelector('#image');
            const imageContainer = document.getElementById('imageContainer');
            const imageUrl = document.querySelector('[name="form[image_url]"]');

            imageInput.addEventListener('keydown', function(event) {
                if (event.key === 'Enter') {
                    event.preventDefault();
                }
            });

            function updateImage() {
                if (imageInput.value) {
                    fetch(`https://tools.sopress.net/unsplash/?json=true&one=${imageInput.value}`).then((response) => response.json()).then(response => {
                        imageUrl.value = response.url;
                        imageContainer.innerHTML = `<img src="${response.url}" alt="⌛">`;
                    });
                } else if (imageUrl.value) {
                    imageContainer.innerHTML = `<img src="${imageUrl.value}" alt="⌛">`;
                }
            }

            // Initial image load
            updateImage();

            let sti = false;
            // Event listener
            imageInput.addEventListener('input', function() {
                clearTimeout(sti);
                sti = setTimeout(updateImage, 500)
            });
        });
    </script>
    <style>
        #imageContainer {
            width: 150px;
            height: 150px;
        }

        #imageContainer img {
            width: 100%;
            height: 100%;
            display: block;
            object-fit: cover;
        }

        .participations {
            font-size: small;
            max-height: 60vh;
            overflow-y: auto;
        }

        article.selected {
            border: 2px solid #333;
        }

        article.past {
            opacity: 0.6;
        }

        a[target="_blank"]::after {
            content: '⇗';
            margin-left: 0.5em;
            font-size: 0.75em;
            vertical-align: middle;
            border: 1px solid;
            width: 1.1em;
            height: 1.1em;
            display: inline-block;
            text-align: center;
            line-height: 0.8em;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="grid">
            <?php if ($id || $new) { ?>
                <article>
                    <form method="post">
                        <input type="hidden" name="form[id]" required value="<?= htmlspecialchars($evenement['id'] ?? ''); ?>">
                        <h1><?= htmlspecialchars($titre); ?>
                            <br><small><?= participationEvenement($evenement) ?></small>
                        </h1>

                        <div>
                            <label for="evenement">Nom de l'événement</label>
                            <input type="text" name="form[evenement]" required value="<?= htmlspecialchars($evenement['evenement'] ?? ''); ?>">
                        </div>
                        <div>
                            <label for="description">Description</label>
                            <textarea name="form[description]"><?= htmlspecialchars($evenement['description'] ?? ''); ?></textarea>
                            <small>Facultatif</small>
                        </div>
                        <div>
                            <label for="lieu">Lieu</label>
                            <input type="text" name="form[lieu]" value="<?= htmlspecialchars($evenement['lieu'] ?? ''); ?>">
                            <small>Facultatif</small>
                        </div>

                        <div>
                            <label for="date">Date</label>
                            <input type="date" name="form[date]" required value="<?= htmlspecialchars($evenement['date'] ?? ''); ?>">
                        </div>

                        <div>
                            <label for="heure">Heure</label>
                            <input type="time" name="form[heure]" value="<?= htmlspecialchars($evenement['heure'] ?? ''); ?>">
                            <small>Facultatif</small>
                        </div>

                        <div>
                            <label for="couleur">couleur</label>
                            <input type="text" name="form[couleur]" value="<?= htmlspecialchars($evenement['couleur'] ?? ''); ?>" oninput="this.closest('div').querySelector('[type=color]').value = this.value">
                            <input type="color" value="<?= htmlspecialchars($evenement['couleur'] ?? ''); ?>" oninput="this.closest('div').querySelector('[type=text]').value = this.value">
                        </div>


                        <div>
                            <label for="logo">Logo</label>
                            <select name="form[logo]"><?php foreach (logos() as $logo) { ?>
                                    <option <?= $logo['url'] == ($evenement['logo']??false) ? 'selected' : ''; ?> value="<?= $logo['url']; ?>"><?= $logo['nom']; ?></option>
                                <?php } ?>
                            </select>
                        </div>

                        <div>
                            <label for="image">Rechercher une image</label>
                            <input type="text" id="image" value="">
                            <small>Saisir quelques mots clés pour choisir une image</small>
                            <div id="imageContainer"></div>
                            <input type="hidden" name="form[image_url]" value="<?= htmlspecialchars($evenement['image_url'] ?? ''); ?>">
                        </div>
                        <BR>
                        <button type="submit">Enregistrer</button>
                    </form>
                </article>
            <?php } ?>
            <div>
                <?php if ($evenement['id'] ?? false) { ?>
                    <article>
                        <b>Participations</b>
                        <div class="participations">
                            <?php if ($participations) { ?>
                                <?php foreach ($participations as $participation) { ?>
                                    <div>
                                        <?= $participation['participe'] == 'ok' ? '👍' : '' ?>
                                        <?= $participation['participe'] == 'ko' ? '🚫' : '' ?>
                                        <?= $participation['participe'] == 'maybe' ? '🤔' : '' ?>
                                        <b><?= strstr($participation['email'], '@') ? $participation['email'] : '<i>Anonyme</i>'; ?></b> <?= $participation['nb'] > 1 ? ' + ' . $participation['nb'] . ' accompagnateur(s)' : ''; ?>
                                    </div>
                                <?php } ?>
                            <?php } else { ?>
                                Pas de réponses pour l'instant
                            <?php } ?>
                        </div>
                    </article>
                    <article>
                        <div>
                            <label>Url à partager pour que les gens indiquent leur participation
                            <a target="_blank" href="<?= urlEvenement($evenement); ?>">Voir la page de l'évènement</a></label>
                            <code><?= urlEvenement($evenement); ?></code>
                            <b>Liens rapides :</b>
                            <a href="<?= urlEvenement($evenement); ?>?p=ok&email={{contact.EMAIL}}">oui</a> |
                            <a href="<?= urlEvenement($evenement); ?>?p=ko&email={{contact.EMAIL}}">non</a> |
                            <a href="<?= urlEvenement($evenement); ?>?p=maybe&email={{contact.EMAIL}}">peut-être</a>
                        </div><br>
                        <figure>
                            <img width="150" src="https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=<?= urlEvenement($evenement); ?>">
                        </figure>
                    </article>
                <?php } ?>

                <article>
                    <a role="button" href="admin.php?new">Créer un évenement</a>
                </article>
                <?php foreach ($evenements as $e) { ?>
                    <article class="<?= $e['id'] == $id ? 'selected' : ''; ?> <?= $e['date'] < date('Y-m-d') ? 'past' : ''; ?>">
                        <a href="admin.php?id=<?= $e['id']; ?>"><?= descriptionEvenement($e); ?></a>
                        <br><small><?= participationEvenement($e, true) ?></small>
                        <div><a href="admin.php?id=<?= $e['id']; ?>">✏️</a> <a href="<?= urlEvenement($e['id']); ?>">👀</a></div>
                    </article>
                <?php } ?>

            </div>
        </div>
    </div>
</body>

</html>