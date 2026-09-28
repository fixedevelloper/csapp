<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Reverse proxys de confiance
    |--------------------------------------------------------------------------
    |
    | Lu par le middleware TrustProxies à chaque requête. Derrière un reverse
    | proxy / Cloudflare, sans ce réglage $request->ip() renvoie l'IP du proxy
    | et tous les visiteurs partagent la même limite des formulaires publics.
    | Ne lister que des proxys réellement devant le serveur (IP séparées par
    | des virgules, ou * si le serveur n'est joignable que via le proxy) :
    | sinon l'en-tête X-Forwarded-For est falsifiable.
    |
    */

    'proxies' => env('TRUSTED_PROXIES'),

];
