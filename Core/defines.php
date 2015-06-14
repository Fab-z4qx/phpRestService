<?php

// Si on a pas ces infos, rien ne peut fonctionner : die
if (!isset($_SERVER['DOCUMENT_ROOT']))
    die();

// Define de la racine du site
define('_PATH_', dirname(dirname(__FILE__).'/').'/');

// Define du dossier Coeur
define('_CORE_', _PATH_ . '../app/core/');

// Define du dossier Coeur
define('_CORE_API_', _PATH_ . '/core/');

// Define du dossier Model
define('_MODEL_', _PATH_ . '../app/models/');

define('_MODEL_API_', _PATH_ . '/models/');

// Define du dossier des Controleurs
define('_CTRL_', _PATH_ . '../app/controllers/');

define('_FILES_', _PATH_ . '../app/files/');

// Define du dossier des Controleurs Public
define('_CTRL_PUBLIC_', _PATH_ . '../app/controllers/PanelPublic/');

// Define du dossier Controleurs entreprise
define('_CTRL_ENT_', _PATH_ . '../app/controllers/PanelEntreprise/');

// Define du dossier des Configs
define('_CONFIG_', _PATH_ . '../app/config/');

// Define du dossier des TPL
define('_TPL_', _PATH_ . '../app/tpl/');

// Define du dossier des TPL Entreprise
define('_TPL_ENT_', _PATH_ . '../app/tpl/PanelEntreprise/');

// Define du dossier des TPL Public
define('_TPL_PUBLIC_', _PATH_ . '../app/tpl/PanelPublic/');

// Define du dossier des TPL commun
define('_TPL_COMMON_', _PATH_ . '../app/tpl/Common/');

// Define du dossier des logs
define('_LOGS_', _PATH_ . '../app/logs/');

?>