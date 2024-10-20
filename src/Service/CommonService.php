<?php

namespace App\Service;

use Symfony\Component\Validator\Constraints\Length;

class CommonService {
    
    public $bannedList;
    public function __construct() {
        $this->bannedList = ['badword', 'badword2'];
    }

    public function frequentlyOccuringWords($content, $numberOfWords){
        $keywords = [];
        $isBanned = false;
        $content = strtolower($content);
        $content_array = explode(' ', $content);

        // remove the banned words
        $filtred = array_diff($content_array, $this->bannedList);
        if(count($content_array) > count($filtred)){
            $isBanned = true;
            return [
                'isBanned' => $isBanned,
                'keywords' => $keywords
            ];
        }
        $wordCount = array_count_values($filtred);
        // sort the values (desc)
        arsort($wordCount);

        $keywords = array_slice(array_keys($wordCount), 0, $numberOfWords);
        return [
            'isBanned' => $isBanned,
            'keywords' => $keywords
        ];
    }
}