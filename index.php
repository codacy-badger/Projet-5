<?php
require_once 'config/config.php';
try
{
    $controller = new ControllerFront();
    if (isset($_REQUEST['action']))
    {
        $action = $_REQUEST['action'];
        switch ($action)
        {
            case 'articlesListe' :
                $controller->afficherListeArticles();
                break;

            case 'recherche' :
                $controller->afficherResultatRecherche();
                break;

            // Affiche le formulaire
            case 'connexion' :
                $controller->afficherLoginForm();
                break;

            case 'loginUser' :
                $controller->login();
                break;

            case 'dashboard' :
                $controller->getDashboardUser();
                break;

            case 'backendDashboard':
                $controller->getBackendDashboard();
                break;

            // Ajoute un article dans les favoris de l'utilisateur
            case 'addFavorite' :
                $controller->addFavoritePost();
                break;

            // Supprime un article des favoris de l'utilisateur
            case 'deleteFavorite' :
                $controller->deleteFavoritePost();
                break;

            // Modifie les données de l'utilisateur (pseudo, email)
            case 'modifDataUser':
                $controller->modificationDataByUser();
                break;

            case 'logoutUser' :
                $controller->logout();
                break;

            // Affiche le formulaire
            case 'nouvelInscrit' :
                $controller->afficherNewLoginForm();
                break;

            case 'inscription' :
                $controller->inscription();
                break;

            case 'confirmationInscriptionByEmail' :
               $controller->confirmationByToken();
                break;

            // Affiche le formulaire
            case 'forgotPassword' :
                $controller->afficherMailForm();
                break;

            case 'forgotPasswordSendMail' :
                $controller->envoyerEmailForPassword();
                break;

            // Affiche le formulaire
            case 'confirmationEmailForPassword' :
                $controller->afficherPasswordForm();
                break;

            case 'modifierPassword' :
                $controller->changerPassword();
                break;

            default :
                break;
        }
    } else {
        $controller->afficherIndex();
    }
}

catch (PDOException $e){
    $controller->erreurPDO($e);
}

catch (ExceptionOutput $e) {
    $controller->erreurOutput($e);
}

catch (InvalidArgumentException $e)
{
   $controller->erreur();
}

catch (Exception $e) {
    echo 'Exception : ' . $e->getMessage() . '(code :' . $e->getCode() . '), ligne ' . $e->getLine() . ' dans le fichier ' . $e->getFile();
}