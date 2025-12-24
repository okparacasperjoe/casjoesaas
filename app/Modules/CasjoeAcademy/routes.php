<?php

use App\Core\Router;
use App\Modules\CasjoeAcademy\Controllers\AcademyController;

// Academy Routes
Router::get('/academy', [AcademyController::class, 'index']);
Router::get('/academy/my-courses', [AcademyController::class, 'myCourses']);
Router::get('/academy/course/{id}', [AcademyController::class, 'course']);
Router::get('/academy/enroll/{id}', [AcademyController::class, 'enroll']);
Router::get('/academy/learn/{id}', [AcademyController::class, 'learn']);

// End of Academy Routes
