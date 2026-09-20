<?php

use App\Core\Router;
use App\Modules\CasjoeAcademy\Controllers\AcademyController;
use App\Modules\CasjoeAcademy\Controllers\LibraryController;
use App\Modules\CasjoeAcademy\Controllers\InstructorController;
use App\Modules\CasjoeAcademy\Controllers\LearnerController;
use App\Modules\CasjoeAcademy\Controllers\QuizController;
use App\Modules\CasjoeAcademy\Controllers\CertificateController;
use App\Modules\CasjoeAcademy\Controllers\BusinessController;
use App\Modules\CasjoeAcademy\Controllers\KnowledgeBaseController;
use App\Modules\CasjoeCloud\Controllers\AssetController;

// Asset Serving Route
Router::get('/cloud/asset/{tenant_id}/{filename}', [AssetController::class, 'serve']);

// Academy Routes
Router::get('/academy', [AcademyController::class, 'index']);
Router::get('/academy/knowledge-base', [KnowledgeBaseController::class, 'index']);
Router::get('/academy/knowledge-base/edit', [KnowledgeBaseController::class, 'edit']);
Router::post('/academy/knowledge-base/update', [KnowledgeBaseController::class, 'update']);
Router::get('/academy/overview', [AcademyController::class, 'overview']);
Router::get('/academy/catalog', [AcademyController::class, 'catalog']);
Router::get('/academy/my-courses', [AcademyController::class, 'myCourses']);
Router::get('/academy/course/{id}', [AcademyController::class, 'course']);
Router::get('/academy/enroll/{id}', [AcademyController::class, 'enroll']);
Router::get('/academy/learn/{id}', [AcademyController::class, 'learn']);

// Academy E-Library & Books Routes (All Tenants)
Router::get('/academy/library', [LibraryController::class, 'index']);
Router::get('/academy/library/read/{id}', [LibraryController::class, 'read']);
Router::get('/academy/library/pdf/{id}', [LibraryController::class, 'servePdf']);
Router::get('/academy/library/create', [LibraryController::class, 'create']);
Router::get('/academy/library/upload', [LibraryController::class, 'create']);
Router::post('/academy/library/store', [LibraryController::class, 'store']);
Router::get('/academy/library/edit/{id}', [LibraryController::class, 'edit']);
Router::post('/academy/library/update', [LibraryController::class, 'update']);
Router::post('/academy/library/progress', [LibraryController::class, 'saveProgress']);

// Academy Instructor Routes
Router::get('/academy/instructor', [InstructorController::class, 'index']);
Router::get('/academy/instructor/create', [InstructorController::class, 'create']);
Router::post('/academy/instructor/store', [InstructorController::class, 'store']);
Router::post('/academy/store', [InstructorController::class, 'store']);
Router::get('/academy/instructor/edit/{id}', [InstructorController::class, 'edit']);
Router::post('/academy/instructor/add-section', [InstructorController::class, 'addSection']);
Router::post('/academy/instructor/section/add', [InstructorController::class, 'addSection']);
Router::post('/academy/instructor/add-lesson', [InstructorController::class, 'addLesson']);
Router::post('/academy/instructor/lesson/add', [InstructorController::class, 'addLesson']);
Router::get('/academy/instructor/edit-lesson/{id}', [InstructorController::class, 'editLesson']);
Router::post('/academy/instructor/update-lesson', [InstructorController::class, 'updateLesson']);
Router::post('/academy/instructor/lesson/update', [InstructorController::class, 'updateLesson']);
Router::post('/academy/instructor/update-course', [InstructorController::class, 'updateCourse']);
Router::post('/academy/instructor/course/update', [InstructorController::class, 'updateCourse']);
Router::post('/academy/instructor/sort-sections', [InstructorController::class, 'sortSections']);
Router::post('/academy/instructor/sort-lessons', [InstructorController::class, 'sortLessons']);
Router::post('/academy/instructor/upload-image', [InstructorController::class, 'uploadEditorImage']);
Router::get('/academy/instructor/publish/{id}', [InstructorController::class, 'publish']);
Router::post('/academy/instructor/ai-outline', [InstructorController::class, 'generateAiOutline']);
Router::post('/academy/ai/generate', [InstructorController::class, 'generateAiOutline']);

// Academy Learner Routes
Router::get('/academy/learner', [LearnerController::class, 'dashboard']);
Router::get('/academy/learn', [LearnerController::class, 'dashboard']);
Router::get('/academy/learner/learn/{id}', [LearnerController::class, 'learn']);
Router::get('/academy/learner/complete/{id}', [LearnerController::class, 'markComplete']);
Router::post('/academy/learner/complete-lesson', [LearnerController::class, 'completeLesson']);
Router::post('/academy/enroll/process', [AcademyController::class, 'enroll']);

// Academy Quiz & Certificate Routes
Router::get('/academy/quiz/create/{id}', [QuizController::class, 'create']);
Router::post('/academy/quiz/store', [QuizController::class, 'store']);
Router::post('/academy/quiz/store/{id}', [QuizController::class, 'store']);
Router::get('/academy/quiz/take/{id}', [QuizController::class, 'take']);
Router::post('/academy/quiz/submit', [QuizController::class, 'submit']);
Router::post('/academy/quiz/submit/{id}', [QuizController::class, 'submit']);
Router::get('/academy/quiz/result/{id}', [QuizController::class, 'result']);

Router::get('/academy/certificate/generate/{id}', [CertificateController::class, 'generate']);
Router::get('/academy/certificate/view/{id}', [CertificateController::class, 'view']);

// Academy Business / Enterprise B2B Routes
Router::get('/academy/business', [BusinessController::class, 'dashboard']);
Router::get('/academy/business/marketplace', [BusinessController::class, 'marketplace']);
Router::post('/academy/business/buy-license', [BusinessController::class, 'buyLicense']);
Router::post('/academy/business/buy', [BusinessController::class, 'buyLicense']);
Router::get('/academy/business/assign', [BusinessController::class, 'assignView']);
Router::post('/academy/business/assign-store', [BusinessController::class, 'assignStore']);

// Tracks Routes
use App\Modules\CasjoeAcademy\Controllers\TrackController;

Router::get('/academy/create', [InstructorController::class, 'create']);
Router::get('/academy/tracks', [TrackController::class, 'index']);
Router::get('/academy/tracks/create', [TrackController::class, 'create']);
Router::post('/academy/tracks/store', [TrackController::class, 'store']);

// End of Academy Routes

