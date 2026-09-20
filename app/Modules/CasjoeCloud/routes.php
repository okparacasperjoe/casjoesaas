<?php

use App\Core\Router;
use App\Modules\CasjoeCloud\Controllers\CloudController;
use App\Modules\CasjoeCloud\Controllers\AssetController;

Router::get('/cloud/asset/{tenant_id}/{filename}', [AssetController::class, 'serve']);
Router::get('/cloud', [CloudController::class, 'index']);
Router::get('/cloud/api/list', [CloudController::class, 'apiListFiles']);
Router::post('/cloud/upload', [CloudController::class, 'upload']);
Router::post('/cloud/folder/create', [CloudController::class, 'createFolder']);
Router::post('/cloud/delete', [CloudController::class, 'deleteFile']);
Router::get('/cloud/preview', [CloudController::class, 'preview']);
Router::post('/cloud/folder/share', [CloudController::class, 'shareFolder']);
Router::get('/cloud/share', [CloudController::class, 'sharedFolder']);
Router::post('/cloud/share/upload', [CloudController::class, 'uploadShared']);
Router::post('/cloud/share/delete', [CloudController::class, 'deleteSharedFile']);
Router::post('/cloud/file/share', [CloudController::class, 'shareFile']);
Router::get('/cloud/file/share', [CloudController::class, 'sharedFileView']);
Router::get('/cloud/file/download', [CloudController::class, 'downloadSharedFile']);
Router::get('/cloud/share/download', [CloudController::class, 'downloadSharedFolderFile']);
Router::get('/cloud/folder-tree', [CloudController::class, 'folderTree']);
Router::get('/cloud/api/images', [CloudController::class, 'apiImages']);
Router::post('/cloud/settings/save', [CloudController::class, 'saveSettings']);
