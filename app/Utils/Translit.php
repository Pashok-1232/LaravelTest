<?php
namespace App\Utils;

class Translit
{
    public static function clear(string $row): string
    {
        $key = $row;
        $key = mb_strtolower($key);
        $key = urldecode($key);
        // u -> https://www.php.net/manual/ru/reference.pcre.pattern.modifiers.php (enable ru chars in alnum)
        $key = preg_replace("/[^[:alnum:]\s]+/u", '', $key);
        $key = preg_replace("/\s\s+/", ' ', $key);
        $clearName = trim($key);

        return $clearName;
    }

    /**
     * Row - row for translit. By default - rule (Any-Latin; Latin-ASCII; [^a-zA-Z0-9[:Space:]] Remove; Lower();)
     * If $rules are specified, then before the default rules are executed, transliteration is performed according to the received $rules.
     * @param string $row
     * @param bool|NULL $replaceSpaces
     * @param string|NULL $rules
     * @return string  */
    public static function make(string $row, ?bool $replaceSpaces = true, ?string $rules = null): string
    {
        if(!is_null($rules))
            $translit = transliterator_transliterate($rules, $row);
        
        $baseRules = 'Any-Latin; Latin-ASCII; [^a-zA-Z0-9[:Space:]] Remove; Lower();';
        $translit = transliterator_transliterate($baseRules, (!is_null($rules) ? $translit : $row));
        $removed = self::pregReplace('/[\`\'\"]/', '', $translit); // " = Ъ
        
        if($replaceSpaces)
            $replaced = self::strReplace(' ', '-', $removed);
        else
            $replaced = $removed;
        
        return $replaced;
    }

    public static function makeOld(string $row, ?bool $replaceSpaces = true, ?string $rules = null): string
    {
        $rules = $rules ?? 'Any-Latin; Latin-ASCII;';//[\'\`] Remove; - Remove before translit
        $translit = transliterator_transliterate($rules, $row);
        $removed = self::pregReplace('/[\`\'\"]/', '', $translit); // " = Ъ
        
        if($replaceSpaces)
            $replaced = self::strReplace(' ', '-', $removed);
        else
            $replaced = $removed;
        
        return $replaced;
    }

    private static function pregReplace(string $pattern, string $replacement, string $subject): string
    {
        return preg_replace($pattern, $replacement, $subject);
    }
    
    private static function strReplace(string $search, string $replace, string $subject): string
    {
        return str_replace($search, $replace, $subject);
    }
}
?>