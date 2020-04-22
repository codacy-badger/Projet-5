<?php

class PostManager extends Connexion
{
    private $offset = 3;

    //Recherche des articles avec le statut actif
    public function getPosts($page, $state, $post = NULL)
    {
        $bdd = $this->dbConnect();
        $listPosts = $bdd->prepare('SELECT posts.id, posts.title, posts.kicker, users.pseudo, posts.content, posts.url, posts.created_at, posts.modified_at
                                                    FROM `posts` INNER JOIN users ON posts.author = users.id 
                                                    WHERE posts.state = :state ORDER BY posts.created_at DESC LIMIT :page,:offset');
        if (!empty($page)) {
            $listPosts->bindValue(':page', intval(($page -1) * 3), PDO::PARAM_INT);
        } else {
            $listPosts->bindValue(':page', intval(0), PDO::PARAM_INT);
        }
        $listPosts->bindValue(':state', $state);
        $listPosts->bindValue(':offset', intval($this->offset), PDO::PARAM_INT);
        $listPosts->execute();
        return $listPosts->fetchAll(PDO::FETCH_CLASS, 'Post');
    }

    //Affichage de tous les articles selon le statut
    public function getAllPosts($page, $state, $offset, $post = NULL)
    {
        $bdd = $this->dbConnect();
        $listPosts = $bdd->prepare('SELECT posts.id, posts.title, posts.kicker, users.pseudo, posts.content, posts.url, posts.created_at, posts.modified_at, posts.state
                                                    FROM `posts` INNER JOIN users ON posts.author = users.id
                                                    WHERE posts.state IN (:state) ORDER BY posts.created_at DESC LIMIT :page, :offset');
        if (!empty($page)) {
            $listPosts->bindValue(':page', intval(($page -1) * $offset), PDO::PARAM_INT);
        } else {
            $listPosts->bindValue(':page', intval(0), PDO::PARAM_INT);
        }
        $listPosts->bindValue(':state', $state);
        $listPosts->bindValue(':offset', intval($offset), PDO::PARAM_INT);
        $listPosts->execute();
        return $listPosts->fetchAll(PDO::FETCH_CLASS, 'Post');
    }

    //Affichage de tous les articles à supprimer
    public function getAllTrashPosts($page, $post = NULL)
    {
        $offset = 10;
        $bdd = $this->dbConnect();
        $listPosts = $bdd->prepare('SELECT posts.id, posts.title, posts.kicker, users.pseudo, posts.content, posts.url, posts.created_at, posts.modified_at, posts.state
                                                    FROM `posts` INNER JOIN users ON posts.author = users.id
                                                    WHERE posts.state = 3 ORDER BY posts.created_at DESC LIMIT :page,:offset');
        if (!empty($page)) {
            $listPosts->bindValue(':page', intval(($page -1) * 10), PDO::PARAM_INT);
        } else {
            $listPosts->bindValue(':page', intval(0), PDO::PARAM_INT);
        }
        $listPosts->bindValue(':offset', intval($offset), PDO::PARAM_INT);
        $listPosts->execute();
        return $listPosts->fetchAll(PDO::FETCH_CLASS, 'Post');
    }

    //Compte le nombre d'articles total
    public function countAllPosts()
    {
        $bdd = $this->dbConnect();
        $statement = $bdd->prepare('SELECT COUNT(id) as nbPosts FROM `posts` WHERE state = 1');
        $statement->execute();
        $resultat = $statement->fetch();
        return $resultat['nbPosts'];
    }

    //Compte le nombre d'articles en attente de validation
    public function countPostsUnckecked()
    {
        $bdd = $this->dbConnect();
        $statement = $bdd->prepare('SELECT COUNT(id) as nbPosts FROM `posts` WHERE state = 0');
        $statement->execute();
        $resultat = $statement->fetch();
        return $resultat['nbPosts'];
    }
    //Compte le nombre d'articles à supprimer
    public function countPostsToDelete()
    {
        $bdd = $this->dbConnect();
        $statement = $bdd->prepare('SELECT COUNT(id) as nbPosts FROM `posts` WHERE state = 3');
        $statement->execute();
        $resultat = $statement->fetch();
        return $resultat['nbPosts'];
    }

    //Compte le nombre d'articles à archiver
    public function countPostsArchived()
    {
        $bdd = $this->dbConnect();
        $statement = $bdd->prepare('SELECT COUNT(id) as nbPosts FROM `posts` WHERE state = 2');
        $statement->execute();
        $resultat = $statement->fetch();
        return $resultat['nbPosts'];
    }

    //Résultat d'une recherche sur les articles avec le statut actif
    public function getSearch($recherche, $page, $post = NULL)
    {
        $bdd = $this->dbConnect();
        if (isset($recherche)) {
            $listPosts = $bdd->prepare("SELECT posts.id, posts.title, posts.kicker, users.pseudo, posts.content, posts.url, posts.created_at, posts.modified_at 
                                                        FROM `posts` INNER JOIN users ON posts.author = users.id 
                                                        WHERE posts.state = :state AND (posts.title LIKE CONCAT('%', :recherche, '%') OR posts.kicker LIKE CONCAT('%', :recherche, '%') OR posts.content LIKE CONCAT('%', :recherche, '%'))
                                                        ORDER BY posts.created_at DESC LIMIT :page,:offset");
            if (isset($page)) {
                $listPosts->bindValue(':page', intval(($page -1) * 3), PDO::PARAM_INT);
            }
            $listPosts->bindValue(':state', intval(1), PDO::PARAM_INT);
            $listPosts->bindValue(':offset', intval($this->offset), PDO::PARAM_INT);
            $listPosts->bindValue(':recherche',  htmlspecialchars($recherche), PDO::PARAM_STR);
            $listPosts->execute();
            return $listPosts->fetchAll(PDO::FETCH_CLASS, 'Post');
        } else {
            $this->getPosts(1, 1);
        }
    }

    //Liste des articles favoris retrouvé par l'id de l'utilisateur
    public function getFavoritePostByIdUser(User $user)
    {
        $bdd = $this->dbConnect();
        $statement = $bdd->prepare('SELECT id_post, posts.id, posts.title, posts.kicker, users.pseudo, posts.content, posts.url, posts.created_at, posts.modified_at 
                                                FROM `favorites_posts` LEFT JOIN posts ON favorites_posts.id_post = posts.id 
                                                    INNER JOIN users ON posts.author = users.id 
                                                WHERE id_user = :id ORDER BY posts.modified_at DESC');
        $statement->execute(array(
            'id' => $user->getId()
        ));
        return $statement->fetchAll(PDO::FETCH_CLASS, 'Post');
    }

    //Ajout d'un article en favoris par l'utilisateur connecté
    public function addFavoritePostByIdUser(User $user, Favorites_posts $favorites)
    {
        $bdd = $this->dbConnect();
        $statement = $bdd->prepare('INSERT INTO `favorites_posts`(id_user, id_post) VALUES (:id_user, :id_post)');
        $statement->execute(array(
            'id_user' => $user->getId(),
            'id_post' => $favorites->getId_post()
        ));
    }

    //Compte le nombre de favoris pour un utilisateur
    public function countFavoritesPostUser(User $user)
    {
        $bdd = $this->dbConnect();
        $statement = $bdd->prepare('SELECT COUNT(id_user) as nb_favorites FROM `favorites_posts` WHERE id_user=:id_user');
        $statement->execute(array(
            'id_user' => $user->getId()
        ));
        $resultat = $statement->fetch();
        return $resultat['nb_favorites'];
    }

    //Recherche si l'article est dans les favoris
    public function getFavorite($id_user, $id_post)
    {
        $bdd = $this->dbConnect();
        $statement = $bdd->prepare('SELECT COUNT(id) as nb_favorites FROM `favorites_posts` WHERE id_user = :id_user AND id_post = :id_post');
        $statement->execute(array(
            'id_user' => $id_user,
            'id_post' => $id_post
        ));
        $resultat = $statement->fetch();
        return $resultat['nb_favorites'];
    }

    //Recherche si le favoris existe déjà
    public function searchFavorite(User $user, Favorites_posts $favorites)
    {
        $bdd = $this->dbConnect();
        $statement = $bdd->prepare('SELECT EXISTS(SELECT 1 FROM `favorites_posts` WHERE id_user = :id_user AND id_post = :id_post LIMIT 1) as result');
        $statement->execute(array(
            'id_user' => $user->getId(),
            'id_post' => $favorites->getId_post()
        ));
        $resultat = $statement->fetch();
        return $resultat['result'];
    }

    public function fillFavoriteInPost(User $user, Post $post)
    {
        $favorite = $this->getFavorite($user->getId(), $post->getId());
        $post->setStatut_favorite($favorite);
    }

    //Supprime un article des favoris par l'utilisateur connecté
    public function deleteFavoritePostByIdUser(User $user, Favorites_posts $favorites)
    {
        $bdd = $this->dbConnect();
        $statement = $bdd->prepare('DELETE FROM `favorites_posts` WHERE id_user = :id_user AND id_post = :id_post');
        $statement->execute(array(
            'id_user' => $user->getId(),
            'id_post' => $favorites->getId_post()
        ));
    }

    //Compte le nombre de page d'articles
    public function countPages($nbPosts, $state)
    {
        $bdd = $this->dbConnect();
        $countPages = $bdd->prepare('SELECT COUNT(*)/:nbPosts AS nb_pages FROM `posts` WHERE posts.state IN (:state)');
        $countPages->execute(array(
            'nbPosts' => $nbPosts,
            'state' => $state
        ));
        $resultat = $countPages->fetch();
        return $resultat['nb_pages'];
    }

    //Compte le nombre de page d'articles résultant d'une recherche
    public function countPagesSearchResult($recherche)
    {
        $bdd = $this->dbConnect();
        if (!empty($recherche))
        {
            $countPages = $bdd->prepare("SELECT COUNT(*)/3 AS nb_pages FROM `posts` 
                                                    WHERE posts.state = 1 AND posts.title LIKE CONCAT('%', :recherche, '%') OR posts.kicker LIKE CONCAT('%', :recherche, '%') OR posts.content LIKE CONCAT('%', :recherche, '%')");
            $countPages->bindValue(':recherche',  htmlspecialchars($recherche), PDO::PARAM_STR);
            $countPages->execute();
            $resultat = $countPages->fetch();
            return $resultat['nb_pages'];
        } else {
            $this->countPages();
        }
    }
}