<?php

namespace App\Controllers;

class EntriesController
{
    private $db;

    /**
     * Dependeny Injection (DI): http://www.phptherightway.com/#dependency_injection
     * If this class relies on a database-connection via PDO we inject that connection
     * into the class at start. If we do this EntriesController will be able to easily
     * reference the PDO with '$this->db' in ALL functions INSIDE the class
     * This class is later being injected into our container inside of 'App/container.php'
     * This results in we being able to call '$this->get('Todos')' to call this class
     * inside of our routes.
     */
    public function __construct(\PDO $pdo)
    {
        $this->db = $pdo;
    }

    public function getAll(int $limit)
    {
        $getAll = $this->db->prepare('SELECT * FROM entries LIMIT :limit');
        $getAll->bindParam(':limit', $limit, \PDO::PARAM_INT);
        $getAll->execute();
        return $getAll->fetchAll();
    }

  
    public function getOne($entryID){
        $getOne = $this->db->prepare(
          " SELECT entryID, title, content, createdAt
            FROM entries WHERE entryID = :entryID"
        );
        $getOne->execute([':entryID' => $entryID]);
        return $getOne->fetch();
      }




    public function add($entry)
    {
        /**
         * Default 'completed' is false so we only need to insert the 'content'
         */
        $addOne = $this->db->prepare(
            'INSERT INTO entries (title, content, createdAt) VALUES (:title, :content, NOW())'
        );

        /**
         * Insert the value from the parameter into the database
         */
        $addOne->execute([
            ':content'  => $entry['content'],
            ':title'  => $entry['title'],
        ]);

        /**
         * A INSERT INTO does not return the created object. If we want to return it to the user
         * that has posted the todo we must build it ourself or fetch it after we have inserted it
         * We can always get the last inserted row in a database by calling 'lastInsertId()'-function
         */
        return [
          'id'          => (int)$this->db->lastInsertId(),
          'title'       => $entry['title'],
          'content'     => $entry['content'],
        ];
    }

    public function remove($entryID){
        $removeOne = $this->db->prepare(
          "DELETE FROM entries WHERE entryID = :entryID;"
        );
        $removeOne->execute([
          ":entryID" => $entryID
        ]);
      }

    public function update($entryID, $entry)
    {
        /**
         * Default 'completed' is false so we only need to insert the 'content'
         */
        $update = $this->db->prepare(
            "UPDATE entries SET content = :content, title = :title WHERE entryID = :entryID;"
        );

        /**
         * Insert the value from the parameter into the database
         */
        $update->execute([
            ':content'  => $entry['content'],
            ':title'  => $entry['title'],
            ':entryID'  => $entryID,
        ]);

        /**
         * A INSERT INTO does not return the created object. If we want to return it to the user
         * that has posted the todo we must build it ourself or fetch it after we have inserted it
         * We can always get the last inserted row in a database by calling 'lastInsertId()'-function
         */
        return [
          'id'          => (int)$this->db->lastInsertId(),
          'title'       => $entry['title'],
          'content'     => $entry['content'],
        ];
    }
}


