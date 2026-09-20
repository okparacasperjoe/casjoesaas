<?php

namespace App\Modules\CasjoeAcademy\Controllers;

use App\Core\Database;
use App\Core\TenantContext;

class LibraryController
{
    private $db;
    private $tenantId;

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $this->db = Database::getInstance();
        $this->tenantId = TenantContext::getTenantId();
    }

    // List all books
    public function index()
    {
        // Require Login
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login?redirect=/academy/library');
            exit;
        }

        $stmt = $this->db->prepare("SELECT * FROM academy_books WHERE (is_published = 1 OR is_published IS NULL) ORDER BY id DESC");
        $stmt->execute();
        $rawBooks = $stmt->fetchAll();
        $uniqueBooks = [];
        foreach ($rawBooks as $b) {
            $key = strtolower(trim($b['title']));
            if (!isset($uniqueBooks[$key])) {
                $uniqueBooks[$key] = $b;
            }
        }
        $books = array_values($uniqueBooks);

        require __DIR__ . '/../Views/library/index.php';
    }

    // Read a specific book
    public function read($params)
    {
        $id = is_array($params) ? ($params['id'] ?? array_values($params)[0]) : $params;
        if (is_array($id)) $id = array_values($id)[0];
        
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        $stmt = $this->db->prepare("SELECT * FROM academy_books WHERE id = ?");
        $stmt->execute([$id]);
        $book = $stmt->fetch();

        if (!$book) {
            die("Book not found.");
        }

        // Fetch User Progress
        $progressStmt = $this->db->prepare("SELECT last_page FROM academy_book_progress WHERE user_id = ? AND book_id = ?");
        $progressStmt->execute([$_SESSION['user_id'], $id]);
        $progress = $progressStmt->fetch();
        $startPage = $progress ? $progress['last_page'] : 1;

        require __DIR__ . '/../Views/library/reader.php';
    }

    // Save Progress (called via AJAX)
    public function saveProgress()
    {
        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            exit;
        }

        $data = json_decode(file_get_contents('php://input'), true);
        $bookId = $data['book_id'];
        $page = $data['page'];

        $sql = "INSERT INTO academy_book_progress (user_id, book_id, last_page) 
                VALUES (?, ?, ?) 
                ON DUPLICATE KEY UPDATE last_page = VALUES(last_page)";
        
        $this->db->query($sql, [$_SESSION['user_id'], $bookId, $page]);
        
        echo json_encode(['status' => 'saved']);
    }

    // Serve the PDF file securely
    public function servePdf($params)
    {
        $id = is_array($params) ? ($params['id'] ?? array_values($params)[0]) : $params;
        if (is_array($id)) $id = array_values($id)[0];
        
        if (!isset($_SESSION['user_id'])) {
            http_response_code(403);
            die("Unauthorized");
        }

        $stmt = $this->db->prepare("SELECT pdf_path FROM academy_books WHERE id = ?");
        $stmt->execute([$id]);
        $book = $stmt->fetch();

        if (!$book || !file_exists(__DIR__ . '/../../../../public' . $book['pdf_path'])) {
            http_response_code(404);
            die("File not found");
        }

        $filePath = __DIR__ . '/../../../../public' . $book['pdf_path'];
        
        header('Content-Type: application/pdf');
        header('Content-Disposition: inline; filename="book.pdf"');
        readfile($filePath);
    }
    // Upload Form
    public function create()
    {
        // Admin Check
        if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
            header('Location: /academy/library');
            exit;
        }
        
        require __DIR__ . '/../Views/library/create.php';
    }

    // Store Book
    public function store()
    {
        // Admin Check
        if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
            http_response_code(403);
            die("Unauthorized action.");
        }

        $title = $_POST['title'];
        $author = $_POST['author'];
        $tenantId = $this->tenantId;

        // Handle Cover Upload
        $coverPath = null;
        if (isset($_FILES['cover']) && $_FILES['cover']['error'] == 0) {
            $uploadDir = __DIR__ . '/../../../../public/uploads/covers/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
            
            $filename = time() . '_' . basename($_FILES['cover']['name']);
            move_uploaded_file($_FILES['cover']['tmp_name'], $uploadDir . $filename);
            $coverPath = '/uploads/covers/' . $filename;
        }

        // Handle PDF Upload
        $pdfPath = null;
        if (isset($_FILES['pdf']) && $_FILES['pdf']['error'] == 0) {
            $uploadDir = __DIR__ . '/../../../../public/uploads/books/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
            
            $filename = time() . '_' . basename($_FILES['pdf']['name']);
            move_uploaded_file($_FILES['pdf']['tmp_name'], $uploadDir . $filename);
            $pdfPath = '/uploads/books/' . $filename;
        } else {
            die("PDF is required.");
        }

        $stmt = $this->db->prepare("INSERT INTO academy_books (tenant_id, title, author, cover_path, pdf_path) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$tenantId, $title, $author, $coverPath, $pdfPath]);

        header('Location: /academy/library');
        exit;
    }

    // Edit Book View
    public function edit($params)
    {
        // Admin Check
        if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
            header('Location: /academy/library');
            exit;
        }

        $id = is_array($params) ? ($params['id'] ?? array_values($params)[0]) : $params;
        if (is_array($id)) $id = array_values($id)[0];
        
        $stmt = $this->db->prepare("SELECT * FROM academy_books WHERE id = ? AND tenant_id = ?");
        $stmt->execute([$id, $this->tenantId]);
        $book = $stmt->fetch();

        if (!$book) {
            die("Book not found.");
        }

        require __DIR__ . '/../Views/library/edit.php';
    }

    // Update Book
    public function update()
    {
        // Admin Check
        if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
            http_response_code(403);
            die("Unauthorized action.");
        }

        $id = $_POST['id'];
        $title = $_POST['title'];
        $author = $_POST['author'];
        $tenantId = $this->tenantId;

        // Verify book belongs to tenant
        $verifyStmt = $this->db->prepare("SELECT id, cover_path, pdf_path FROM academy_books WHERE id = ? AND tenant_id = ?");
        $verifyStmt->execute([$id, $tenantId]);
        $book = $verifyStmt->fetch();

        if (!$book) {
            die("Book not found or unauthorized.");
        }

        $coverPath = $book['cover_path']; // Default to existing
        $pdfPath = $book['pdf_path'];     // Default to existing

        // Handle Cover Upload
        if (isset($_FILES['cover']) && $_FILES['cover']['error'] == 0) {
            $uploadDir = __DIR__ . '/../../../../public/uploads/covers/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
            
            $filename = time() . '_' . basename($_FILES['cover']['name']);
            move_uploaded_file($_FILES['cover']['tmp_name'], $uploadDir . $filename);
            $coverPath = '/uploads/covers/' . $filename;
            
            // Optional: delete old cover file if it exists and isn't a default placeholder
            if ($book['cover_path'] && file_exists(__DIR__ . '/../../../../public' . $book['cover_path'])) {
                @unlink(__DIR__ . '/../../../../public' . $book['cover_path']);
            }
        }

        // Handle PDF Upload
        if (isset($_FILES['pdf']) && $_FILES['pdf']['error'] == 0) {
            $uploadDir = __DIR__ . '/../../../../public/uploads/books/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
            
            $filename = time() . '_' . basename($_FILES['pdf']['name']);
            move_uploaded_file($_FILES['pdf']['tmp_name'], $uploadDir . $filename);
            $pdfPath = '/uploads/books/' . $filename;
            
            // Optional: delete old pdf file if it exists
            if ($book['pdf_path'] && file_exists(__DIR__ . '/../../../../public' . $book['pdf_path'])) {
                 @unlink(__DIR__ . '/../../../../public' . $book['pdf_path']);
            }
        }

        $stmt = $this->db->prepare("UPDATE academy_books SET title = ?, author = ?, cover_path = ?, pdf_path = ? WHERE id = ? AND tenant_id = ?");
        $stmt->execute([$title, $author, $coverPath, $pdfPath, $id, $tenantId]);

        header('Location: /academy/library');
        exit;
    }

}
