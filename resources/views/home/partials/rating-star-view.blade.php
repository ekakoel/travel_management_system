@php
function renderStars($score, $max = 5) {
    static $starIdCounter = 0;

    $fullStars = floor($score);
    $halfStar = ($score - $fullStars >= 0.5) ? 1 : 0;
    $emptyStars = $max - $fullStars - $halfStar;

    $starSvg = function($colorLeft = '#FFD700', $colorRight = '#FFD700') use (&$starIdCounter) {
        $starIdCounter++;
        $gradId = 'halfGrad' . $starIdCounter;

        return '
        <svg aria-hidden="true" focusable="false" role="img" xmlns="http://www.w3.org/2000/svg" 
            viewBox="0 0 20 20" width="1em" height="1em" style="vertical-align:inherit; margin: -2px; ">
            <defs>
                <linearGradient id="'.$gradId.'" x1="0" x2="1" y1="0" y2="0">
                    <stop offset="50%" stop-color="'.$colorLeft.'"/>
                    <stop offset="50%" stop-color="'.$colorRight.'"/>
                </linearGradient>
            </defs>
            <path fill="url(#'.$gradId.')" d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 
                2 9.24l5.46 4.73L5.82 21z"/>
        </svg>';
    };

    $html = '';

    // Full stars (all yellow)
    for ($i = 0; $i < $fullStars; $i++) {
        $html .= $starSvg('#ffbf00', '#ffbf00');
    }

    // Half star (left yellow, right gray)
    if ($halfStar) {
        $html .= $starSvg('#ffbf00', '#C0C0C0');
    }

    // Empty stars (all gray)
    for ($i = 0; $i < $emptyStars; $i++) {
        $html .= $starSvg('#C0C0C0', '#C0C0C0');
    }

    return $html;
}
@endphp
