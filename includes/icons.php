<?php
function get_icon($name, $size = '24px', $color = 'currentColor') {
    $svg_start = '<svg viewBox="0 0 100 120" stroke="'.$color.'" stroke-width="4" stroke-linecap="round" stroke-linejoin="round" fill="none" width="'.$size.'" height="'.$size.'" style="display:inline-block; vertical-align:middle;">';
    $svg_end = '</svg>';

    $paths = '';
    switch ($name) {
        case 'leaf':
            // Simple feuille pour le logo et le badge
            $paths = '
                <path d="M50 95 C 10 95 10 30 50 15 C 90 30 90 95 50 95 Z" fill="var(--caribbean)" opacity="0.2" stroke="none" />
                <path d="M50 95 C 10 95 10 30 50 15 C 90 30 90 95 50 95 Z" />
                <line x1="50" y1="95" x2="50" y2="40" />
                <line x1="50" y1="75" x2="30" y2="60" />
                <line x1="50" y1="60" x2="70" y2="45" />
            ';
            break;
            
        case 'plante':
            // Stickman plantant une graine / plante
            $paths = '
                <circle cx="40" cy="40" r="12" />
                <line x1="40" y1="52" x2="40" y2="85" />
                <line x1="40" y1="65" x2="20" y2="75" />
                <line x1="40" y1="65" x2="60" y2="75" />
                <line x1="40" y1="85" x2="25" y2="110" />
                <line x1="40" y1="85" x2="55" y2="110" />
                <path d="M70 110 Q80 80 90 70 M70 110 Q85 100 95 95" stroke="var(--mountain)" />
            ';
            break;
            
        case 'search':
            // Loupe simple
            $paths = '
                <circle cx="45" cy="45" r="25" />
                <line x1="63" y1="63" x2="85" y2="85" stroke-width="8" stroke-linecap="round" />
                <circle cx="45" cy="45" r="15" stroke="none" fill="var(--caribbean)" opacity="0.2" />
            ';
            break;

        case 'shield':
            // Stickman tenant un bouclier
            $paths = '
                <circle cx="60" cy="30" r="12" />
                <line x1="60" y1="42" x2="60" y2="75" />
                <line x1="60" y1="55" x2="40" y2="65" />
                <line x1="60" y1="55" x2="80" y2="65" />
                <line x1="60" y1="75" x2="45" y2="105" />
                <line x1="60" y1="75" x2="75" y2="105" />
                <path d="M20 40 L40 40 L40 70 Q40 90 30 100 Q20 90 20 70 Z" fill="var(--caribbean)" opacity="0.1" stroke="none"/>
                <path d="M20 40 L40 40 L40 70 Q40 90 30 100 Q20 90 20 70 Z" />
            ';
            break;

        case 'clock':
            // Stickman pointant une horloge
            $paths = '
                <circle cx="30" cy="40" r="12" />
                <line x1="30" y1="52" x2="30" y2="85" />
                <line x1="30" y1="65" x2="50" y2="55" />
                <line x1="30" y1="65" x2="15" y2="75" />
                <line x1="30" y1="85" x2="15" y2="110" />
                <line x1="30" y1="85" x2="45" y2="110" />
                <circle cx="70" cy="45" r="20" />
                <line x1="70" y1="45" x2="70" y2="30" />
                <line x1="70" y1="45" x2="80" y2="50" />
            ';
            break;

        case 'truck':
            // Stickman conduisant un camion
            $paths = '
                <circle cx="30" cy="40" r="12" />
                <line x1="30" y1="52" x2="30" y2="80" />
                <line x1="30" y1="65" x2="55" y2="60" />
                <line x1="30" y1="80" x2="50" y2="80" />
                <line x1="50" y1="80" x2="50" y2="95" />
                <path d="M10 95 L80 95 L90 70 L60 70 L60 30 L10 30 Z" fill="var(--caribbean)" opacity="0.1" stroke="none" />
                <path d="M10 95 L80 95 L90 70 L60 70 L60 30 L10 30 Z" />
                <circle cx="60" cy="65" r="8" />
                <circle cx="25" cy="95" r="10" />
                <circle cx="75" cy="95" r="10" />
            ';
            break;

        case 'box':
            // Stickman tenant un carton
            $paths = '
                <circle cx="50" cy="30" r="15" />
                <line x1="50" y1="45" x2="50" y2="75" />
                <line x1="50" y1="55" x2="30" y2="65" />
                <line x1="50" y1="55" x2="70" y2="65" />
                <line x1="50" y1="75" x2="35" y2="95" />
                <line x1="50" y1="75" x2="65" y2="95" />
                <rect x="25" y="65" width="50" height="40" rx="4" fill="var(--caribbean)" opacity="0.1" stroke="none"/>
                <rect x="25" y="65" width="50" height="40" rx="4" />
                <line x1="40" y1="80" x2="60" y2="80" />
            ';
            break;

        case 'factory':
            // Stickman à l'usine
            $paths = '
                <circle cx="70" cy="40" r="12" />
                <line x1="70" y1="52" x2="70" y2="85" />
                <line x1="70" y1="65" x2="45" y2="65" />
                <line x1="70" y1="65" x2="85" y2="75" />
                <line x1="70" y1="85" x2="60" y2="105" />
                <line x1="70" y1="85" x2="80" y2="105" />
                <path d="M10 105 L45 105 L45 50 L30 40 L30 50 L15 40 L15 50 L10 50 Z" fill="var(--caribbean)" opacity="0.1" stroke="none" />
                <path d="M10 105 L45 105 L45 50 L30 40 L30 50 L15 40 L15 50 L10 50 Z" />
            ';
            break;

        case 'cart':
            // Stickman poussant un chariot
            $paths = '
                <circle cx="40" cy="30" r="12" />
                <line x1="40" y1="42" x2="40" y2="75" />
                <line x1="40" y1="55" x2="65" y2="60" />
                <line x1="40" y1="55" x2="30" y2="65" />
                <line x1="40" y1="75" x2="25" y2="95" />
                <line x1="40" y1="75" x2="50" y2="95" />
                <path d="M60 55 L75 55 L85 85 L55 85 Z" fill="var(--caribbean)" opacity="0.1" stroke="none" />
                <path d="M60 55 L75 55 L85 85 L55 85 Z" />
                <line x1="55" y1="85" x2="50" y2="95" />
                <line x1="85" y1="85" x2="80" y2="95" />
                <circle cx="50" cy="100" r="5" />
                <circle cx="80" cy="100" r="5" />
            ';
            break;

        case 'bag':
        case 'consumer':
            // Stickman consommateur avec téléphone/sac
            $paths = '
                <circle cx="50" cy="30" r="12" />
                <line x1="50" y1="42" x2="50" y2="75" />
                <line x1="50" y1="55" x2="65" y2="55" />
                <line x1="65" y1="55" x2="65" y2="45" />
                <line x1="50" y1="55" x2="35" y2="65" />
                <line x1="50" y1="75" x2="40" y2="105" />
                <line x1="50" y1="75" x2="60" y2="105" />
                <rect x="60" y="30" width="10" height="15" rx="2" fill="var(--surface-el)" />
            ';
            break;

        case 'farmer':
            // Stickman agriculteur / producteur
            $paths = '
                <path d="M20 30 Q50 10 80 30 L90 35 L10 35 Z" fill="var(--caribbean)" opacity="0.2" stroke="none"/>
                <path d="M20 30 Q50 10 80 30" />
                <line x1="10" y1="35" x2="90" y2="35" />
                <circle cx="50" cy="50" r="15" />
                <line x1="50" y1="65" x2="50" y2="95" />
                <line x1="50" y1="75" x2="30" y2="85" />
                <line x1="50" y1="75" x2="70" y2="85" />
                <line x1="50" y1="95" x2="35" y2="115" />
                <line x1="50" y1="95" x2="65" y2="115" />
                <path d="M70 85 Q80 70 90 60 M70 85 Q85 85 95 80" stroke="var(--mountain)" />
            ';
            break;

        case 'wave':
            // Stickman faisant signe de la main
            $paths = '
                <circle cx="50" cy="40" r="12" />
                <line x1="50" y1="52" x2="50" y2="85" />
                <line x1="50" y1="65" x2="70" y2="45" />
                <line x1="50" y1="65" x2="30" y2="75" />
                <line x1="50" y1="85" x2="35" y2="110" />
                <line x1="50" y1="85" x2="65" y2="110" />
                <path d="M70 45 Q75 40 80 45 Q85 50 80 55" />
            ';
            break;

        case 'edit':
            // Stickman avec un crayon
            $paths = '
                <circle cx="40" cy="40" r="12" />
                <line x1="40" y1="52" x2="40" y2="85" />
                <line x1="40" y1="65" x2="65" y2="65" />
                <line x1="40" y1="65" x2="25" y2="75" />
                <line x1="40" y1="85" x2="25" y2="110" />
                <line x1="40" y1="85" x2="55" y2="110" />
                <path d="M60 70 L90 40 L80 30 L50 60 Z" fill="var(--caribbean)" opacity="0.1" stroke="none" />
                <path d="M60 70 L90 40 L80 30 L50 60 Z" />
                <line x1="50" y1="60" x2="45" y2="75" />
                <line x1="60" y1="70" x2="45" y2="75" />
            ';
            break;

        case 'delete':
            // Stickman jetant à la poubelle
            $paths = '
                <circle cx="30" cy="40" r="12" />
                <line x1="30" y1="52" x2="30" y2="85" />
                <line x1="30" y1="65" x2="55" y2="55" />
                <line x1="30" y1="65" x2="15" y2="75" />
                <line x1="30" y1="85" x2="15" y2="110" />
                <line x1="30" y1="85" x2="45" y2="110" />
                <path d="M60 60 L90 60 L85 100 L65 100 Z" fill="var(--danger)" opacity="0.1" stroke="none" />
                <path d="M60 60 L90 60 L85 100 L65 100 Z" stroke="var(--danger)" />
                <line x1="75" y1="60" x2="75" y2="50" stroke="var(--danger)" />
            ';
            break;

        case 'plus':
            // Stickman tenant un signe plus
            $paths = '
                <circle cx="40" cy="40" r="12" />
                <line x1="40" y1="52" x2="40" y2="85" />
                <line x1="40" y1="65" x2="60" y2="55" />
                <line x1="40" y1="65" x2="25" y2="75" />
                <line x1="40" y1="85" x2="25" y2="110" />
                <line x1="40" y1="85" x2="55" y2="110" />
                <line x1="70" y1="40" x2="90" y2="40" stroke="var(--caribbean)" />
                <line x1="80" y1="30" x2="80" y2="50" stroke="var(--caribbean)" />
            ';
            break;

        case 'check':
            // Stickman levant le pouce (validation)
            $paths = '
                <circle cx="50" cy="40" r="12" />
                <line x1="50" y1="52" x2="50" y2="85" />
                <line x1="50" y1="65" x2="75" y2="55" />
                <line x1="50" y1="65" x2="30" y2="75" />
                <line x1="50" y1="85" x2="35" y2="110" />
                <line x1="50" y1="85" x2="65" y2="110" />
                <path d="M75 55 L85 45 L95 30" stroke="var(--caribbean)" />
            ';
            break;

        case 'warn':
            // Stickman avec un panneau d'avertissement
            $paths = '
                <circle cx="40" cy="40" r="12" />
                <line x1="40" y1="52" x2="40" y2="85" />
                <line x1="40" y1="65" x2="60" y2="65" />
                <line x1="40" y1="65" x2="20" y2="75" />
                <line x1="40" y1="85" x2="25" y2="110" />
                <line x1="40" y1="85" x2="55" y2="110" />
                <path d="M80 30 L95 60 L65 60 Z" fill="var(--warning)" opacity="0.1" stroke="none" />
                <path d="M80 30 L95 60 L65 60 Z" stroke="var(--warning)" />
                <line x1="80" y1="42" x2="80" y2="52" stroke="var(--warning)" />
                <circle cx="80" cy="56" r="1" stroke="var(--warning)" />
                <line x1="60" y1="65" x2="60" y2="110" stroke="var(--warning)" />
            ';
            break;

        case 'print':
            $paths = '
                <circle cx="30" cy="40" r="12" />
                <line x1="30" y1="52" x2="30" y2="85" />
                <line x1="30" y1="65" x2="50" y2="65" />
                <line x1="30" y1="65" x2="15" y2="75" />
                <line x1="30" y1="85" x2="15" y2="110" />
                <line x1="30" y1="85" x2="45" y2="110" />
                <rect x="55" y="55" width="40" height="20" />
                <rect x="65" y="40" width="20" height="15" stroke-dasharray="2 2" />
                <line x1="65" y1="75" x2="65" y2="90" />
                <line x1="85" y1="75" x2="85" y2="90" />
                <line x1="65" y1="90" x2="85" y2="90" />
            ';
            break;

        case 'star':
            $paths = '
                <circle cx="40" cy="40" r="12" />
                <line x1="40" y1="52" x2="40" y2="85" />
                <line x1="40" y1="65" x2="60" y2="55" />
                <line x1="40" y1="65" x2="25" y2="75" />
                <line x1="40" y1="85" x2="25" y2="110" />
                <line x1="40" y1="85" x2="55" y2="110" />
                <path d="M75 30 L80 45 L95 45 L82 55 L87 70 L75 60 L63 70 L68 55 L55 45 L70 45 Z" fill="var(--warning)" opacity="0.2" stroke="none" />
                <path d="M75 30 L80 45 L95 45 L82 55 L87 70 L75 60 L63 70 L68 55 L55 45 L70 45 Z" stroke="var(--warning)" />
            ';
            break;
            
        case 'camera':
            $paths = '
                <circle cx="30" cy="40" r="12" />
                <line x1="30" y1="52" x2="30" y2="85" />
                <line x1="30" y1="65" x2="45" y2="55" />
                <line x1="30" y1="65" x2="45" y2="70" />
                <line x1="30" y1="85" x2="15" y2="110" />
                <line x1="30" y1="85" x2="45" y2="110" />
                <rect x="50" y="45" width="40" height="30" rx="4" />
                <circle cx="70" cy="60" r="8" />
            ';
            break;
            
        case 'pin':
            $paths = '
                <circle cx="40" cy="40" r="12" />
                <line x1="40" y1="52" x2="40" y2="85" />
                <line x1="40" y1="65" x2="60" y2="55" />
                <line x1="40" y1="65" x2="20" y2="75" />
                <line x1="40" y1="85" x2="25" y2="110" />
                <line x1="40" y1="85" x2="55" y2="110" />
                <path d="M75 30 Q90 30 90 45 Q90 60 75 80 Q60 60 60 45 Q60 30 75 30 Z" fill="var(--caribbean)" opacity="0.1" stroke="none" />
                <path d="M75 30 Q90 30 90 45 Q90 60 75 80 Q60 60 60 45 Q60 30 75 30 Z" stroke="var(--caribbean)" />
                <circle cx="75" cy="45" r="4" stroke="var(--caribbean)" />
            ';
            break;

        case 'tag':
            $paths = '
                <circle cx="40" cy="40" r="12" />
                <line x1="40" y1="52" x2="40" y2="85" />
                <line x1="40" y1="65" x2="60" y2="55" />
                <line x1="40" y1="65" x2="20" y2="75" />
                <line x1="40" y1="85" x2="25" y2="110" />
                <line x1="40" y1="85" x2="55" y2="110" />
                <path d="M65 30 L85 30 L95 50 L75 80 L55 60 Z" fill="var(--caribbean)" opacity="0.1" stroke="none" />
                <path d="M65 30 L85 30 L95 50 L75 80 L55 60 Z" stroke="var(--caribbean)" />
                <circle cx="70" cy="40" r="3" stroke="var(--caribbean)" />
            ';
            break;
            
        case 'info':
            $paths = '
                <circle cx="40" cy="40" r="12" />
                <line x1="40" y1="52" x2="40" y2="85" />
                <line x1="40" y1="65" x2="60" y2="55" />
                <line x1="40" y1="65" x2="20" y2="75" />
                <line x1="40" y1="85" x2="25" y2="110" />
                <line x1="40" y1="85" x2="55" y2="110" />
                <circle cx="80" cy="50" r="15" stroke="var(--mountain)" />
                <line x1="80" y1="42" x2="80" y2="44" stroke="var(--mountain)" />
                <line x1="80" y1="50" x2="80" y2="58" stroke="var(--mountain)" />
            ';
            break;

        case 'choco':
        case 'cafe':
        case 'grain':
            // Stickman tenant une fève/grain
            $paths = '
                <circle cx="40" cy="40" r="12" />
                <line x1="40" y1="52" x2="40" y2="85" />
                <line x1="40" y1="65" x2="60" y2="55" />
                <line x1="40" y1="65" x2="20" y2="75" />
                <line x1="40" y1="85" x2="25" y2="110" />
                <line x1="40" y1="85" x2="55" y2="110" />
                <path d="M70 45 Q85 35 90 50 Q75 60 70 45" fill="var(--warning)" opacity="0.2" stroke="none" />
                <path d="M70 45 Q85 35 90 50 Q75 60 70 45" stroke="var(--warning)" />
                <path d="M73 48 Q80 45 87 47" stroke="var(--warning)" />
            ';
            break;

        default:
            // Par défaut : Stickman qui dit bonjour
            $paths = '
                <circle cx="50" cy="40" r="12" />
                <line x1="50" y1="52" x2="50" y2="85" />
                <line x1="50" y1="65" x2="30" y2="75" />
                <line x1="50" y1="65" x2="70" y2="75" />
                <line x1="50" y1="85" x2="35" y2="110" />
                <line x1="50" y1="85" x2="65" y2="110" />
            ';
            break;
    }

    return $svg_start . $paths . $svg_end;
}
?>
