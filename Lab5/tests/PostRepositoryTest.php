<?php

require_once __DIR__ . '/../src/Repositories/PostRepository.php';
require_once __DIR__ . '/../src/Models/Post.php';
require_once __DIR__ . '/../vendor/autoload.php';

use PHPUnit\Framework\TestCase;

use src\Repositories\PostRepository;

class PostRepositoryTest extends TestCase
{
	private PostRepository $postRepository;

	public function __construct(?string $name = null, array $data = [], $dataName = '')
	{
		parent::__construct($name, $data, $dataName);
	}

	/**
	 * Runs before each test
	 */
	protected function setUp(): void
	{
		parent::setUp();
		$this->postRepository = new PostRepository();
	}

	/**
	 * Runs after each test
	 */
	protected function tearDown(): void
	{
		parent::tearDown();

		// TODO: Read the username and host from the .env file

		$dsn = "mysql:host=localhost;";
		$options = [
			PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
			PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
			PDO::ATTR_EMULATE_PREPARES   => false,
		];
		try {
			$pdo = new PDO($dsn, 'root', 'KimChaewon', $options);
		} catch (PDOException $e) {
			throw new PDOException($e->getMessage(), (int)$e->getCode());
		}
		$commands = file_get_contents(__DIR__ . '/../database/test_schema.sql', TRUE);
		$pdo->exec($commands);
	}

	public function testPostCreation()
	{
		$post = (new PostRepository)->savePost('test', 'body');
        //$this->assertEquals(`1`, $post->id);
		$this->assertEquals('test', $post->title);
		$this->assertEquals('body', $post->body);
	}

	public function testPostRetrieval()
	{
		// TODO test the "get" methods in the PostRepository class
        // assertcount
	}

	public function testPostUpdate()
	{
		// TODO create a post, update the title and body, and check that you get the expected title and body
	}

	public function testPostDeletion()
	{
		// TODO: delete a post by ID and check that it isn't in the database anymore
	}
}
