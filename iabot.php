<?php

/**
 * @package IABot
 */
/*
Plugin Name: IABot Automatis Response Support
Plugin URI: https://kawasakiTeam.com/IABot.php
Description: Solution de réponse automatque pour le suppoer en ligne des utilisateur WooCommerce.
Version: 1.0.0
Author: KawasakiTeam
Author URI: https://kawasakiTeam.com
License: GPLv2 or later
Text Domain: iabot
*/

/*
 creer 1 table keywords
 creer 1 table answers
 creer 1 table keywords_answers

*/
// je définie une constante qui sera l'url du dossier de notre plugin
define('_IABOT__PLUGIN_DIR', plugin_dir_path(__FILE__));
// j'appelle un fichier functions.php qui contient toutes les fonctions 
// utiles à mon plugin : verif des champs input, validité email, etc...
require_once(_IABOT__PLUGIN_DIR . 'functions.php');

// fonction d'activation du plugin
function iabot_plugin_activation()
{
    global $wpdb;
    // création de mes 3 tables
    $wpdb->query("CREATE TABLE IF NOT EXISTS {$wpdb->prefix}keywords (id INT AUTO_INCREMENT PRIMARY KEY, word VARCHAR(30) NOT NULL)");
    $wpdb->query("CREATE TABLE IF NOT EXISTS {$wpdb->prefix}answers (id INT AUTO_INCREMENT PRIMARY KEY, answer TEXT NOT NULL)");
    $wpdb->query("CREATE TABLE IF NOT EXISTS {$wpdb->prefix}keywords_answers (id INT AUTO_INCREMENT PRIMARY KEY, id_keywords INT NOT NULL, id_answers INT NOT NULL)");
}
// fonction de desactivation de mon plugin 
function iabot_plugin_desactivation()
{
    global $wpdb;
    // destruction de mes 3 tables
    $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}keywords");
    $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}answers");
    $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}keywords_answers");
}
// fonction d'ajout du plugin au menu admin
function iabot_plugin_setup_menu()
{
    $iabot_icon_base64 = "PHN2ZyBhcmlhLWhpZGRlbj0idHJ1ZSIgZm9jdXNhYmxlPSJmYWxzZSIgZGF0YS1wcmVmaXg9ImZhcyIgZGF0YS1pY29uPSJyb2JvdCIgY2xhc3M9InN2Zy1pbmxpbmUtLWZhIGZhLXJvYm90IGZhLXctMjAiIHJvbGU9ImltZyIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB2aWV3Qm94PSIwIDAgNjQwIDUxMiI+PHBhdGggZmlsbD0iY3VycmVudENvbG9yIiBkPSJNMzIsMjI0SDY0VjQxNkgzMkEzMS45NjE2NiwzMS45NjE2NiwwLDAsMSwwLDM4NFYyNTZBMzEuOTYxNjYsMzEuOTYxNjYsMCwwLDEsMzIsMjI0Wm01MTItNDhWNDQ4YTY0LjA2MzI4LDY0LjA2MzI4LDAsMCwxLTY0LDY0SDE2MGE2NC4wNjMyOCw2NC4wNjMyOCwwLDAsMS02NC02NFYxNzZhNzkuOTc0LDc5Ljk3NCwwLDAsMSw4MC04MEgyODhWMzJhMzIsMzIsMCwwLDEsNjQsMFY5Nkg0NjRBNzkuOTc0LDc5Ljk3NCwwLDAsMSw1NDQsMTc2Wk0yNjQsMjU2YTQwLDQwLDAsMSwwLTQwLDQwQTM5Ljk5NywzOS45OTcsMCwwLDAsMjY0LDI1NlptLTgsMTI4SDE5MnYzMmg2NFptOTYsMEgyODh2MzJoNjRaTTQ1NiwyNTZhNDAsNDAsMCwxLDAtNDAsNDBBMzkuOTk3LDM5Ljk5NywwLDAsMCw0NTYsMjU2Wm0tOCwxMjhIMzg0djMyaDY0Wk02NDAsMjU2VjM4NGEzMS45NjE2NiwzMS45NjE2NiwwLDAsMS0zMiwzMkg1NzZWMjI0aDMyQTMxLjk2MTY2LDMxLjk2MTY2LDAsMCwxLDY0MCwyNTZaIj48L3BhdGg+PC9zdmc+";
    $iabot_icon_data_uri = 'data:image/svg+xml;base64,' . $iabot_icon_base64;
    add_menu_page('admin_menu', 'IABot', 'manage_options', 'iabot', 'iabot_init', $iabot_icon_data_uri, 3);
}
add_action('admin_menu', 'iabot_plugin_setup_menu');
// fonction d'initialisation de ma page de plugin coté admin
function iabot_init()
{
    global $wpdb;
    if (!empty($_POST['word']) && isset($_POST['word'])) {

        // je traite les données reçu en POST
        $_POST['word'] = verifInput('word', true);
        $_POST['answer'] = verifInput('answer', true);
        // Entrer les information dans les tables
        $wpdb->insert($wpdb->prefix . "keywords", ['word' => $_POST['word']], ['%s']);
        $id_keywords = $wpdb->insert_id;
        $wpdb->insert($wpdb->prefix . "answers", ['answer' => $_POST['answer']]);
        $id_answers = $wpdb->insert_id;
        $wpdb->insert($wpdb->prefix . "keywords_answers", ['id_keywords' => $id_keywords, 'id_answers' => $id_answers]);
    }
    include(_IABOT__PLUGIN_DIR . "/views/admin_form.php");
    $results = $wpdb->get_results("
    SELECT k.word,a.answer
    FROM  {$wpdb->prefix}keywords as k
    INNER JOIN {$wpdb->prefix}keywords_answers as ka
    ON k.id = ka.id_keywords
    INNER JOIN {$wpdb->prefix}answers as a
    ON ka.id_answers = a.id
    ORDER by k.word
    ", OBJECT);
    include(_IABOT__PLUGIN_DIR . "/views/keywords_answers.php");
}




// partie front
function iabot()
{
    global $wpdb;
    if (!empty($_POST['userSearch']) && isset($_POST['userSearch'])) {
        // récupération de la recherche de mon utilisateur
        $_POST['userSearch'] = str_replace(["/", "\\", "'", "\""], " ", $_POST['userSearch']);
        $boolIA = false;
        // j'extrais les mots clés
        $tabKeyword = explode(" ", $_POST['userSearch']);
        foreach ($tabKeyword as $value) {
            // pour chaque mot clé je fais une recherche dans ma table
            $results = $wpdb->get_results(
                "SELECT a.answer
            FROM  {$wpdb->prefix}keywords as k
            INNER JOIN {$wpdb->prefix}keywords_answers as ka
            ON k.id = ka.id_keywords
            INNER JOIN {$wpdb->prefix}answers as a
            ON ka.id_answers = a.id
            WHERE k.word = '$value'",
                OBJECT
            );
                if (count($results) > 0 && !$boolIA) {
                    // en cas de succes je stoque le resultat dans $response
                    $response = $results[0]->answer;
                    $boolIA = true;
                }
                
        }
        if (!$boolIA){
            // en cas d'échec je renvoie un message type
            $response = "Nous n'avons pas trouvé de solution à votre problème.";
        }
    }
    include(_IABOT__PLUGIN_DIR . "/views/front_form.php");
    
}
add_action('wp_head', 'iabot');



register_activation_hook(__FILE__, 'iabot_plugin_activation');
register_deactivation_hook(__FILE__, 'iabot_plugin_desactivation');
