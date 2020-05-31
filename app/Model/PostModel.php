<?php


namespace Blog\Model;


use Exception;
use R;

class PostModel
{
	private $id;
	private $uid;
	private $title;
	private $text;
	private UserModel $author;
	private $views;


	/**
	 * @return mixed
	 */
	public function getUid()
	{
		return $this->uid;
	}

	/**
	 * @return mixed
	 */
	public function getViews()
	{
		return $this->views;
	}

	/**
	 * @param mixed $views
	 *
	 * @return PostModel
	 */
	public function setViews($views)
	{
		$this->views = $views;
		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * @return mixed
	 */
	public function getTitle()
	{
		return $this->title;
	}

	/**
	 * @param mixed $title
	 *
	 * @return PostModel
	 */
	public function setTitle($title)
	{
		$this->title = $title;
		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getText()
	{
		return $this->text;
	}

	/**
	 * @param mixed $text
	 *
	 * @return PostModel
	 */
	public function setText($text)
	{
		$this->text = $text;
		return $this;
	}

	/**
	 * @return UserModel
	 */
	public function getAuthor(): UserModel
	{
		return $this->author;
	}

	/**
	 * @param UserModel $author
	 *
	 * @return PostModel
	 */
	public function setAuthor(UserModel $author): PostModel
	{
		$this->author = $author;
		return $this;
	}

	public function load($identifier)
	{
		$post = NULL;
		if (is_int($identifier)) {
			$post = R::load('post', $identifier);
		}
		if (is_string($identifier)) {
			$post = R::findOne('post', 'uid = ?', [$identifier]);
		}

		if ($post === NULL || $post['id'] === 0) throw new Exception('Post not found');

		$this->id = $post['id'];
		$this->uid = $post['uid'];
		$this->title = $post['title'];
		$this->text = $post['text'];
		$this->author = (new UserModel())->load($post['author_id']);


		return $this;
	}

	public function create()
	{
		R::begin();
		try {
			$uid = uuid_create();
			$newPost = R::dispense('post');
			$newPost->uid = $uid;
			$newPost->title = $this->title;
			$newPost->text = $this->text;
			$newPost->author = $this->author->getBean();
			$newPostId = R::store($newPost);
			R::commit();
			$this->uid = $uid;
			$this->id = $newPostId;
		} catch (\Exception $exception) {
			R::rollback();
		}
		return $this;
	}

	public function getPosts($page = 1, $offset = 0, $limit = 10)
	{
		if (!$page) $page = 1;
		if (!$offset || $offset < 0) $offset = 0;
		if (!$limit || $limit < 0) $limit = 10;

		if ($page > 1) {
			$offset = ($page * $limit) - $limit;
		}


		$postsList = [];
		$posts = R::findCollection('post', 'LIMIT ? OFFSET ?', [$limit, $offset]);
		while ($post = $posts->next()) {
			$author = R::load('user', $post['author_id']);

			$postsList[] = [
				'post' => [
					'title' => $post['title'],
					'text' => $post['text'],
					'id' => $post['uid'],
				],
				'author' => [
					'login' => $author['username'],
					'id' => $author['uid'],
				],
			];

		}

		return $postsList;
	}

}