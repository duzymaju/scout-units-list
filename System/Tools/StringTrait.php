<?php

namespace ScoutUnitsList\System\Tools;

/**
 * System tools string trait
 */
trait StringTrait
{
    /**
     * Convert to slug
     *
     * @param string $text    text
     * @param string $divider divider
     *
     * @return string
     */
    public function convertToSlug($text, $divider = '-')
    {
        $changeFrom = [
            'Ą', 'ą', 'Ć', 'ć', 'Ę', 'ę', 'Ł', 'ł', 'Ń', 'ń', 'Ó', 'ó', 'Ś', 'ś', 'Ź', 'ź', 'Ż', 'ż', ' '
        ];
        $changeTo = [
            'a', 'a', 'c', 'c', 'e', 'e', 'l', 'l', 'n', 'n', 'o', 'o', 's', 's', 'z', 'z', 'z', 'z', $divider
        ];
        $simpleText = strtolower(str_replace($changeFrom, $changeTo, trim(strip_tags($text))));
        $slug = preg_replace('#[^0-9a-z' . $divider . ']#', '', $simpleText);

        while ($slug != $noDoubledHyphens = str_replace($divider . $divider, $divider, $slug)) {
            $slug = $noDoubledHyphens;
        }

        return $slug;
    }
}
