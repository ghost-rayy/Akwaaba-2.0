<?php

namespace App\Support;

class CompanyThemes
{
    public const DEFAULT = 'stormy';

    /**
     * @return array<string, array{label: string, description: string, swatch: string}>
     */
    public static function options(): array
    {
        return [
            'stormy' => [
                'label' => 'Stormy Blue',
                'description' => 'Default blue-gray brand palette',
                'swatch' => '#55708a',
            ],
            'emerald' => [
                'label' => 'Emerald',
                'description' => 'Fresh green for growth-focused brands',
                'swatch' => '#059669',
            ],
            'indigo' => [
                'label' => 'Indigo',
                'description' => 'Deep indigo with a modern tech feel',
                'swatch' => '#4f46e5',
            ],
            'violet' => [
                'label' => 'Violet',
                'description' => 'Rich purple accent theme',
                'swatch' => '#7c3aed',
            ],
            'rose' => [
                'label' => 'Rose',
                'description' => 'Warm rose and burgundy tones',
                'swatch' => '#e11d48',
            ],
            'teal' => [
                'label' => 'Teal',
                'description' => 'Calm teal for a clean corporate look',
                'swatch' => '#0d9488',
            ],
            'navy' => [
                'label' => 'Navy',
                'description' => 'Classic corporate deep navy blue',
                'swatch' => '#2c4478',
            ],
            'sky' => [
                'label' => 'Sky Blue',
                'description' => 'Bright, approachable blue',
                'swatch' => '#0284c7',
            ],
            'cyan' => [
                'label' => 'Cyan',
                'description' => 'Cool aqua with a technical feel',
                'swatch' => '#0891b2',
            ],
            'forest' => [
                'label' => 'Forest Green',
                'description' => 'Deeper natural green',
                'swatch' => '#16a34a',
            ],
            'amber' => [
                'label' => 'Amber Gold',
                'description' => 'Warm gold accents',
                'swatch' => '#d97706',
            ],
            'orange' => [
                'label' => 'Sunset Orange',
                'description' => 'Energetic orange palette',
                'swatch' => '#ea580c',
            ],
            'crimson' => [
                'label' => 'Crimson',
                'description' => 'Bold, confident red',
                'swatch' => '#dc2626',
            ],
            'fuchsia' => [
                'label' => 'Fuchsia',
                'description' => 'Vivid magenta for creative brands',
                'swatch' => '#c026d3',
            ],
            'slate' => [
                'label' => 'Graphite',
                'description' => 'Neutral slate gray, minimal look',
                'swatch' => '#475569',
            ],
            'bronze' => [
                'label' => 'Bronze',
                'description' => 'Earthy brown and bronze tones',
                'swatch' => '#855e3a',
            ],
            'lime' => [
                'label' => 'Lime',
                'description' => 'Bright citrus green energy',
                'swatch' => '#65a30d',
            ],
            'mint' => [
                'label' => 'Mint',
                'description' => 'Soft mint for a light, airy brand',
                'swatch' => '#10b981',
            ],
            'sapphire' => [
                'label' => 'Sapphire',
                'description' => 'Jewel-tone deep sapphire blue',
                'swatch' => '#1d4ed8',
            ],
            'cobalt' => [
                'label' => 'Cobalt',
                'description' => 'Strong cobalt blue presence',
                'swatch' => '#1440b4',
            ],
            'plum' => [
                'label' => 'Plum',
                'description' => 'Deep plum purple elegance',
                'swatch' => '#9333ea',
            ],
            'lavender' => [
                'label' => 'Lavender',
                'description' => 'Soft lavender with a calm tone',
                'swatch' => '#8b5cf6',
            ],
            'coral' => [
                'label' => 'Coral',
                'description' => 'Warm coral pink accent',
                'swatch' => '#f43f5e',
            ],
            'peach' => [
                'label' => 'Peach',
                'description' => 'Soft peach and apricot warmth',
                'swatch' => '#f97316',
            ],
            'wine' => [
                'label' => 'Wine',
                'description' => 'Deep wine red for premium brands',
                'swatch' => '#9f1239',
            ],
            'charcoal' => [
                'label' => 'Charcoal',
                'description' => 'Near-black charcoal, ultra minimal',
                'swatch' => '#374151',
            ],
            'olive' => [
                'label' => 'Olive',
                'description' => 'Muted olive green, natural feel',
                'swatch' => '#4d7c0f',
            ],
            'sand' => [
                'label' => 'Sand',
                'description' => 'Warm sandy beige neutrals',
                'swatch' => '#a8a29e',
            ],
            'aqua' => [
                'label' => 'Aqua',
                'description' => 'Bright aqua and turquoise',
                'swatch' => '#06b6d4',
            ],
            'magenta' => [
                'label' => 'Magenta',
                'description' => 'Electric magenta highlight',
                'swatch' => '#db2777',
            ],
            'midnight' => [
                'label' => 'Midnight',
                'description' => 'Near-black midnight blue',
                'swatch' => '#1e3a5f',
            ],
        ];
    }

    public static function keys(): array
    {
        return array_keys(self::options());
    }

    public static function normalize(?string $theme): string
    {
        $theme = $theme ?: self::DEFAULT;

        return in_array($theme, self::keys(), true) ? $theme : self::DEFAULT;
    }
}
