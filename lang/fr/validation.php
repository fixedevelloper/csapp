<?php

// Messages de validation en français, utilisés par l'API d'administration (middleware AdminLocale)
return [
    'array' => 'Le champ :attribute doit être une liste.',
    'boolean' => 'Le champ :attribute doit être vrai ou faux.',
    'email' => 'Le champ :attribute doit être une adresse e-mail valide.',
    'exists' => 'La valeur choisie pour :attribute est invalide.',
    'image' => 'Le champ :attribute doit être une image.',
    'in' => 'La valeur choisie pour :attribute est invalide.',
    'integer' => 'Le champ :attribute doit être un nombre entier.',
    'max' => [
        'string' => 'Le champ :attribute ne peut pas dépasser :max caractères.',
        'file' => 'Le fichier :attribute ne peut pas dépasser :max Ko.',
        'numeric' => 'Le champ :attribute ne peut pas dépasser :max.',
        'array' => 'Le champ :attribute ne peut pas avoir plus de :max éléments.',
    ],
    'mimes' => 'Le fichier :attribute doit être de type : :values.',
    'min' => [
        'string' => 'Le champ :attribute doit contenir au moins :min caractères.',
        'file' => 'Le fichier :attribute doit faire au moins :min Ko.',
        'numeric' => 'Le champ :attribute doit être au moins :min.',
        'array' => 'Le champ :attribute doit avoir au moins :min éléments.',
    ],
    'required' => 'Le champ :attribute est obligatoire.',
    'string' => 'Le champ :attribute doit être une chaîne de caractères.',
    'unique' => 'Cette valeur existe déjà pour :attribute.',
    'uploaded' => 'Le téléversement de :attribute a échoué.',

    'attributes' => [
        'name' => 'nom',
        'title' => 'titre',
        'excerpt' => 'extrait',
        'content' => 'contenu',
        'meta_title' => 'titre SEO',
        'meta_description' => 'description SEO',
        'meta_keywords' => 'mots-clés',
        'categories' => 'catégories',
        'categories.*' => 'catégorie',
        'tags' => 'tags',
        'tags.*' => 'tag',
        'image' => 'image',
        'status' => 'statut',
        'email' => 'adresse e-mail',
        'password' => 'mot de passe',
        'is_read' => 'lu',
    ],
];
