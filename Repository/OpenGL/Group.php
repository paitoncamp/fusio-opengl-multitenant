<?php

namespace App\Repository\OpenGL;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;

/**
 * Repository which handles all database operations regarding a group
 */
class Group
{
    /**
     * @var Connection
     */
    private $connection;

    //public function __construct(Connection $connection)
	public function __construct()
    {
        //$this->connection = $connection;
    }
	
	public function setupConnection(Connection $connection)
	{
		$this->connection = $connection;
	}
    
    public function findById(int $id)
    {
        return $this->connection->fetchAssoc('SELECT id, parent_id, `name`,code, affects_gross FROM groups WHERE id = :id', [
            'id' => $id,
        ]);
    }

    public function insert(int $id, int $parentId, string $name, string $code, int $affectsGross): int
    {
        $this->connection->insert('groups', [
            'id' => $id,
            'parent_id' => $parentId,
            'name' => $name,
			'code' => $code,
			'affects_gross' => $affectsGross
            //'insert_date' => (new \DateTime())->format('Y-m-d H:i:s'),
        ]);

        return (int) $this->connection->lastInsertId();
    }

    public function update(int $id, int $parentId, string $name, string $code, int $affectsGross): int
    {
        $this->connection->update('groups', [
            'id' => $id,
			'parent_id' => $parentId,
            'name' => $name,
			'code' => $code,
			'affects_gross' => $affectsGross
        ], [
            'id' => $id
        ]);

        return $id;
    }
    
    public function delete(int $id): int
    {
        $this->connection->delete('groups', [
            'id' => $id
        ]);

        return $id;
    }
}
