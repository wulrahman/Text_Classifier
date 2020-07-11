<?php
        
class AI_WAHEED {
    
    function train($array) {
        
        $array_phrases = array();

        foreach($array['document'] as $label => $documents) {

            foreach($documents as $key => $phrases) {

                foreach($phrases as $phrase => $score) {

                    if(array_key_exists($phrase,  $array_phrases['label'][$label])) {

                        $array_phrases['label'][$label][$phrase] += $score;

                    }
                    else {
                        $array_phrases['label'][$label][$phrase] = $score;
                    }


                    if(array_key_exists($phrase,  $array_phrases['phrase'])) {

                        $array_phrases['phrase'][$phrase] += $score;

                    }
                    else {
                        $array_phrases['phrase'][$phrase] = $score;
                    }
                }

            }

        }

        $vocabulary = count($array_phrases['phrase']);

        $total_documents = count($array['document']);

        foreach ($array_phrases['label'] as $label => $phrases) {

            $n = array_sum(array_values($phrases));

            $probability["label"][$label] = count($array['document'][$label])/ $total_documents;

            foreach($phrases as $phrase => $n_k) {
                $probability[$label][$phrase] = ($n_k+1)/($n + $vocabulary);
            }

        }
        
        return $probability;
        
    }
    
    function document_matrix($content) {
        
        $words = $this->splitSentence($content)[0];

        $frequency = array();

        foreach($words as $word) {

            $word = strtolower($word);

            if(isset($frequency[$word])) {

                $frequency[$word] += 1;

            }
            else {

                $frequency[$word] = 1;

            }

        }
        
        return $frequency;
        
    }
    
    function predict($trained_data, $data) {
        
        $words = $this->splitSentence($data)[0];
                                    
        $score[] = array();

        foreach($trained_data as $label => $data) {
            
            foreach($words as $key => $word) {

                $min_score = min($data);

                if(array_key_exists($word, $data)) {

                    $score["label"][$label][$word] = $data[$word];

                }
                else {

                    $score["label"][$label][$word] = $min_score;

                }

            }

        }
        
        $final_score_main_array[] = array();

        foreach($score["label"] as $label => $scores) {

            $final_score_main = $trained_data["label"][$label];

            $final_score[$label] = array_product($scores)*$final_score_main;

        }
        
        $final_score["label"] = array_keys($final_score, max($final_score));
        
        return $final_score;
        
        
    }
    
    function splitSentence($words) {
        
        preg_match_all('/\w+/', $words, $matches);
        return $matches;
        
    }
    
    function sentenceCase($string) { 
    
        $sentences = preg_split('/([.?!]+)/', $string, -1,PREG_SPLIT_NO_EMPTY|PREG_SPLIT_DELIM_CAPTURE); 
        $newString = ''; 

        foreach ($sentences as $key => $sentence) { 

            $newString .= ($key & 1) == 0? 
                ucfirst(strtolower(trim($sentence))) : 
                $sentence.' '; 

        } 

        return trim($newString); 

    }

    function breakLongText($text, $length = 200, $maxLength = 250) {

        $textLength = strlen($text);

        $splitText = array();

        if (!($textLength > $maxLength)){
            $splitText[] = $text;
            return $splitText;
        }

        $needle = '.';

        while (strlen($text) > $length) {

            $end = strpos($text, $needle, $length);

            if ($end === false) {
                
                $splitText[] = substr($text,0);
                $text = '';
                break;

            }

            $end++;
            $splitText[] = substr($text,0,$end);
            $text = substr_replace($text,'',0,$end);

        }

        if ($text) {
             $splitText[] = substr($text,0);
        }

        return $splitText;

    }
    
    function long_text_prediction($trained_data, $text) {

        $paragraphs = $this->breakLongText($this->sentenceCase($text));

        foreach($paragraphs as $key => $sentence) {

            $scores[]  = $this->predict($trained_data, $sentence);

        }

        $label_score = array();

        foreach($scores as $key => $label) {

            if(count($label['label']) == 1) {

                if(isset($label_score[$label['label']['0']])) {

                    $label_score[$label['label']['0']] += 1;

                }
                else {

                    $label_score[$label['label']['0']] = 1;

                }

            }

        }
        
        return $label_score;
        
    }

    
}

