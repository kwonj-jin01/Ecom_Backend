<?php

namespace App\Filament\Widgets;

use App\Models\User;
use Filament\Widgets\Widget;

class AnimatedStatsWidget extends Widget
{
    protected static string $view = 'filament.widgets.animated-stats-widget';
    protected int | string | array $columnSpan = 'full';

    // Ordre d'affichage sur le dashboard
    protected static ?int $sort = 1;

    public function getStats(): array
    {
        return [
            [
                'value' => 125000,           // Valeur à animer
                'label' => 'Total Ventes',   // Label principal
                'currency' => '€',           // Devise
                'subtitle' => '0 F CFA',     // Sous-titre
                'description' => 'Chiffre d\'affaires', // Description
                'color' => '#3b82f6',        // Couleur de la carte
                'style' => 'primary',        // Style (pour extensions futures)
                'chart' => [5, 9, 7, 6, 10, 12, 14], // 👈 ajout du graphique
            ],
            [
                'value' => 125000,           // Valeur à animer
                'label' => 'Total Ventes',   // Label principal
                'currency' => '€',           // Devise
                'subtitle' => '0 F CFA',     // Sous-titre
                'description' => 'Chiffre d\'affaires', // Description
                'color' => '#3b82f6',        // Couleur de la carte
                'style' => 'primary',        // Style (pour extensions futures)
                'chart' => [5, 9, 7, 6, 10, 12, 14], // 👈 ajout du graphique
            ],
            [
                'value' => 125000,           // Valeur à animer
                'label' => 'Total Ventes',   // Label principal
                'currency' => '€',           // Devise
                'subtitle' => '0 F CFA',     // Sous-titre
                'description' => 'Chiffre d\'affaires', // Description
                'color' => '#3b82f6',        // Couleur de la carte
                'style' => 'primary',        // Style (pour extensions futures)
                'chart' => [5, 9, 7, 6, 10, 12, 14], // 👈 ajout du graphique
            ],
            [
                'value' => 125000,           // Valeur à animer
                'label' => 'Total Ventes',   // Label principal
                'currency' => '€',           // Devise
                'subtitle' => '0 F CFA',     // Sous-titre
                'description' => 'Chiffre d\'affaires', // Description
                'color' => '#3b82f6',        // Couleur de la carte
                'style' => 'primary',        // Style (pour extensions futures)
                'chart' => [5, 9, 7, 6, 10, 12, 14], // 👈 ajout du graphique
            ],
            [
                'value' => 125000,           // Valeur à animer
                'label' => 'Total Ventes',   // Label principal
                'currency' => '€',           // Devise
                'subtitle' => '0 F CFA',     // Sous-titre
                'description' => 'Chiffre d\'affaires', // Description
                'color' => '#3b82f6',        // Couleur de la carte
                'style' => 'primary',        // Style (pour extensions futures)
                'chart' => [5, 9, 7, 6, 10, 12, 14], // 👈 ajout du graphique
            ],


        ];
    }
}
