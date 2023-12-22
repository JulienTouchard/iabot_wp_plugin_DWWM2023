<?php
// fonction de gestion de securité des input et de champs vides optionels
$error = [];
function verifInput($inputName,$obligatoire = false){
    global $error;
    $input = trim(strip_tags($_POST[$inputName]));
    if($obligatoire && empty($input)){
        $error[$inputName] = "Le champ $inputName est vide!";
    }
    return $input;
}