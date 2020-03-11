<?php

class UserManager extends Connexion
{
    /** INSCRIPTION ET CONFIRMATIONS */

    /** étape 1 : création user + token associé */
    public function registration(User $user)
    {
        $bdd = $this->dbConnect();
        $bdd->beginTransaction();

        $statement = $bdd->prepare('INSERT INTO `users` (`pseudo`, `role`, `email`, `password`, `date_inscription`, `date_modification`, `cgu`, `state`) VALUES (:pseudo, :role, :email, :password, NOW(), NOW(), :cgu, :state) ');
        $statement->execute(array(
            'pseudo' => htmlspecialchars($user->getPseudo()),
            'role' => $user->getRole(),
            'email' => $user->getEmail(),
            'password' => $user->getPassword(),
            'cgu' => $user->getCgu(),
            'state' => $user->getState()
        ));

        $id_user = $bdd->lastInsertId();
        $token = $user->generateToken();
        $interval = 5 * 24 * 60;

        $tokenStatement = $bdd->prepare('INSERT INTO `tokens` (`token`, `id_user`, `expiration_token`) VALUES (:token, :id_user, DATE_ADD(now(), INTERVAL :interval MINUTE))');
        $tokenStatement->execute(array(
            'token' => $token,
            'interval' => $interval,
            'id_user' => $id_user
        ));
        $bdd->commit();
    }

    //récupération du token avec l'email
    public function tokenRecuperation(User $user)
    {
        $bdd = $this->dbConnect();
        $statement = $bdd->prepare('SELECT `token`, `email` FROM `tokens` JOIN `users` ON `id_user` = `id` WHERE `email` LIKE :email');
        $statement->execute(array(
            'email' => $user->getEmail()
        ));

        return $statement->fetchAll(PDO::FETCH_CLASS, 'User');
    }

    //étape 2 : confirmation de l'inscription via lien email & suppression token en bdd
    public function registrationConfirmationByToken(User $user)
    {
        $bdd = $this->dbConnect();
            $statement = $bdd->prepare('UPDATE `users` SET `state` = 1 WHERE `id` = (SELECT `id_user` FROM `tokens` WHERE `token` = :token AND `expiration_token` > NOW())');
            $statement->execute(array('token' =>$user->getToken()));
            $statement = $bdd->prepare('DELETE FROM tokens WHERE token = :token');
            $statement->execute(array('token' =>$user->getToken()));
    }

    //étape 3 : confirmation de l'inscription par l'administrateur
    public function registrationConfirmationByAdmin(User $user)
    {
        $bdd = $this->dbConnect();
            $statement = $bdd->prepare('UPDATE `users` SET `state` = :state');
            $statement->execute(array(
                'state' => $user->getState()
            ));

    }

    /** SUPPRESSIONS UTILISATEURS ET TOKENS */

    //purge des utilisateurs n'ayant pas validé leur inscription
    public function deleteExpiredTokenAndUser()
    {
        $bdd = $this->dbConnect();
        $statement = $bdd->prepare('DELETE FROM `users` WHERE `id` = (SELECT `id_user` FROM `tokens` WHERE `expiration_token` < NOW()) AND state = 0');
        $statement->execute();
    }

    //purge des utilisateurs ne s'étant pas connectés depuis 3 mois
    public function deleteExpirateduser()
    {
        $bdd = $this->dbConnect();
        $statement = $bdd->prepare('DELETE FROM users WHERE DATEDIFF(date_modification, date_inscription) > 90 AND role != 1');
        $statement->execute();
    }
}