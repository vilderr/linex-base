<?php

namespace linex\base\helpers;

/**
 * Class StringHelper
 * @package linex\base\helpers
 */
class StringHelper extends \yii\helpers\StringHelper
{
    /**
     * @param $string
     *
     * @return string
     */
    public static function toLower($string)
    {
        return strtolower($string);
    }

    public static function translit($string, $lang = null, $params = [])
    {
        static $search = [];

        if ($lang === null) {
            $lang = Utils::getLocale();
        }

        if (!isset($search[$lang])) {
            $mess = self::langMessages($lang);
            $trans_from = explode(',', $mess['trans_from']);
            $trans_to = explode(',', $mess['trans_to']);
            foreach ($trans_from as $i => $from) {
                $search[$lang][$from] = $trans_to[$i];
            }
        }

        $defaultParams = [
            "max_len"               => 100,
            "change_case"           => 'L',
            "replace_space"         => '-',
            "replace_other"         => '-',
            "delete_repeat_replace" => true,
            "safe_chars"            => '',
        ];

        foreach ($defaultParams as $key => $value)
            if (!array_key_exists($key, $params))
                $params[$key] = $value;

        $len = strlen($string);
        $str_new = '';
        $last_chr_new = '';

        for ($i = 0; $i < $len; $i++) {
            $chr = substr($string, $i, 1);
            if (preg_match("/[a-zA-Z0-9]/", $chr) || strpos($params["safe_chars"], $chr) !== false) {
                $chr_new = $chr;
            } elseif (preg_match("/\\s/", $chr)) {
                if (
                    !$params["delete_repeat_replace"]
                    ||
                    ($i > 0 && $last_chr_new != $params["replace_space"])
                )
                    $chr_new = $params["replace_space"];
                else
                    $chr_new = '';
            } else {
                if (array_key_exists($chr, $search[$lang])) {
                    $chr_new = $search[$lang][$chr];
                } else {
                    if (
                        !$params["delete_repeat_replace"]
                        ||
                        ($i > 0 && $i != $len - 1 && $last_chr_new != $params["replace_other"])
                    )
                        $chr_new = $params["replace_other"];
                    else
                        $chr_new = '';
                }
            }

            if (strlen($chr_new)) {
                if ($params["change_case"] == "L" || $params["change_case"] == "l")
                    $chr_new = strtolower($chr_new);
                elseif ($params["change_case"] == "U" || $params["change_case"] == "u")
                    $chr_new = strtoupper($chr_new);

                $str_new .= $chr_new;
                $last_chr_new = $chr_new;
            }

            if (strlen($str_new) >= $params["max_len"])
                break;
        }

        return $str_new;
    }

    /**
     * @param $lang
     *
     * @return mixed
     */
    private static function langMessages($lang)
    {
        $messages = [
            'ru' => [
                'trans_from'   => 'а,б,в,г,д,е,ё,ж,з,и,й,к,л,м,н,о,п,р,с,т,у,ф,х,ц,ч,ш,щ,ъ,ы,ь,э,ю,я,А,Б,В,Г,Д,Е,Ё,Ж,З,И,Й,К,Л,М,Н,О,П,Р,С,Т,У,Ф,Х,Ц,Ч,Ш,Щ,Ъ,Ы,Ь,Э,Ю,Я,і,І,ї,Ї,ґ,Ґ',
                'trans_to'     => 'a,b,v,g,d,e,ye,zh,z,i,y,k,l,m,n,o,p,r,s,t,u,f,kh,ts,ch,sh,shch,,y,,e,yu,ya,A,B,V,G,D,E,YE,ZH,Z,I,Y,K,L,M,N,O,P,R,S,T,U,F,KH,TS,CH,SH,SHCH,,Y,,E,YU,YA,i,I,i,I,g,G',
                'correct_from' => 'й ц у к е н г ш щ з х ъ ф ы в а п р о л д ж э \ я ч с м и т ь б ю . Й Ц У К Е Н Г Ш Щ З Х Ъ Ф Ы В А П Р О Л Д Ж Э / Я Ч С М И Т Ь Б Ю , " № ; : ?',
                'correct_to'   => 'q w e r t y u i o p [ ] a s d f g h j k l ; \' \ z x c v b n m , . / Q W E R T Y U I O P { } A S D F G H J K L : | ~ Z X C V B N M < > ? @ # $ ^ &',
            ],
        ];

        return $messages[$lang];
    }
}