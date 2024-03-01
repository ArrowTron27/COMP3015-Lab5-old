<?php

namespace src\Repositories;

require_once __DIR__ . '/Repository.php';
require_once __DIR__ . '/../Models/Post.php';

use PDOException;
use src\Models\Post as Post;

class PostRepository extends Repository
{

	/**
	 * @return Post[] gets all posts in the repository
	 */
	public function getAllPosts(): array
	{
		$sqlStatement = $this->pdo->prepare("SELECT * FROM posts;");
		$sqlStatement->execute();
		$resultSet = $sqlStatement->fetchAll();

		$posts = [];
		foreach ($resultSet as $record) {
			$posts[] = new Post($record);
		}

		return $posts;
	}

	/**
	 * @param int $id
	 * @return Post|false Post object if it was found, false otherwise
	 */
	public function getPostById(int $id): Post|false
	{
		$sqlStatement = $this->pdo->prepare('SELECT id, title, body, created_at, updated_at FROM posts WHERE id = ?');
		$sqlStatement->execute([$id]);
		$resultSet = $sqlStatement->fetch();
		if ($resultSet) {
			return (new Post($resultSet));
		}
		return false;
	}

	/**
	 * @param string $title
	 * @param string $body
	 * @return Post|false the newly created Post object, or false in the case of a failure to save or retrieve the new record
	 */
	public function savePost(string $title, string $body): Post|false
	{
		$sqlStatement = $this->pdo->prepare("INSERT INTO posts (id, title, body, created_at, updated_at) VALUES(NULL, ?, ?, ?, NULL);"); // values are: id, title, body, created_at, updated_at
		$createdAt = date('Y-m-d H:i:s');
		try {
			$success = $sqlStatement->execute([$title, $body, $createdAt]);
			if ($success) {
				$postId = $this->pdo->lastInsertId();
				return $this->getPostById($postId);
			}
			return false;
		} catch (PDOException) {
			return false;
		}
	}

	/**
	 * @param int $id
	 * @param string $title
	 * @param string $body
	 * @return bool
	 */
	public function updatePost(int $id, string $title, string $body): bool
	{
		$sqlStatement = $this->pdo->prepare("UPDATE posts SET title = ?, body = ?, updated_at = ? WHERE id = ?");
		$updatedAt = date('Y-m-d H:i:s');
		return $sqlStatement->execute([$title, $body, $updatedAt, $id]);
	}

	/**
	 * @param int $id
	 * @return bool
	 */
	public function deletePostById(int $id): bool
	{
		$sqlStatement = $this->pdo->prepare("DELETE FROM posts WHERE id = ?;");
		return $sqlStatement->execute([$id]);
	}
}
