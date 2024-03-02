<?php

require_once __DIR__ . '/../src/Repositories/PostRepository.php';
require_once __DIR__ . '/../src/Models/Post.php';
require_once __DIR__ . '/../vendor/autoload.php';

use PHPUnit\Framework\TestCase;

use src\Repositories\PostRepository;

use Dotenv\Dotenv;

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

        // Load environment variables from .env file
        $dotenv = Dotenv::createImmutable(__DIR__ . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR, '.env.ci');
        $dotenv->load();

        // Read database credentials from environment variables
        $host = $_ENV['DB_HOST'];
        $username = $_ENV['DB_USER'];
        $password = $_ENV['DB_PASS'];

		$dsn = "mysql:host=$host;";
		$options = [
			PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
			PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
			PDO::ATTR_EMULATE_PREPARES   => false,
		];
		try {
			$pdo = new PDO($dsn, $username, $password, $options);
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

        parent::tearDown();
	}

	public function testPostRetrieval()
	{
        $postRepository = new PostRepository();

        $posts = $postRepository->getAllPosts();

        foreach ($posts as $post) {
            $postId = $post->id;

            $retrievedPost = $postRepository->getPostById($postId);

            $this->assertEquals($postId, $retrievedPost->id);
        }
        parent::tearDown();
	}

	public function testPostUpdate()
	{
        // Create a test instance of PostRepository or mock it if it depends on external resources
        $postRepository = new PostRepository();

        // Create a post
        $postId = $postRepository->savePost('Initial Title', 'Initial Body')->id;

        // Update the post title and body
        $updatedTitle = 'Updated Title';
        $updatedBody = 'Updated Body';
        $postRepository->updatePost($postId, $updatedTitle, $updatedBody);

        // Retrieve the updated post
        $updatedPost = $postRepository->getPostById($postId);

        // Assert that the title and body have been updated as expected
        $this->assertEquals($updatedTitle, $updatedPost->title);
        $this->assertEquals($updatedBody, $updatedPost->body);

        parent::tearDown();
	}

	public function testPostDeletion()
	{
        // Create a instance of Post Repository
        $postRepository = new PostRepository();

        // Create a post
        $postId = $postRepository->savePost('Title', 'Body')->id;

        // Delete the post
        $postRepository->deletePostById($postId);

        // Attempt to retrieve the deleted post
        $deletedPost = $postRepository->getPostById($postId);

        // Assert that the deleted post is null, indicating it doesn't exist anymore
        $this->assertEquals(null, $deletedPost);

        parent::tearDown();
	}
}
