<?php

namespace App\Support;

use Illuminate\Validation\Rule;

/**
 * Questions du formulaire de devis par type de projet.
 * Source unique pour la validation, l'e-mail et l'affichage admin.
 * Types de champ : choice (une valeur), multi (plusieurs valeurs), text, url.
 */
class DevisSchema
{
    public const DEADLINES = ['Urgent (moins d\'1 mois)', '1 à 3 mois', 'Plus de 3 mois', 'Flexible'];

    public static function services(): array
    {
        $yesNo = ['Oui', 'Non'];

        return [
            'Site web' => [
                'site_kind' => ['label' => 'Nouveau site ou refonte', 'type' => 'choice', 'options' => ['Nouveau site', 'Refonte d\'un site existant']],
                'existing_url' => ['label' => 'Adresse du site actuel', 'type' => 'url'],
                'site_type' => ['label' => 'Type de site', 'type' => 'choice', 'options' => ['Site vitrine', 'Site d\'entreprise', 'Prise de rendez-vous / réservation', 'Blog / média', 'Autre']],
                'pages' => ['label' => 'Nombre de pages', 'type' => 'choice', 'options' => ['1 à 5', '6 à 10', '11 à 20', 'Plus de 20']],
                'features' => ['label' => 'Fonctionnalités', 'type' => 'multi', 'options' => ['Blog', 'Multilingue', 'Formulaire de réservation', 'Espace membre', 'Galerie / portfolio', 'Chat WhatsApp']],
                'content' => ['label' => 'Textes et images', 'type' => 'choice', 'options' => ['Je les fournis', 'À produire par vous', 'Un peu des deux']],
                'domain_hosting' => ['label' => 'Nom de domaine et hébergement', 'type' => 'choice', 'options' => ['Déjà acquis', 'À prendre en charge', 'Je ne sais pas']],
            ],
            'E-commerce' => [
                'shop_kind' => ['label' => 'Boutique nouvelle ou existante', 'type' => 'choice', 'options' => ['Nouvelle boutique', 'Boutique existante à refaire']],
                'existing_url' => ['label' => 'Adresse de la boutique actuelle', 'type' => 'url'],
                'products' => ['label' => 'Nombre de produits', 'type' => 'choice', 'options' => ['Moins de 50', '50 à 200', '200 à 1000', 'Plus de 1000']],
                'payments' => ['label' => 'Moyens de paiement', 'type' => 'multi', 'options' => ['Mobile Money', 'Carte bancaire', 'Paiement à la livraison', 'Virement']],
                'delivery' => ['label' => 'Livraison', 'type' => 'choice', 'options' => ['Ville uniquement', 'National', 'International', 'Retrait en magasin']],
                'stock' => ['label' => 'Gestion de stock et variantes', 'type' => 'choice', 'options' => $yesNo],
                'training' => ['label' => 'Formation à l\'administration', 'type' => 'choice', 'options' => $yesNo],
            ],
            'Application mobile' => self::application(),
            'Logiciel métier / SaaS' => self::application(),
            'Design graphique / UI-UX' => [
                'deliverables' => ['label' => 'Livrables', 'type' => 'multi', 'options' => ['Logo', 'Charte graphique', 'Maquettes web', 'Maquettes mobile', 'Refonte d\'interface', 'Supports print']],
                'screens' => ['label' => 'Nombre d\'écrans ou de supports', 'type' => 'choice', 'options' => ['1 à 5', '6 à 15', '16 à 30', 'Plus de 30']],
                'brand_guide' => ['label' => 'Charte graphique existante', 'type' => 'choice', 'options' => $yesNo],
                'reference_url' => ['label' => 'Exemple apprécié', 'type' => 'url'],
            ],
            'Marketing digital / SEO' => [
                'goal' => ['label' => 'Objectif', 'type' => 'choice', 'options' => ['Visibilité', 'Générer des contacts', 'Augmenter les ventes']],
                'existing_url' => ['label' => 'Adresse du site actuel', 'type' => 'url'],
                'channels' => ['label' => 'Canaux', 'type' => 'multi', 'options' => ['Référencement (SEO)', 'Réseaux sociaux', 'Publicité en ligne', 'E-mailing']],
                'target' => ['label' => 'Zone cible', 'type' => 'choice', 'options' => ['Cameroun', 'Afrique', 'International']],
                'ad_budget' => ['label' => 'Budget publicitaire mensuel', 'type' => 'choice', 'options' => ['Aucun', 'Moins de 100 000 FCFA', '100 000 à 500 000 FCFA', 'Plus de 500 000 FCFA']],
                'engagement' => ['label' => 'Type de mission', 'type' => 'choice', 'options' => ['Suivi mensuel', 'Mission ponctuelle']],
            ],
        ];
    }

    private static function application(): array
    {
        return [
            'platforms' => ['label' => 'Plateformes', 'type' => 'multi', 'options' => ['Web', 'Android', 'iOS']],
            'audience' => ['label' => 'Public visé', 'type' => 'choice', 'options' => ['Usage interne', 'Clients', 'Grand public']],
            'features' => ['label' => 'Fonctionnalités principales', 'type' => 'multi', 'options' => ['Comptes utilisateurs', 'Paiement en ligne', 'Notifications', 'Tableau de bord', 'Messagerie', 'Géolocalisation']],
            'integrations' => ['label' => 'Intégrations ou API existantes', 'type' => 'choice', 'options' => ['Oui', 'Non', 'Je ne sais pas']],
            'specs' => ['label' => 'Cahier des charges ou maquettes', 'type' => 'choice', 'options' => ['Disponibles', 'Partiels', 'Aucun']],
        ];
    }

    /** Types de projet pour lesquels la description libre n'est pas obligatoire. */
    public static function guidedTypes(): array
    {
        return array_keys(self::services());
    }

    /** Règles de validation de `details.*` pour un type de projet (vide si type inconnu). */
    public static function rules(?string $projectType): array
    {
        $fields = self::services()[$projectType] ?? null;
        if (!$fields) {
            return [];
        }

        $rules = ['details' => ['nullable', 'array']];
        foreach ($fields as $key => $f) {
            $path = "details.$key";
            switch ($f['type']) {
                case 'choice':
                    $rules[$path] = ['nullable', 'string', Rule::in($f['options'])];
                    break;
                case 'multi':
                    $rules[$path] = ['nullable', 'array', 'max:' . count($f['options'])];
                    $rules["$path.*"] = ['string', 'distinct', Rule::in($f['options'])];
                    break;
                case 'url':
                    $rules[$path] = ['nullable', 'string', 'max:255', 'url'];
                    break;
                default:
                    $rules[$path] = ['nullable', 'string', 'max:500'];
            }
        }

        return $rules;
    }

    /** Réponses lisibles [{label, value}] pour l'e-mail et l'admin ; ignore les clés inconnues et les valeurs vides. */
    public static function present(?string $projectType, ?array $details): array
    {
        $fields = self::services()[$projectType] ?? [];
        $out = [];
        foreach ($fields as $key => $f) {
            $v = $details[$key] ?? null;
            if (is_array($v)) {
                $v = implode(', ', array_filter($v, 'is_string'));
            }
            if (is_string($v) && trim($v) !== '') {
                $out[] = ['label' => $f['label'], 'value' => $v];
            }
        }

        return $out;
    }
}
