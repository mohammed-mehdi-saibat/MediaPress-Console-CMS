<?php

require_once 'mediapress.php';

class BlogCMS
{
    private array $users = [];
    private array $articles = [];
    private array $categories = [];
    private ?User $currentUser = null;

    public function run(): void
    {
        $this->createInitialData();
        $this->showWelcome();
        $this->showMainMenu();
    }

    private function createInitialData(): void
    {
        $this->users[] = new Visitor("marco", "marco@test.com", "pass123");
        $this->users[] = new Author("lea", "lea@test.com", "pass123", "Tech writer");
        $this->users[] = new Editor("thomas", "thomas@test.com", "pass123");
        $this->users[] = new Admin("amina", "amina@test.com", "pass123");

        $this->categories[] = new Category("Tech", "Technology news");
        $this->categories[] = new Category("Sports", "Sports news");
        $this->categories[] = new Category("Food", "Food recipes");

        $author1 = $this->findUserByUsername("lea");
        $visitor = $this->findUserByUsername("marco");

        $article1 = new Article(
            "AI Future",
            "AI is changing everything.",
            $author1,
            "published"
        );
        $article1->addCategory($this->categories[0]);
        $this->articles[] = $article1;

        $comment1 = new Comment("Great article!", $visitor, $article1, "approved");
        $article1->addComment($comment1);

        $comment2 = new Comment("I disagree", $visitor, $article1, "pending");
        $article1->addComment($comment2);
    }

    private function showWelcome(): void
    {
        echo "=== BLOG CMS ===\n";
        echo "Users: " . count($this->users) . " | ";
        echo "Articles: " . count($this->articles) . " | ";
        echo "Categories: " . count($this->categories) . "\n";
        echo "================\n\n";
    }

    private function showMainMenu(): void
    {
        while (true) {
            echo "\n=== MAIN MENU ===\n";
            echo "1. Login as Visitor\n";
            echo "2. Login as Author\n";
            echo "3. Login as Editor\n";
            echo "4. Login as Admin\n";
            echo "5. Exit\n";
            echo "Choice: ";

            $choice = trim(fgets(STDIN));

            switch ($choice) {
                case '1':
                    $this->loginAs("marco", "Visitor");
                    break;
                case '2':
                    $this->loginAs("lea", "Author");
                    break;
                case '3':
                    $this->loginAs("thomas", "Editor");
                    break;
                case '4':
                    $this->loginAs("amina", "Admin");
                    break;
                case '5':
                    echo "Goodbye!\n";
                    exit;
                default:
                    echo "Invalid choice.\n";
            }
        }
    }

    private function loginAs(string $username, string $roleName): void
    {
        $this->currentUser = $this->findUserByUsername($username);
        echo "\nLogged in as {$roleName}: {$username}\n";
        $this->showRoleMenu();
    }

    private function showRoleMenu(): void
    {
        if (!$this->currentUser) return;

        $role = $this->currentUser->getRole();

        while (true) {
            echo "\n=== " . strtoupper($role) . " MENU ===\n";
            echo "1. View Articles\n";
            echo "2. View Article with Comments\n";

            if ($role === 'author' || $role === 'editor' || $role === 'admin') {
                echo "3. Create Article\n";
                echo "4. My Articles\n";
            }

            if ($role === 'editor' || $role === 'admin') {
                echo "5. Manage Comments\n";
                echo "6. Manage Categories\n";
            }

            if ($role === 'admin') {
                echo "7. Manage Users\n";
            }

            echo "8. Logout\n";
            echo "Choice: ";

            $choice = trim(fgets(STDIN));

            switch ($choice) {
                case '1':
                    $this->listArticles();
                    break;
                case '2':
                    $this->viewArticleWithComments();
                    break;
                case '3':
                    if ($role === 'author' || $role === 'editor' || $role === 'admin') {
                        $this->createArticle();
                    }
                    break;
                case '4':
                    if ($role === 'author' || $role === 'editor' || $role === 'admin') {
                        $this->listMyArticles();
                    }
                    break;
                case '5':
                    if ($role === 'editor' || $role === 'admin') {
                        $this->manageComments();
                    }
                    break;
                case '6':
                    if ($role === 'editor' || $role === 'admin') {
                        $this->manageCategories();
                    }
                    break;
                case '7':
                    if ($role === 'admin') {
                        $this->manageUsers();
                    }
                    break;
                case '8':
                    echo "Logging out...\n";
                    $this->currentUser = null;
                    return;
                default:
                    echo "Invalid choice.\n";
            }
        }
    }

    private function listArticles(): void
    {
        if (empty($this->articles)) {
            echo "No articles.\n";
            return;
        }

        echo "\n=== ARTICLES ===\n";
        foreach ($this->articles as $index => $article) {
            echo ($index + 1) . ". [{$article->getStatus()}] ";
            echo "{$article->getTitle()} by {$article->getAuthor()->getUsername()}\n";
        }

        echo "\nEnter article number to view (0 to go back): ";
        $choice = trim(fgets(STDIN));

        if ($choice !== '0' && is_numeric($choice) && $choice > 0 && $choice <= count($this->articles)) {
            $this->viewArticleDetails($this->articles[$choice - 1]);
        }
    }

    private function viewArticleDetails(Article $article): void
    {
        echo "\n=== {$article->getTitle()} ===\n";
        echo "Author: {$article->getAuthor()->getUsername()}\n";
        echo "Status: {$article->getStatus()}\n";
        echo "Content: {$article->getContent()}\n";

        $comments = $article->getComments();
        if (!empty($comments)) {
            echo "\nComments:\n";
            foreach ($comments as $comment) {
                echo "- [{$comment->getStatus()}] {$comment->getAuthor()->getUsername()}: {$comment->getContent()}\n";
            }
        }

        echo "\nPress Enter to continue...";
        fgets(STDIN);
    }

    private function viewArticleWithComments(): void
    {
        if (empty($this->articles)) {
            echo "No articles.\n";
            return;
        }

        echo "\n=== ARTICLES ===\n";
        foreach ($this->articles as $index => $article) {
            $commentCount = count($article->getComments());
            echo ($index + 1) . ". {$article->getTitle()} ({$commentCount} comments)\n";
        }

        echo "\nEnter article number (0 to go back): ";
        $choice = trim(fgets(STDIN));

        if ($choice !== '0' && is_numeric($choice) && $choice > 0 && $choice <= count($this->articles)) {
            $article = $this->articles[$choice - 1];
            $this->viewArticleDetails($article);

            if ($this->currentUser) {
                echo "\nAdd comment? (y/n): ";
                if (trim(fgets(STDIN)) === 'y') {
                    $this->addComment($article);
                }
            }
        }
    }

    private function addComment(Article $article): void
    {
        echo "Your comment: ";
        $content = trim(fgets(STDIN));

        if (!empty($content)) {
            $status = 'pending';
            if ($this->currentUser->getRole() === 'editor' || $this->currentUser->getRole() === 'admin') {
                $status = 'approved';
            }

            $comment = new Comment($content, $this->currentUser, $article, $status);
            $article->addComment($comment);
            echo "Comment added!\n";
        }
    }

    private function createArticle(): void
    {
        echo "\n=== CREATE ARTICLE ===\n";

        echo "Title: ";
        $title = trim(fgets(STDIN));

        echo "Content: ";
        $content = trim(fgets(STDIN));

        if (!empty($title) && !empty($content)) {
            $article = new Article($title, $content, $this->currentUser, 'draft');

            if (!empty($this->categories)) {
                echo "\nCategories:\n";
                foreach ($this->categories as $index => $category) {
                    echo ($index + 1) . ". {$category->getName()}\n";
                }

                echo "Add categories (comma-separated numbers, or 0 to skip): ";
                $input = trim(fgets(STDIN));

                if ($input !== '0') {
                    $choices = array_map('intval', array_map('trim', explode(',', $input)));
                    foreach ($choices as $choice) {
                        if ($choice > 0 && $choice <= count($this->categories)) {
                            $article->addCategory($this->categories[$choice - 1]);
                        }
                    }
                }
            }

            $this->articles[] = $article;
            echo "Article created!\n";
        }
    }

    private function listMyArticles(): void
    {
        $myArticles = array_filter($this->articles, fn($a) => $a->getAuthor()->getId() === $this->currentUser->getId());

        if (empty($myArticles)) {
            echo "You have no articles.\n";
            return;
        }

        echo "\n=== MY ARTICLES ===\n";
        foreach ($myArticles as $index => $article) {
            echo ($index + 1) . ". [{$article->getStatus()}] {$article->getTitle()}\n";
        }

        echo "\nEnter article number to manage (0 to go back): ";
        $choice = trim(fgets(STDIN));

        if ($choice !== '0' && is_numeric($choice) && $choice > 0 && $choice <= count($myArticles)) {
            $articlesArray = array_values($myArticles);
            $this->manageArticle($articlesArray[$choice - 1]);
        }
    }

    private function manageArticle(Article $article): void
    {
        echo "\n=== MANAGE ARTICLE ===\n";
        echo "Title: {$article->getTitle()}\n";
        echo "Status: {$article->getStatus()}\n";

        echo "\n1. Publish\n";
        echo "2. Archive\n";
        echo "3. Delete\n";
        echo "0. Back\n";
        echo "Choice: ";

        $choice = trim(fgets(STDIN));

        switch ($choice) {
            case '1':
                $article->publish();
                echo "Article published!\n";
                break;
            case '2':
                $article->archive();
                echo "Article archived!\n";
                break;
            case '3':
                $this->deleteArticle($article);
                break;
        }
    }

    private function deleteArticle(Article $article): void
    {
        if (!$article->canEdit($this->currentUser)) {
            echo "Cannot delete.\n";
            return;
        }

        $index = array_search($article, $this->articles, true);
        if ($index !== false) {
            array_splice($this->articles, $index, 1);
            echo "Article deleted.\n";
        }
    }

    private function manageComments(): void
    {
        echo "\n=== MANAGE COMMENTS ===\n";
        echo "1. View All Comments\n";
        echo "2. Moderate Pending\n";
        echo "3. Edit Comment\n";
        echo "4. Delete Comment\n";
        echo "0. Back\n";
        echo "Choice: ";

        $choice = trim(fgets(STDIN));

        switch ($choice) {
            case '1':
                $this->viewAllComments();
                break;
            case '2':
                $this->moderateComments();
                break;
            case '3':
                $this->editComment();
                break;
            case '4':
                $this->deleteComment();
                break;
        }
    }

    private function viewAllComments(): void
    {
        $allComments = [];
        foreach ($this->articles as $article) {
            foreach ($article->getComments() as $comment) {
                $allComments[] = ['article' => $article, 'comment' => $comment];
            }
        }

        if (empty($allComments)) {
            echo "No comments.\n";
            return;
        }

        echo "\n=== ALL COMMENTS ===\n";
        foreach ($allComments as $index => $item) {
            $comment = $item['comment'];
            echo ($index + 1) . ". [{$comment->getStatus()}] ";
            echo "{$comment->getAuthor()->getUsername()}: {$comment->getContent()}\n";
        }
    }

    private function moderateComments(): void
    {
        $pendingComments = [];
        foreach ($this->articles as $article) {
            foreach ($article->getComments() as $comment) {
                if ($comment->getStatus() === 'pending') {
                    $pendingComments[] = ['article' => $article, 'comment' => $comment];
                }
            }
        }

        if (empty($pendingComments)) {
            echo "No pending comments.\n";
            return;
        }

        echo "\n=== PENDING COMMENTS ===\n";
        foreach ($pendingComments as $index => $item) {
            $comment = $item['comment'];
            echo ($index + 1) . ". {$comment->getAuthor()->getUsername()}: {$comment->getContent()}\n";
        }

        echo "\nEnter comment number to moderate (0 to go back): ";
        $choice = trim(fgets(STDIN));

        if ($choice !== '0' && is_numeric($choice) && $choice > 0 && $choice <= count($pendingComments)) {
            $item = $pendingComments[$choice - 1];
            $comment = $item['comment'];

            echo "\n1. Approve\n";
            echo "2. Mark as Spam\n";
            echo "Choice: ";

            $action = trim(fgets(STDIN));

            if ($action === '1') {
                $comment->approve();
                echo "Approved!\n";
            } elseif ($action === '2') {
                $comment->markAsSpam();
                echo "Marked as spam!\n";
            }
        }
    }

    private function editComment(): void
    {
        $allComments = [];
        foreach ($this->articles as $article) {
            foreach ($article->getComments() as $comment) {
                $allComments[] = ['article' => $article, 'comment' => $comment];
            }
        }

        if (empty($allComments)) {
            echo "No comments.\n";
            return;
        }

        echo "\n=== EDIT COMMENT ===\n";
        foreach ($allComments as $index => $item) {
            $comment = $item['comment'];
            echo ($index + 1) . ". [{$comment->getStatus()}] ";
            echo "{$comment->getAuthor()->getUsername()}: {$comment->getContent()}\n";
        }

        echo "\nEnter comment number to edit (0 to go back): ";
        $choice = trim(fgets(STDIN));

        if ($choice !== '0' && is_numeric($choice) && $choice > 0 && $choice <= count($allComments)) {
            $item = $allComments[$choice - 1];
            $comment = $item['comment'];

            if (!$comment->canEdit($this->currentUser)) {
                echo "Cannot edit this comment.\n";
                return;
            }

            echo "Current: {$comment->getContent()}\n";
            echo "New content: ";
            $newContent = trim(fgets(STDIN));

            if (!empty($newContent)) {
                // Note: Comment class needs updateContent method
                // For now, we'll just create a new comment
                echo "Editing not implemented in simple version.\n";
            }
        }
    }

    private function deleteComment(): void
    {
        $allComments = [];
        foreach ($this->articles as $article) {
            foreach ($article->getComments() as $comment) {
                $allComments[] = ['article' => $article, 'comment' => $comment];
            }
        }

        if (empty($allComments)) {
            echo "No comments.\n";
            return;
        }

        echo "\n=== DELETE COMMENT ===\n";
        foreach ($allComments as $index => $item) {
            $comment = $item['comment'];
            echo ($index + 1) . ". [{$comment->getStatus()}] ";
            echo "{$comment->getAuthor()->getUsername()}: {$comment->getContent()}\n";
        }

        echo "\nEnter comment number to delete (0 to go back): ";
        $choice = trim(fgets(STDIN));

        if ($choice !== '0' && is_numeric($choice) && $choice > 0 && $choice <= count($allComments)) {
            $item = $allComments[$choice - 1];
            $comment = $item['comment'];
            $article = $item['article'];

            if (!$comment->canEdit($this->currentUser)) {
                echo "Cannot delete this comment.\n";
                return;
            }

            if ($article->removeComment($comment)) {
                echo "Comment deleted!\n";
            }
        }
    }

    private function manageCategories(): void
    {
        echo "\n=== MANAGE CATEGORIES ===\n";
        echo "1. List Categories\n";
        echo "2. Add Category\n";
        echo "0. Back\n";
        echo "Choice: ";

        $choice = trim(fgets(STDIN));

        switch ($choice) {
            case '1':
                $this->listCategories();
                break;
            case '2':
                $this->addCategory();
                break;
        }
    }

    private function listCategories(): void
    {
        if (empty($this->categories)) {
            echo "No categories.\n";
            return;
        }

        echo "\n=== CATEGORIES ===\n";
        foreach ($this->categories as $index => $category) {
            echo ($index + 1) . ". {$category->getName()} - {$category->getDescription()}\n";
        }
    }

    private function addCategory(): void
    {
        echo "\n=== ADD CATEGORY ===\n";

        echo "Name: ";
        $name = trim(fgets(STDIN));

        echo "Description: ";
        $desc = trim(fgets(STDIN));

        if (!empty($name)) {
            $this->categories[] = new Category($name, $desc);
            echo "Category added!\n";
        }
    }

    private function manageUsers(): void
    {
        echo "\n=== MANAGE USERS ===\n";
        echo "1. List Users\n";
        echo "2. Change User Role\n";
        echo "0. Back\n";
        echo "Choice: ";

        $choice = trim(fgets(STDIN));

        switch ($choice) {
            case '1':
                $this->listUsers();
                break;
            case '2':
                $this->changeUserRole();
                break;
        }
    }

    private function listUsers(): void
    {
        echo "\n=== USERS ===\n";
        foreach ($this->users as $index => $user) {
            echo ($index + 1) . ". {$user->getUsername()} ({$user->getRole()})\n";
        }
    }

    private function changeUserRole(): void
    {
        echo "\n=== CHANGE USER ROLE ===\n";
        foreach ($this->users as $index => $user) {
            echo ($index + 1) . ". {$user->getUsername()} ({$user->getRole()})\n";
        }

        echo "\nEnter user number: ";
        $choice = trim(fgets(STDIN));

        if (is_numeric($choice) && $choice > 0 && $choice <= count($this->users)) {
            $user = $this->users[$choice - 1];

            echo "Current role: {$user->getRole()}\n";
            echo "New role (visitor/author/editor/admin): ";
            $newRole = trim(fgets(STDIN));

            if (in_array($newRole, ['visitor', 'author', 'editor', 'admin'])) {
                $user->setRole($newRole);
                echo "Role changed!\n";
            }
        }
    }

    private function findUserByUsername(string $username): ?User
    {
        foreach ($this->users as $user) {
            if ($user->getUsername() === $username) {
                return $user;
            }
        }
        return null;
    }
}

$cms = new BlogCMS();
$cms->run();
