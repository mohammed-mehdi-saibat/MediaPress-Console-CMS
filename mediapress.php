<?php

class User
{
    private static int $nextId = 1;
    private int $id;
    private string $username;
    private string $email;
    private string $password;
    private string $role;
    private DateTime $createdAt;

    public function __construct(string $username, string $email, string $password, string $role = 'user')
    {
        $this->id = self::$nextId++;
        $this->username = $username;
        $this->email = $email;
        $this->setPassword($password);
        $this->role = $role;
        $this->createdAt = new DateTime();
    }

    public function setPassword(string $password): void
    {
        $this->password = password_hash($password, PASSWORD_DEFAULT);
    }

    public function verifyPassword(string $password): bool
    {
        return password_verify($password, $this->password);
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getRole(): string
    {
        return $this->role;
    }

    public function setRole(string $role): void
    {
        $this->role = $role;
    }
}

class Author extends User
{
    private string $bio;

    public function __construct(string $username, string $email, string $password, string $bio)
    {
        parent::__construct($username, $email, $password, 'author');
        $this->bio = $bio;
    }

    public function getBio(): string
    {
        return $this->bio;
    }
}

class Editor extends User
{
    public function __construct(string $username, string $email, string $password)
    {
        parent::__construct($username, $email, $password, 'editor');
    }
}

class Admin extends User
{
    public function __construct(string $username, string $email, string $password)
    {
        parent::__construct($username, $email, $password, 'admin');
    }
}

class Visitor extends User
{
    public function __construct(string $username, string $email, string $password)
    {
        parent::__construct($username, $email, $password, 'visitor');
    }
}

class Article
{
    private static int $nextId = 1;
    private int $id;
    private string $title;
    private string $content;
    private User $author;
    private string $status;
    private array $categories = [];
    private array $comments = [];

    public function __construct(string $title, string $content, User $author, string $status = 'draft')
    {
        $this->id = self::$nextId++;
        $this->title = $title;
        $this->content = $content;
        $this->author = $author;
        $this->status = $status;
    }

    public function publish(): void
    {
        $this->status = 'published';
    }

    public function archive(): void
    {
        $this->status = 'archived';
    }

    public function addCategory(Category $category): void
    {
        $this->categories[] = $category;
    }

    public function canEdit(User $user): bool
    {
        if ($user->getRole() === 'editor' || $user->getRole() === 'admin') {
            return true;
        }
        return $this->author->getId() === $user->getId();
    }

    public function addComment(Comment $comment): void
    {
        $this->comments[] = $comment;
    }

    public function removeComment(Comment $comment): bool
    {
        $index = array_search($comment, $this->comments, true);
        if ($index !== false) {
            unset($this->comments[$index]);
            $this->comments = array_values($this->comments);
            return true;
        }
        return false;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    public function getAuthor(): User
    {
        return $this->author;
    }

    public function getCategories(): array
    {
        return $this->categories;
    }

    public function getComments(): array
    {
        return $this->comments;
    }

    public function getApprovedComments(): array
    {
        return array_filter($this->comments, fn($comment) => $comment->getStatus() === 'approved');
    }
}

class Category
{
    private static int $nextId = 1;
    private int $id;
    private string $name;
    private string $description;

    public function __construct(string $name, string $description)
    {
        $this->id = self::$nextId++;
        $this->name = $name;
        $this->description = $description;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): string
    {
        return $this->description;
    }
}

class Comment
{
    private static int $nextId = 1;
    private int $id;
    private string $content;
    private User $author;
    private Article $article;
    private string $status;

    public function __construct(string $content, User $author, Article $article, string $status = 'pending')
    {
        $this->id = self::$nextId++;
        $this->content = $content;
        $this->author = $author;
        $this->article = $article;
        $this->status = $status;
    }

    public function approve(): void
    {
        $this->status = 'approved';
    }

    public function markAsSpam(): void
    {
        $this->status = 'spam';
    }

    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    public function canEdit(User $user): bool
    {
        if ($user->getRole() === "editor" || $user->getRole() === 'admin') {
            return true;
        }
        return $this->author->getId() === $user->getId();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getAuthor(): User
    {
        return $this->author;
    }

    public function getStatus(): string
    {
        return $this->status;
    }
}
