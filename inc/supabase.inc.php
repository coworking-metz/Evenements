<?php
include __DIR__ . '/supabase.class.php';

function supabase()
{
    if (empty($GLOBALS['supabase'])) {
        $GLOBALS['supabase'] = new SupabaseClient(
            SUPABASE_API_URL,
            SUPABASE_API_KEY
        );
    }
    return $GLOBALS['supabase'];
}

function getParticipations($id_evenement)
{
    $criteria = ['id_evenement' => $id_evenement];
    $liste =  supabase()->read('participations', $criteria);
    usort($liste, function($a, $b) {
        $priority = ['ok' => 1, 'maybe' => 2, 'ko' => 3];
        return $priority[$a['participe']] <=> $priority[$b['participe']];
    });
    return $liste;
}

function getEvenement($id)
{
    if (!$id) return;
    $criteria = [
        'id' => $id
    ];
    return prepareEvenement(supabase()->read('evenements', $criteria)[0] ?? false);
}

function prepareEvenement($evenement)
{
    if ($evenement['image_url']) {

        $hash = sha1($evenement['image_url']);
        $file = realpath(__DIR__ . '/../tmp/') . '/' . $hash . '.jpg';
        if (!file_exists($file)) {
            $c = file_get_contents($evenement['image_url']);
            if ($c) {
                // Resize and compress image
                $original = imagecreatefromstring($c);
                $width = imagesx($original);
                $height = imagesy($original);
                $new_width = 1200;
                $new_height = intval($new_width * ($height / $width));
                $new_image = imagecreatetruecolor($new_width, $new_height);
                imagecopyresampled($new_image, $original, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
                imagejpeg($new_image, $file, 50);
                imagedestroy($original);
                imagedestroy($new_image);
            }
        }
        $evenement['image_url'] = baseUrl() . 'tmp/' . $hash . '.jpg';
    }
    return $evenement;
}
function getEvenements()
{
    $criteria = [
        'order' => 'id.desc'
    ];
    return supabase()->read('evenements', $criteria);
}
function  upsertEvenement($data)
{

    $criteria = ['id' => $data['id'] ?? false];
    $data['ok'] = $data['ok'] ?? 0;
    $data['ko'] = $data['ko'] ?? 0;
    $data['maybe'] = $data['maybe'] ?? 0;
    unset($data['id']);
    $data['withdate']= !!($data['withdate']??false);
    if($data['withdate']) {
        if(!$data['heure']) {
            $data['heure']=null;
        }
    }
    $data['calendrier']= !!($data['calendrier']??false);
    $response = supabase()->upsert('evenements', $data, $criteria);
    if (!empty($response['id'])) {
        return $response;
    }

    $ret = getEvenement($criteria['id']);
    if ($ret) {
        return $ret;
    } else {
        me($ret, $data, $response);
    }
}
function getParticipation($email, $id_evenement)
{
    $criteria = ['id_evenement' => $id_evenement, 'email' => $email];
    return supabase()->read('participations', $criteria)[0] ?? false;
}
function  upsertParticipation($id_evenement, $data)
{
    $criteria = ['email' => $data['email'], 'id_evenement' => $id_evenement];
    $response = supabase()->upsert('participations', $data, $criteria);
    if (!empty($response['id'])) {
        return $response;
    }
    return getParticipation($criteria['email'], $id_evenement);
}
function hashEvenement($data)

{
    return md5($data['evenement'] . $data['date'] . $data['heure'] . $data['id']);
}

function texteParticipation($participation)
{
    if (empty($participation['participe'])) return 'Vous n\'avez pas encore répondu';
    if ($participation['participe'] == 'ok') {
        $ret = 'Vous avez confirmé votre participation 👍';

        if ($participation['nb'] > 1) {
            $ret .= '<small>Vous venez accompagné de ' . ($participation['nb'] - 1) . ' personne(s).</small>';
        }
        return $ret;
    }
    if ($participation['participe'] == 'ko') return 'Vous avez indiqué ne pas participer à cet événement 👎';
    if ($participation['participe'] == 'maybe') return 'Vous avez répondu que vous allez peut-être participer 🤔';
}
/*
function getAbonnementByStripeId($stripe_id)
{
    if (!$stripe_id) return;
    return supabase()->read('abonnements', ['stripe_id' => $stripe_id])[0] ?? null;
}
function getClientByStripeId($stripe_id)
{
    if (!$stripe_id) return;
    return supabase()->read('clients', ['stripe_id' => $stripe_id])[0] ?? null;
}

function getClientById($id)
{
    if (!$id) return;
    return supabase()->read('clients', ['id' => $id])[0] ?? null;
}

function updateAbonnement($data, $id)
{

    $criteria = ['id' => $id];
    $response = supabase()->update('abonnements', $data, $criteria);
    print_r($response);
    exit;
    $abonnement = supabase()->read('abonnements', $criteria)[0]??false;
    return $abonnement;
}

function insertAbonnement($subscription)
{
    $client = getClientByStripeId($subscription['customer']);
    if (!$client) return;

    $data = [
        'commande' => $subscription,
        'client_id' => $client['id'],
        'stripe_id' => $subscription->id,
    ];
    $criteria = ['stripe_id' => $data['stripe_id']];

    supabase()->upsert('abonnements', $data, $criteria);
    $abonnement = supabase()->read('abonnements', $criteria)[0]??false;
    return $abonnement;
}

function  upsertSite($site)
{
    $site['slug'] = slugify($site['nom']);
    if (empty($site['slug'])) return;

    $criteria = ['slug' => $site['slug']];
    $response = supabase()->upsert('sites', $site, $criteria);
    $site = supabase()->read('sites', $criteria)[0]??false;
    return $site;
}
function upsertClient($customer)
{
    if (!$customer->email ?? false) return;
    // Construct the data payload for Supabase
    $data = [
        'nom' => $customer->name,
        'email' => $customer->email,
        'stripe_id' => $customer->id,
        'adresse' => $customer->address,
    ];
    $criteria = ['stripe_id' => $data['stripe_id']];
    $response = supabase()->upsert('clients', $data, $criteria);
    $client = supabase()->read('clients', $criteria)[0]??false;
    return $client;
}
*/