<?php

class User
{
    private int $id;
    private string  $username;
    private string $email;
    private string $password;
    private string $role;
    private DateTime $createdAt;
    private ?DateTime $lastLogin = null;

    // private const validRoles = ['visitor', 'author', 'editor', 'admin'];

    public function __construct(int $id, string $username, string $email, string $role, DateTime $createdAt, ?DateTime $lastLogin = null)
    {
        $this->id = $id;
        $this->username = $username;
        $this->email = $email;
        $this->role = $role;
        $this->createdAt = $createdAt;
        $this->lastLogin = $lastLogin;
    }

    public function setPassword(string $password): void
    {
        $this->password = password_hash($password, PASSWORD_DEFAULT);
    }
    public function updateLastLogin(): DateTime
    {
        return $this->lastLogin = new DateTime();
    }
    public function verifyPassword(string $password): bool
    {
        return password_verify($password, $this->password);
    }
    public function getId(): int
    {
        return $this->id;
    }
    public function getUserName(): string
    {
        return $this->username;
    }
    public function getEmail(): string
    {
        return $this->email;
    }
    public function getPassword(): string
    {
        return $this->password;
    }
    public function getRole(): string
    {
        return $this->role;
    }
    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }
    public function getLastLogin(): ?DateTime
    {
        return $this->lastLogin;
    }
}

class Article
{
    private int $id;
    private string $title;
    private string $content;
    private string $expert;
    private string $status;
    private  $author;
    private ?DateTime $createdAt;
    private ?DateTime $publishedAt;
    private ?DateTime $updatedAt;

    public function __construct($id, $title, $content, $expert, $status, $author, $createdAt, $publishedAt, $updatedAt)
    {
        $this->id = $id;
        $this->title = $title;
        $this->content = $content;
        $this->expert = $expert;
        $this->status = $status;
        $this->author = $author;
        $this->createdAt = $createdAt;
        $this->publishedAt = $publishedAt;
        $this->updatedAt = $updatedAt;
    }
}

class Category
{
    private int $id;
    private string $name;
    private string $description;
    private string  $parent;
    private ?DateTime $createdAt;

    public function __construct($id, $name, $description, $parent, $createdAt)
    {
        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
        $this->parent = $parent;
        $this->createdAt = $createdAt;
    }
}


class Author extends User
{
    private string $bio;

    public function __construct(int $id, string $username, string $email, DateTime $createdAt, string $bio, string $role = "author", ?DateTime $lastLogin = null)
    {
        parent::__construct($id,  $username, $email,  $role,  $createdAt, $lastLogin);
        $this->bio = $bio;
    }

    //----------GETTERS
    public function getId(): int
    {
        return parent::getId();
    }
    public function getUserName(): string
    {
        return parent::getUserName();
    }
    public function getEmail(): string
    {
        return parent::getEmail();
    }
    public function getPassword(): string
    {
        return parent::getPassword();
    }
    public function getRole(): string
    {
        return parent::getRole();
    }
    public function getCreatedAt(): DateTime
    {
        return parent::getCreatedAt();
    }
    public function getLastLogin(): ?DateTime
    {
        return parent::getLastLogin();
    }
    //----------GETTERS

    public function __toString()
    {
        return "------Author #{$this->getId()}\nUSERNAME: {$this->getUsername()}\nBIO: {$this->bio}\nEMAIL: {$this->getEmail()}\nROLE: {$this->getRole()}\n";
    }
}



class Editor extends User
{
    private string $moderationLevel;

    public function __construct($moderationLevel)
    {
        $this->moderationLevel = $moderationLevel;
    }
}

class Admin extends User
{
    private bool $isSuperAdmin;

    public function __construct($isSuperAdmin)
    {
        $this->isSuperAdmin = $isSuperAdmin;
    }
}
