 
<?php

error_reporting(0);

require_once('Text_Classifier.php');

ini_set('max_execution_time', 0); 

ini_set("memory_limit","-1");

$new_ai_sentiment = new AI_WAHEED;

$setting['sentiment_data_set'] = array(
    array("url" => "Trainingset/negative-articles.txt", "type" => 2, "trained" => 0, "sentiment"=>"negative"),
    array("url" => "Trainingset/positive-articles.txt", "type" => 2, "trained" => 0, "sentiment"=>"positive")
);

foreach($setting['sentiment_data_set'] as $key => $sentiment_file_data) {

    if($sentiment_file_data["type"] == 1 && $sentiment_file_data["trained"] == 0 ) {

        $files = glob($sentiment_file_data["url"]);

        foreach($files as $file) {

            $content = file_get_contents($file);

            $array_sentiment['document'][$sentiment_file_data["sentiment"]][] = $new_ai_sentiment->document_matrix($content);

        }


    }
    if($sentiment_file_data["type"] == 2 && $sentiment_file_data["trained"] == 0) {

        $content = file_get_contents($sentiment_file_data["url"]);

        $array_sentiment['document'][$sentiment_file_data["sentiment"]][] = $new_ai_sentiment->document_matrix($content);

    }

}

if(!empty($array_sentiment)) {
    
    $probability_sentiment = $new_ai_sentiment->train($array_sentiment);
    
}

$text = "Five people have been killed after attackers stormed a South African church, reportedly amid an argument over its leadership.";

$label_score_sentiment = $new_ai_sentiment->long_text_prediction($probability_sentiment, $text);

print_r($label_score_sentiment );

$array['sentiment']  = array_keys($label_score_sentiment, max($label_score_sentiment))['0'];

?>