<?php

class SupabaseClient
{
    private $endpoint;
    private $apiKey;

    public function __construct($endpoint, $apiKey)
    {
        $this->endpoint = $endpoint;
        $this->apiKey = $apiKey;
    }

    // Fonction pour effectuer une requête à Supabase
    private function request($method, $path, $data = [], $headers = [])
    {
        // Initialiser cURL
        $ch = curl_init();

       $url = $this->endpoint . '/rest/v1' . $path;

        // Options de base pour cURL
        $options = [
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_URL => $url,
            CURLOPT_HTTPHEADER => array_merge([
                'apikey: ' . $this->apiKey,
                'Content-Type: application/json'
            ], $headers),
            CURLOPT_CUSTOMREQUEST => $method,
        ];

        if (!empty($data)) {
            $options[CURLOPT_POSTFIELDS] = json_encode($data);
        }

        curl_setopt_array($ch, $options);

        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            // Erreur de cURL
            $error_msg = curl_error($ch);
            curl_close($ch);
            throw new Exception($error_msg);
        }

        curl_close($ch);
        return json_decode($response, true);
    }

    // Créer une entrée
    public function create($table, $data, $headers = [])
    {
        $response = $this->request('POST', '/' . $table, $data, $headers);
        if(!empty($response['message']) && !empty($response['code'])) me([$response,$data]);
        return $response;
    }

    // Lire des entrées avec tri par défaut sur 'id' en ordre décroissant
    public function read($table, $criteria = [], $order = 'id.desc')
    {
        if (is_array($criteria)) {
            $criteria = array_map(function ($item) {
                return 'eq.' . $item;
            }, $criteria);
            $criteria['order'] = $order;
            $criteria = http_build_query($criteria);
        }

        return $this->request('GET', '/' . $table . (empty($criteria) ? "" : "?" . $criteria));
    }


    // Mettre à jour une entrée
    public function update($table, $data, $criteria)
    {
        if (is_array($criteria)) {
            $criteria = http_build_query(array_map(function ($item) {
                return 'eq.' . $item;
            }, $criteria));
        }
        return $this->request('PATCH', '/' . $table . "?" . $criteria, $data);
    }
    // Insérer ou mettre à jour une entrée
    public function upsert($table, $data, $criteria)
    {
        if (is_array($criteria) && count(array_filter($criteria))) {
            $criteria = http_build_query(array_map(function ($item) {
                return 'eq.' . $item;
            }, $criteria));

            // Effectuer une requête de lecture pour vérifier si l'entrée existe
            $response = $this->read($table, $criteria);
        }
        // Vérifier si une ligne correspondante a été trouvée
        if ($id = $response[0]['id'] ?? false) {
            return $this->update($table, $data, ['id' => $id]);
        } else {
            $headers = ["Prefer: return=representation"];
            // Ligne non trouvée, insertion
            return $this->create($table, $data, $headers)[0] ?? false;
        }
    }
    // Supprimer une entrée
    public function delete($table, $criteria)
    {
        if (is_array($criteria)) {
            $criteria = http_build_query(array_map(function ($item) {
                return 'eq.' . $item;
            }, $criteria));
        }

        return $this->request('DELETE', '/' . $table . "?" . $criteria);
    }
}
