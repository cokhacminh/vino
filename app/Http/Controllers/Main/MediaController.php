<?php

namespace App\Http\Controllers\Main;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MediaController extends Controller
{
    /**
     * Check Admin permission
     */
    private function checkAdmin()
    {
        $user = Auth::user();
        if (!$user || (!$user->hasRole('Admin') && !$user->can('Admin'))) {
            abort(403, 'Bạn không có quyền truy cập trang này.');
        }
    }

    /**
     * Check if current user has permission for a folder action
     */
    private function userCanAction($folderPath, $action = 'can_upload')
    {
        $user = Auth::user();
        if (!$user) return false;

        // Admin always has full access
        if ($user->hasRole('Admin') || $user->can('Admin')) return true;

        $folderPath = rtrim($folderPath, '/');

        // Check user-level permission
        $userPerm = DB::table('media_folder_permissions')
            ->where('folder_path', $folderPath)
            ->where('permission_type', 'user')
            ->where('user_id', $user->id)
            ->where($action, true)
            ->first();
        if ($userPerm) return true;

        // Check department-level permission
        if ($user->MaPB) {
            $deptPerm = DB::table('media_folder_permissions')
                ->where('folder_path', $folderPath)
                ->where('permission_type', 'department')
                ->where('department_id', $user->MaPB)
                ->where($action, true)
                ->first();
            if ($deptPerm) return true;
        }

        // Check parent folders (inherited permissions)
        $parts = explode('/', $folderPath);
        $parentPath = '';
        for ($i = 0; $i < count($parts) - 1; $i++) {
            $parentPath .= ($i > 0 ? '/' : '') . $parts[$i];

            $userPerm = DB::table('media_folder_permissions')
                ->where('folder_path', $parentPath)
                ->where('permission_type', 'user')
                ->where('user_id', $user->id)
                ->where($action, true)
                ->first();
            if ($userPerm) return true;

            if ($user->MaPB) {
                $deptPerm = DB::table('media_folder_permissions')
                    ->where('folder_path', $parentPath)
                    ->where('permission_type', 'department')
                    ->where('department_id', $user->MaPB)
                    ->where($action, true)
                    ->first();
                if ($deptPerm) return true;
            }
        }

        return false;
    }

    /**
     * Trang Quản Lý Dữ Liệu
     */
    public function storage()
    {
        // Chỉ user id=1 mới được truy cập trang này
        if (Auth::id() !== 1) {
            abort(403, 'Bạn không có quyền truy cập trang này.');
        }

        $config = [
            'endpoint'   => env('AWS_ENDPOINT', ''),
            'access_key' => env('AWS_ACCESS_KEY_ID', ''),
            'secret_key' => env('AWS_SECRET_ACCESS_KEY', ''),
            'bucket'     => env('AWS_BUCKET', ''),
            'region'     => env('AWS_DEFAULT_REGION', 'ap-southeast-1'),
            'path_style' => env('AWS_USE_PATH_STYLE_ENDPOINT', false),
        ];

        $isConfigured = !empty($config['endpoint']) && !empty($config['access_key']) && !empty($config['bucket']);

        // Get users and departments for permission UI
        $users = DB::table('users')
            ->where('TinhTrang', 'Active')
            ->select('id', 'name', 'MaPB')
            ->orderBy('name')
            ->get();

        $departments = DB::table('phongban')
            ->select('MaPB', 'TenPB')
            ->orderBy('TenPB')
            ->get();

        return view('main.media.storage', compact('config', 'isConfigured', 'users', 'departments'));
    }

    /**
     * Trang Thư Viện Ảnh
     */
    public function gallery()
    {
        return view('main.media.gallery');
    }

    /**
     * API: Get images from "Thư Viện Ảnh" folder (AJAX)
     */
    public function galleryApi(Request $request)
    {
        $basePath = 'Thư Viện Ảnh';
        $subFolder = $request->input('folder', '');
        $path = $subFolder ? $basePath . '/' . $subFolder : $basePath;

        try {
            $disk = Storage::disk('s3');

            // Get subfolders
            $directories = $disk->directories($path);
            $folders = [];
            foreach ($directories as $dir) {
                $dirName = str_replace($path . '/', '', $dir);
                if (strpos($dirName, '/') === false && $dirName !== '') {
                    // Try to get a thumbnail from the first image in the folder
                    $folderFiles = $disk->files($dir);
                    $thumb = null;
                    foreach ($folderFiles as $ff) {
                        $ext = strtolower(pathinfo($ff, PATHINFO_EXTENSION));
                        if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp'])) {
                            try {
                                $thumb = $disk->temporaryUrl($ff, now()->addMinutes(60));
                            } catch (\Exception $e) {
                                try { $thumb = $disk->url($ff); } catch (\Exception $e2) {}
                            }
                            break;
                        }
                    }
                    $folders[] = [
                        'name' => $dirName,
                        'path' => str_replace($basePath . '/', '', $dir),
                        'thumbnail' => $thumb,
                        'count' => count(array_filter($folderFiles, fn($f) => basename($f) !== '.keep')),
                    ];
                }
            }

            // Get images
            $allFiles = $disk->files($path);
            $images = [];
            $imageExts = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp', 'svg'];

            // Preload uploader info for all files in this folder
            $filePaths = collect($allFiles)->filter(function ($f) use ($imageExts) {
                return in_array(strtolower(pathinfo($f, PATHINFO_EXTENSION)), $imageExts);
            })->values()->all();
            $uploaders = DB::table('media_uploads')
                ->whereIn('file_path', $filePaths)
                ->pluck('uploaded_by_name', 'file_path');

            foreach ($allFiles as $file) {
                $fileName = basename($file);
                $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                if (!in_array($ext, $imageExts)) continue;

                $url = null;
                try {
                    $url = $disk->temporaryUrl($file, now()->addMinutes(60));
                } catch (\Exception $e) {
                    try { $url = $disk->url($file); } catch (\Exception $e2) {}
                }

                $size = null;
                $lastModified = null;
                try {
                    $size = $disk->size($file);
                    $lastModified = $disk->lastModified($file);
                } catch (\Exception $e) {}

                $images[] = [
                    'name' => $fileName,
                    'path' => $file,
                    'url' => $url,
                    'size' => $size,
                    'lastModified' => $lastModified,
                    'extension' => $ext,
                    'uploaded_by' => $uploaders[$file] ?? null,
                ];
            }

            // Get all folders under base path for Move modal (recursive scan)
            $allFoldersList = [['name' => 'Thư Viện Ảnh (gốc)', 'path' => '']];
            try {
                $scanDirs = function($parentPath) use (&$scanDirs, $disk, $basePath) {
                    $result = [];
                    $dirs = $disk->directories($parentPath);
                    foreach ($dirs as $d) {
                        $relative = $d;
                        if (strpos($d, $basePath . '/') === 0) {
                            $relative = substr($d, strlen($basePath . '/'));
                        }
                        if ($relative && $relative !== '.') {
                            $result[] = ['name' => $relative, 'path' => $relative];
                            // Recurse into subdirectories
                            $subResult = $scanDirs($d);
                            $result = array_merge($result, $subResult);
                        }
                    }
                    return $result;
                };
                $allFoldersList = array_merge($allFoldersList, $scanDirs($basePath));
            } catch (\Exception $e) {
                // Fallback: use directories we already fetched
                foreach ($folders as $f) {
                    $allFoldersList[] = ['name' => $f['name'], 'path' => $f['path']];
                }
            }

            // Breadcrumb
            $breadcrumb = [['name' => 'Thư Viện Ảnh', 'path' => '']];
            if ($subFolder) {
                $parts = explode('/', $subFolder);
                $built = '';
                foreach ($parts as $part) {
                    $built .= ($built ? '/' : '') . $part;
                    $breadcrumb[] = ['name' => $part, 'path' => $built];
                }
            }

            // Permissions for current user on this folder
            $user = Auth::user();
            $isAdmin = $user && ($user->hasRole('Admin') || $user->can('Admin'));
            $permissions = [
                'can_upload' => $isAdmin || $this->userCanAction($path, 'can_upload'),
                'can_rename' => $isAdmin || $this->userCanAction($path, 'can_rename'),
                'can_delete' => $isAdmin || $this->userCanAction($path, 'can_delete'),
            ];

            return response()->json([
                'success' => true,
                'folders' => $folders,
                'images' => $images,
                'breadcrumb' => $breadcrumb,
                'permissions' => $permissions,
                'allFolders' => $allFoldersList,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi tải ảnh: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Trang Thư Viện Video
     */
    public function videoGallery()
    {
        return view('main.media.video_gallery');
    }

    /**
     * API: Get videos from "Thư Viện Video" folder (AJAX)
     */
    public function videoGalleryApi(Request $request)
    {
        $basePath = 'Thư Viện Video';
        $subFolder = $request->input('folder', '');
        $path = $subFolder ? $basePath . '/' . $subFolder : $basePath;

        try {
            $disk = Storage::disk('s3');

            if (!$disk->exists($basePath)) {
                $disk->put($basePath . '/.keep', '');
            }

            $directories = $disk->directories($path);
            $folders = [];
            $videoExts = ['mp4', 'avi', 'mov', 'mkv', 'webm', 'flv', 'wmv', 'm4v'];

            foreach ($directories as $dir) {
                $dirName = str_replace($path . '/', '', $dir);
                if (strpos($dirName, '/') === false && $dirName !== '') {
                    $folderFiles = $disk->files($dir);
                    $count = 0;
                    foreach ($folderFiles as $ff) {
                        $ext = strtolower(pathinfo($ff, PATHINFO_EXTENSION));
                        if (in_array($ext, $videoExts)) $count++;
                    }
                    $folders[] = [
                        'name' => $dirName,
                        'path' => str_replace($basePath . '/', '', $dir),
                        'thumbnail' => null,
                        'count' => $count,
                    ];
                }
            }

            $allFiles = $disk->files($path);
            $videos = [];
            $filePaths = collect($allFiles)->filter(function ($f) use ($videoExts) {
                return in_array(strtolower(pathinfo($f, PATHINFO_EXTENSION)), $videoExts);
            })->values()->all();
            $uploaders = DB::table('media_uploads')
                ->whereIn('file_path', $filePaths)
                ->get()
                ->keyBy('file_path');

            foreach ($allFiles as $file) {
                $fileName = basename($file);
                $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                if (!in_array($ext, $videoExts)) continue;

                $url = null;
                try {
                    $url = $disk->temporaryUrl($file, now()->addMinutes(60));
                } catch (\Exception $e) {
                    try { $url = $disk->url($file); } catch (\Exception $e2) {}
                }

                $size = null;
                $lastModified = null;
                try {
                    $size = $disk->size($file);
                    $lastModified = $disk->lastModified($file);
                } catch (\Exception $e) {}

                $videos[] = [
                    'name' => $fileName,
                    'path' => $file,
                    'url' => $url,
                    'size' => $size,
                    'lastModified' => $lastModified,
                    'extension' => $ext,
                    'uploaded_by' => isset($uploaders[$file]) ? $uploaders[$file]->uploaded_by_name : null,
                    'uploaded_by_id' => isset($uploaders[$file]) ? $uploaders[$file]->uploaded_by : null,
                ];
            }

            $allFoldersList = [['name' => 'Thư Viện Video (gốc)', 'path' => '']];
            try {
                $scanDirs = function($parentPath) use (&$scanDirs, $disk, $basePath) {
                    $result = [];
                    $dirs = $disk->directories($parentPath);
                    foreach ($dirs as $d) {
                        $relative = $d;
                        if (strpos($d, $basePath . '/') === 0) {
                            $relative = substr($d, strlen($basePath . '/'));
                        }
                        if ($relative && $relative !== '.') {
                            $result[] = ['name' => $relative, 'path' => $relative];
                            $result = array_merge($result, $scanDirs($d));
                        }
                    }
                    return $result;
                };
                $allFoldersList = array_merge($allFoldersList, $scanDirs($basePath));
            } catch (\Exception $e) {
                foreach ($folders as $f) {
                    $allFoldersList[] = ['name' => $f['name'], 'path' => $f['path']];
                }
            }

            $breadcrumb = [['name' => 'Thư Viện Video', 'path' => '']];
            if ($subFolder) {
                $parts = explode('/', $subFolder);
                $built = '';
                foreach ($parts as $part) {
                    $built .= ($built ? '/' : '') . $part;
                    $breadcrumb[] = ['name' => $part, 'path' => $built];
                }
            }

            $user = Auth::user();
            $isAdmin = $user && ($user->hasRole('Admin') || $user->can('Admin'));
            $isSale = $user && $user->can('Sale');
            $permissions = [
                'can_upload' => $isAdmin || $isSale || $this->userCanAction($path, 'can_upload'),
                'can_rename' => $isAdmin || $this->userCanAction($path, 'can_rename'),
                'can_delete' => $isAdmin || $this->userCanAction($path, 'can_delete'),
            ];

            return response()->json([
                'success' => true,
                'folders' => $folders,
                'videos' => $videos,
                'breadcrumb' => $breadcrumb,
                'permissions' => $permissions,
                'allFolders' => $allFoldersList,
                'current_user_id' => $user ? $user->id : null,
                'is_admin' => $isAdmin,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi tải video: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Trang Album Kỉ Niệm
     */
    public function memoryGallery()
    {
        return view('main.media.memory_gallery');
    }

    /**
     * API: Get images from "Kỉ Niệm" folder (AJAX)
     */
    public function memoryGalleryApi(Request $request)
    {
        $basePath = 'Kỉ Niệm';
        $subFolder = $request->input('folder', '');
        $path = $subFolder ? $basePath . '/' . $subFolder : $basePath;

        try {
            $disk = Storage::disk('s3');

            if (!$disk->exists($basePath)) {
                $disk->put($basePath . '/.keep', '');
            }

            $directories = $disk->directories($path);
            $folders = [];
            foreach ($directories as $dir) {
                $dirName = str_replace($path . '/', '', $dir);
                if (strpos($dirName, '/') === false && $dirName !== '') {
                    $folderFiles = $disk->files($dir);
                    $thumb = null;
                    foreach ($folderFiles as $ff) {
                        $ext = strtolower(pathinfo($ff, PATHINFO_EXTENSION));
                        if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp'])) {
                            try {
                                $thumb = $disk->temporaryUrl($ff, now()->addMinutes(60));
                            } catch (\Exception $e) {
                                try { $thumb = $disk->url($ff); } catch (\Exception $e2) {}
                            }
                            break;
                        }
                    }
                    $folders[] = [
                        'name' => $dirName,
                        'path' => str_replace($basePath . '/', '', $dir),
                        'thumbnail' => $thumb,
                        'count' => count(array_filter($folderFiles, fn($f) => basename($f) !== '.keep')),
                    ];
                }
            }

            $allFiles = $disk->files($path);
            $images = [];
            $imageExts = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp', 'svg'];

            $filePaths = collect($allFiles)->filter(function ($f) use ($imageExts) {
                return in_array(strtolower(pathinfo($f, PATHINFO_EXTENSION)), $imageExts);
            })->values()->all();
            $uploaders = DB::table('media_uploads')
                ->whereIn('file_path', $filePaths)
                ->pluck('uploaded_by_name', 'file_path');

            foreach ($allFiles as $file) {
                $fileName = basename($file);
                $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                if (!in_array($ext, $imageExts)) continue;

                $url = null;
                try {
                    $url = $disk->temporaryUrl($file, now()->addMinutes(60));
                } catch (\Exception $e) {
                    try { $url = $disk->url($file); } catch (\Exception $e2) {}
                }

                $size = null;
                $lastModified = null;
                try {
                    $size = $disk->size($file);
                    $lastModified = $disk->lastModified($file);
                } catch (\Exception $e) {}

                $images[] = [
                    'name' => $fileName,
                    'path' => $file,
                    'url' => $url,
                    'size' => $size,
                    'lastModified' => $lastModified,
                    'extension' => $ext,
                    'uploaded_by' => $uploaders[$file] ?? null,
                ];
            }

            $allFoldersList = [['name' => 'Album Kỉ Niệm (gốc)', 'path' => '']];
            try {
                $scanDirs = function($parentPath) use (&$scanDirs, $disk, $basePath) {
                    $result = [];
                    $dirs = $disk->directories($parentPath);
                    foreach ($dirs as $d) {
                        $relative = $d;
                        if (strpos($d, $basePath . '/') === 0) {
                            $relative = substr($d, strlen($basePath . '/'));
                        }
                        if ($relative && $relative !== '.') {
                            $result[] = ['name' => $relative, 'path' => $relative];
                            $result = array_merge($result, $scanDirs($d));
                        }
                    }
                    return $result;
                };
                $allFoldersList = array_merge($allFoldersList, $scanDirs($basePath));
            } catch (\Exception $e) {
                foreach ($folders as $f) {
                    $allFoldersList[] = ['name' => $f['name'], 'path' => $f['path']];
                }
            }

            $breadcrumb = [['name' => 'Album Kỉ Niệm', 'path' => '']];
            if ($subFolder) {
                $parts = explode('/', $subFolder);
                $built = '';
                foreach ($parts as $part) {
                    $built .= ($built ? '/' : '') . $part;
                    $breadcrumb[] = ['name' => $part, 'path' => $built];
                }
            }

            $user = Auth::user();
            $isAdmin = $user && ($user->hasRole('Admin') || $user->can('Admin'));
            $permissions = [
                'can_upload' => $isAdmin || $this->userCanAction($path, 'can_upload'),
                'can_rename' => $isAdmin || $this->userCanAction($path, 'can_rename'),
                'can_delete' => $isAdmin || $this->userCanAction($path, 'can_delete'),
            ];

            return response()->json([
                'success' => true,
                'folders' => $folders,
                'images' => $images,
                'breadcrumb' => $breadcrumb,
                'permissions' => $permissions,
                'allFolders' => $allFoldersList,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi tải ảnh: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Trang MiDo Farm
     */
    public function farmGallery()
    {
        return view('main.media.farm_gallery');
    }

    /**
     * API: Get images & videos from "Farm Tôm" folder (AJAX)
     * Only Admin can upload/rename/delete/create
     */
    public function farmGalleryApi(Request $request)
    {
        $basePath = 'Farm Tôm';
        $subFolder = $request->input('folder', '');
        $path = $subFolder ? $basePath . '/' . $subFolder : $basePath;

        try {
            $disk = Storage::disk('s3');

            if (!$disk->exists($basePath)) {
                $disk->put($basePath . '/.keep', '');
            }

            $imageExts = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp', 'svg'];
            $videoExts = ['mp4', 'avi', 'mov', 'mkv', 'webm', 'flv', 'wmv', 'm4v'];
            $allMediaExts = array_merge($imageExts, $videoExts);

            $directories = $disk->directories($path);
            $folders = [];
            foreach ($directories as $dir) {
                $dirName = str_replace($path . '/', '', $dir);
                if (strpos($dirName, '/') === false && $dirName !== '') {
                    $folderFiles = $disk->files($dir);
                    $thumb = null;
                    foreach ($folderFiles as $ff) {
                        $ext = strtolower(pathinfo($ff, PATHINFO_EXTENSION));
                        if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp'])) {
                            try {
                                $thumb = $disk->temporaryUrl($ff, now()->addMinutes(60));
                            } catch (\Exception $e) {
                                try { $thumb = $disk->url($ff); } catch (\Exception $e2) {}
                            }
                            break;
                        }
                    }
                    $mediaCount = count(array_filter($folderFiles, function($f) use ($allMediaExts) {
                        return basename($f) !== '.keep' && in_array(strtolower(pathinfo($f, PATHINFO_EXTENSION)), $allMediaExts);
                    }));
                    $folders[] = [
                        'name' => $dirName,
                        'path' => str_replace($basePath . '/', '', $dir),
                        'thumbnail' => $thumb,
                        'count' => $mediaCount,
                    ];
                }
            }

            $allFiles = $disk->files($path);
            $images = [];
            $videos = [];

            // Collect all media file paths for uploader lookup
            $allMediaPaths = collect($allFiles)->filter(function ($f) use ($allMediaExts) {
                return in_array(strtolower(pathinfo($f, PATHINFO_EXTENSION)), $allMediaExts);
            })->values()->all();
            $uploaders = DB::table('media_uploads')
                ->whereIn('file_path', $allMediaPaths)
                ->pluck('uploaded_by_name', 'file_path');

            foreach ($allFiles as $file) {
                $fileName = basename($file);
                $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                $isImage = in_array($ext, $imageExts);
                $isVideo = in_array($ext, $videoExts);
                if (!$isImage && !$isVideo) continue;

                $url = null;
                try {
                    $url = $disk->temporaryUrl($file, now()->addMinutes(60));
                } catch (\Exception $e) {
                    try { $url = $disk->url($file); } catch (\Exception $e2) {}
                }

                $size = null;
                $lastModified = null;
                try {
                    $size = $disk->size($file);
                    $lastModified = $disk->lastModified($file);
                } catch (\Exception $e) {}

                $item = [
                    'name' => $fileName,
                    'path' => $file,
                    'url' => $url,
                    'size' => $size,
                    'lastModified' => $lastModified,
                    'extension' => $ext,
                    'uploaded_by' => $uploaders[$file] ?? null,
                ];

                if ($isImage) {
                    $images[] = $item;
                } else {
                    $videos[] = $item;
                }
            }

            $allFoldersList = [['name' => 'MiDo Farm (gốc)', 'path' => '']];
            try {
                $scanDirs = function($parentPath) use (&$scanDirs, $disk, $basePath) {
                    $result = [];
                    $dirs = $disk->directories($parentPath);
                    foreach ($dirs as $d) {
                        $relative = $d;
                        if (strpos($d, $basePath . '/') === 0) {
                            $relative = substr($d, strlen($basePath . '/'));
                        }
                        if ($relative && $relative !== '.') {
                            $result[] = ['name' => $relative, 'path' => $relative];
                            $result = array_merge($result, $scanDirs($d));
                        }
                    }
                    return $result;
                };
                $allFoldersList = array_merge($allFoldersList, $scanDirs($basePath));
            } catch (\Exception $e) {
                foreach ($folders as $f) {
                    $allFoldersList[] = ['name' => $f['name'], 'path' => $f['path']];
                }
            }

            $breadcrumb = [['name' => 'MiDo Farm', 'path' => '']];
            if ($subFolder) {
                $parts = explode('/', $subFolder);
                $built = '';
                foreach ($parts as $part) {
                    $built .= ($built ? '/' : '') . $part;
                    $breadcrumb[] = ['name' => $part, 'path' => $built];
                }
            }

            // Only Admin can modify
            $user = Auth::user();
            $isAdmin = $user && ($user->hasRole('Admin') || $user->can('Admin'));
            $permissions = [
                'can_upload' => $isAdmin,
                'can_rename' => $isAdmin,
                'can_delete' => $isAdmin,
            ];

            return response()->json([
                'success' => true,
                'folders' => $folders,
                'images' => $images,
                'videos' => $videos,
                'breadcrumb' => $breadcrumb,
                'permissions' => $permissions,
                'allFolders' => $allFoldersList,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi tải dữ liệu: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Trang MGS MORE
     */
    public function mgsGallery()
    {
        return view('main.media.mgs_gallery');
    }

    /**
     * API: Get images & videos from "Trại Giống" folder (AJAX)
     * Only Admin can upload/rename/delete/create
     */
    public function mgsGalleryApi(Request $request)
    {
        $basePath = 'Trại Giống';
        $subFolder = $request->input('folder', '');
        $path = $subFolder ? $basePath . '/' . $subFolder : $basePath;

        try {
            $disk = Storage::disk('s3');

            if (!$disk->exists($basePath)) {
                $disk->put($basePath . '/.keep', '');
            }

            $imageExts = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp', 'svg'];
            $videoExts = ['mp4', 'avi', 'mov', 'mkv', 'webm', 'flv', 'wmv', 'm4v'];
            $allMediaExts = array_merge($imageExts, $videoExts);

            $directories = $disk->directories($path);
            $folders = [];
            foreach ($directories as $dir) {
                $dirName = str_replace($path . '/', '', $dir);
                if (strpos($dirName, '/') === false && $dirName !== '') {
                    $folderFiles = $disk->files($dir);
                    $thumb = null;
                    foreach ($folderFiles as $ff) {
                        $ext = strtolower(pathinfo($ff, PATHINFO_EXTENSION));
                        if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp'])) {
                            try {
                                $thumb = $disk->temporaryUrl($ff, now()->addMinutes(60));
                            } catch (\Exception $e) {
                                try { $thumb = $disk->url($ff); } catch (\Exception $e2) {}
                            }
                            break;
                        }
                    }
                    $mediaCount = count(array_filter($folderFiles, function($f) use ($allMediaExts) {
                        return basename($f) !== '.keep' && in_array(strtolower(pathinfo($f, PATHINFO_EXTENSION)), $allMediaExts);
                    }));
                    $folders[] = [
                        'name' => $dirName,
                        'path' => str_replace($basePath . '/', '', $dir),
                        'thumbnail' => $thumb,
                        'count' => $mediaCount,
                    ];
                }
            }

            $allFiles = $disk->files($path);
            $images = [];
            $videos = [];

            $allMediaPaths = collect($allFiles)->filter(function ($f) use ($allMediaExts) {
                return in_array(strtolower(pathinfo($f, PATHINFO_EXTENSION)), $allMediaExts);
            })->values()->all();
            $uploaders = DB::table('media_uploads')
                ->whereIn('file_path', $allMediaPaths)
                ->pluck('uploaded_by_name', 'file_path');

            foreach ($allFiles as $file) {
                $fileName = basename($file);
                $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                $isImage = in_array($ext, $imageExts);
                $isVideo = in_array($ext, $videoExts);
                if (!$isImage && !$isVideo) continue;

                $url = null;
                try {
                    $url = $disk->temporaryUrl($file, now()->addMinutes(60));
                } catch (\Exception $e) {
                    try { $url = $disk->url($file); } catch (\Exception $e2) {}
                }

                $size = null;
                $lastModified = null;
                try {
                    $size = $disk->size($file);
                    $lastModified = $disk->lastModified($file);
                } catch (\Exception $e) {}

                $item = [
                    'name' => $fileName,
                    'path' => $file,
                    'url' => $url,
                    'size' => $size,
                    'lastModified' => $lastModified,
                    'extension' => $ext,
                    'uploaded_by' => $uploaders[$file] ?? null,
                ];

                if ($isImage) {
                    $images[] = $item;
                } else {
                    $videos[] = $item;
                }
            }

            $allFoldersList = [['name' => 'MGS MORE (gốc)', 'path' => '']];
            try {
                $scanDirs = function($parentPath) use (&$scanDirs, $disk, $basePath) {
                    $result = [];
                    $dirs = $disk->directories($parentPath);
                    foreach ($dirs as $d) {
                        $relative = $d;
                        if (strpos($d, $basePath . '/') === 0) {
                            $relative = substr($d, strlen($basePath . '/'));
                        }
                        if ($relative && $relative !== '.') {
                            $result[] = ['name' => $relative, 'path' => $relative];
                            $result = array_merge($result, $scanDirs($d));
                        }
                    }
                    return $result;
                };
                $allFoldersList = array_merge($allFoldersList, $scanDirs($basePath));
            } catch (\Exception $e) {
                foreach ($folders as $f) {
                    $allFoldersList[] = ['name' => $f['name'], 'path' => $f['path']];
                }
            }

            $breadcrumb = [['name' => 'MGS MORE', 'path' => '']];
            if ($subFolder) {
                $parts = explode('/', $subFolder);
                $built = '';
                foreach ($parts as $part) {
                    $built .= ($built ? '/' : '') . $part;
                    $breadcrumb[] = ['name' => $part, 'path' => $built];
                }
            }

            $user = Auth::user();
            $isAdmin = $user && ($user->hasRole('Admin') || $user->can('Admin'));
            $permissions = [
                'can_upload' => $isAdmin,
                'can_rename' => $isAdmin,
                'can_delete' => $isAdmin,
            ];

            return response()->json([
                'success' => true,
                'folders' => $folders,
                'images' => $images,
                'videos' => $videos,
                'breadcrumb' => $breadcrumb,
                'permissions' => $permissions,
                'allFolders' => $allFoldersList,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi tải dữ liệu: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Test S3 Connection (AJAX)
     */
    public function testConnection(Request $request)
    {
        $this->checkAdmin();

        try {
            $diskConfig = [
                'driver'                  => 's3',
                'key'                     => $request->input('access_key', env('AWS_ACCESS_KEY_ID')),
                'secret'                  => $request->input('secret_key', env('AWS_SECRET_ACCESS_KEY')),
                'region'                  => $request->input('region', env('AWS_DEFAULT_REGION', 'ap-southeast-1')),
                'bucket'                  => $request->input('bucket', env('AWS_BUCKET')),
                'endpoint'                => $request->input('endpoint', env('AWS_ENDPOINT')),
                'use_path_style_endpoint' => true,
                'throw'                   => true,
            ];

            config(['filesystems.disks.s3_test' => $diskConfig]);

            $disk = Storage::disk('s3_test');
            $files = $disk->files('');

            return response()->json([
                'success' => true,
                'message' => 'Kết nối thành công! Tìm thấy ' . count($files) . ' file trong bucket.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi kết nối: ' . $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Save S3 Config to .env
     */
    public function saveConfig(Request $request)
    {
        $this->checkAdmin();

        $request->validate([
            'endpoint'   => 'required|url',
            'access_key' => 'required|string',
            'secret_key' => 'required|string',
            'bucket'     => 'required|string',
            'region'     => 'required|string',
        ]);

        try {
            $envPath = base_path('.env');
            $envContent = file_get_contents($envPath);

            $updates = [
                'AWS_ACCESS_KEY_ID'            => $request->access_key,
                'AWS_SECRET_ACCESS_KEY'        => $request->secret_key,
                'AWS_DEFAULT_REGION'           => $request->region,
                'AWS_BUCKET'                   => $request->bucket,
                'AWS_ENDPOINT'                 => $request->endpoint,
                'AWS_USE_PATH_STYLE_ENDPOINT'  => 'true',
            ];

            foreach ($updates as $key => $value) {
                $pattern = "/^{$key}=.*/m";
                $replacement = "{$key}={$value}";

                if (preg_match($pattern, $envContent)) {
                    $envContent = preg_replace($pattern, $replacement, $envContent);
                } else {
                    $envContent .= "\n{$replacement}";
                }
            }

            file_put_contents($envPath, $envContent);

            return response()->json([
                'success' => true,
                'message' => 'Đã lưu cấu hình thành công! Vui lòng restart server để áp dụng.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi lưu cấu hình: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Browse files/folders in bucket (AJAX)
     */
    public function browse(Request $request)
    {
        $user = Auth::user();
        if (!$user) abort(403);

        $prefix = rtrim($request->input('path', ''), '/');
        if ($prefix) $prefix .= '/';

        $isAdmin = $user->hasRole('Admin') || $user->can('Admin');

        // Non-admin: check can_view permission
        if (!$isAdmin && $prefix) {
            $folderPath = rtrim($prefix, '/');
            if (!$this->userCanAction($folderPath, 'can_view')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn không có quyền xem thư mục này.',
                ], 403);
            }
        }

        try {
            $disk = Storage::disk('s3');

            $directories = $disk->directories($prefix ? rtrim($prefix, '/') : '');
            $allFiles = $disk->files($prefix ? rtrim($prefix, '/') : '');

            $folders = [];
            foreach ($directories as $dir) {
                $dirName = $prefix ? str_replace($prefix, '', $dir) : $dir;
                if (strpos($dirName, '/') === false && $dirName !== '') {
                    // Count permissions for this folder
                    $permCount = DB::table('media_folder_permissions')
                        ->where('folder_path', $dir)
                        ->count();

                    // Calculate folder size
                    $folderSize = 0;
                    try {
                        $folderFiles = $disk->allFiles($dir);
                        foreach ($folderFiles as $ff) {
                            try {
                                $folderSize += $disk->size($ff);
                            } catch (\Exception $e) {}
                        }
                    } catch (\Exception $e) {}

                    $folders[] = [
                        'name'  => $dirName,
                        'path'  => $dir,
                        'type'  => 'folder',
                        'size'  => $folderSize,
                        'lastModified' => null,
                        'permCount' => $permCount,
                    ];
                }
            }

            $files = [];
            foreach ($allFiles as $file) {
                $fileName = $prefix ? str_replace($prefix, '', $file) : $file;
                if (strpos($fileName, '/') === false && $fileName !== '') {
                    $size = null;
                    $lastModified = null;
                    try {
                        $size = $disk->size($file);
                        $lastModified = $disk->lastModified($file);
                    } catch (\Exception $e) {}

                    $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                    $isImage = in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'bmp', 'ico']);
                    $isVideo = in_array($ext, ['mp4', 'webm', 'avi', 'mov', 'mkv']);

                    $url = null;
                    try {
                        $url = $disk->temporaryUrl($file, now()->addMinutes(30));
                    } catch (\Exception $e) {
                        try {
                            $url = $disk->url($file);
                        } catch (\Exception $e2) {}
                    }

                    $files[] = [
                        'name'         => $fileName,
                        'path'         => $file,
                        'type'         => 'file',
                        'size'         => $size,
                        'lastModified' => $lastModified,
                        'extension'    => $ext,
                        'isImage'      => $isImage,
                        'isVideo'      => $isVideo,
                        'url'          => $url,
                    ];
                }
            }

            $breadcrumb = [['name' => 'Root', 'path' => '']];
            if ($prefix) {
                $parts = explode('/', rtrim($prefix, '/'));
                $currentPath = '';
                foreach ($parts as $part) {
                    $currentPath .= $part;
                    $breadcrumb[] = ['name' => $part, 'path' => $currentPath];
                    $currentPath .= '/';
                }
            }

            // Calculate total storage size when viewing root
            $totalStorageSize = null;
            if (!$prefix) {
                $totalStorageSize = 0;
                foreach ($folders as $f) {
                    $totalStorageSize += $f['size'] ?? 0;
                }
                foreach ($files as $f) {
                    $totalStorageSize += $f['size'] ?? 0;
                }
            }

            return response()->json([
                'success'    => true,
                'folders'    => $folders,
                'files'      => $files,
                'breadcrumb' => $breadcrumb,
                'path'       => $prefix,
                'totalStorageSize' => $totalStorageSize,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi duyệt file: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Upload file to S3
     */
    public function upload(Request $request)
    {
        $user = Auth::user();
        $path = rtrim($request->input('path', ''), '/');

        // Check permission
        if (!$this->userCanAction($path ?: '', 'can_upload')) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền upload vào thư mục này.',
            ], 403);
        }

        $request->validate([
            'files'  => 'required',
            'files.*' => 'file|max:102400',
        ]);

        $uploaded = [];

        try {
            $disk = Storage::disk('s3');

            foreach ($request->file('files') as $file) {
                $fileName = $file->getClientOriginalName();
                $fullPath = $path ? $path . '/' . $fileName : $fileName;

                $disk->putFileAs(
                    $path ?: '',
                    $file,
                    $fileName,
                    'public'
                );

                $uploaded[] = $fullPath;

                // Track uploader
                DB::table('media_uploads')->insert([
                    'file_path' => $fullPath,
                    'uploaded_by' => $user->id,
                    'uploaded_by_name' => $user->name,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Đã upload ' . count($uploaded) . ' file thành công.',
                'files'   => $uploaded,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi upload: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete file from S3
     */
    public function delete(Request $request)
    {
        $path = $request->input('path');
        $type = $request->input('type', 'file');

        if (!$path) {
            return response()->json(['success' => false, 'message' => 'Đường dẫn không hợp lệ.'], 400);
        }

        // Determine folder path for permission check
        $folderPath = $type === 'folder' ? $path : dirname($path);
        if ($folderPath === '.') $folderPath = '';

        if (!$this->userCanAction($folderPath, 'can_delete')) {
            // For files: allow if the user is the uploader
            if ($type === 'file') {
                $user = auth()->user();
                $uploadRecord = DB::table('media_uploads')->where('file_path', $path)->first();
                if (!$user || !$uploadRecord || $uploadRecord->uploaded_by != $user->id) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Bạn không có quyền xóa file này.',
                    ], 403);
                }
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn không có quyền xóa trong thư mục này.',
                ], 403);
            }
        }

        try {
            $disk = Storage::disk('s3');

            if ($type === 'folder') {
                // Delete all files inside first (S3-compatible fix)
                $allFiles = $disk->allFiles($path);
                if (!empty($allFiles)) {
                    $disk->delete($allFiles);
                }
                // Then delete directory
                $disk->deleteDirectory($path);
                // Clean up media_uploads records
                DB::table('media_uploads')->where('file_path', 'like', $path . '/%')->delete();
                // Also remove permissions for this folder
                DB::table('media_folder_permissions')->where('folder_path', $path)->delete();
                $message = 'Đã xóa thư mục thành công.';
            } else {
                $disk->delete($path);
                $message = 'Đã xóa file thành công.';
            }

            return response()->json([
                'success' => true,
                'message' => $message,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi xóa: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Create folder on S3
     */
    public function createFolder(Request $request)
    {
        $currentPath = rtrim($request->input('current_path', $request->input('path', '')), '/');

        // Check permission: can_upload on current folder
        if (!$this->userCanAction($currentPath ?: '', 'can_upload')) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền tạo thư mục tại đây.',
            ], 403);
        }

        $request->validate([
            'folder_name' => 'required|string|max:100',
        ]);

        $folderName = trim($request->folder_name);
        $fullPath = $currentPath ? $currentPath . '/' . $folderName : $folderName;

        try {
            $disk = Storage::disk('s3');
            $disk->put($fullPath . '/.keep', '');

            return response()->json([
                'success' => true,
                'message' => 'Đã tạo thư mục "' . $folderName . '" thành công.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi tạo thư mục: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Move file or folder on S3
     */
    public function moveFile(Request $request)
    {
        $sourcePath = $request->input('source_path');
        $destFolder = $request->input('dest_folder', '');
        $type = $request->input('type', 'file');

        // Auto-detect base path from source path
        $basePath = 'Thư Viện Ảnh';
        $knownBases = ['Thư Viện Video', 'Kỉ Niệm', 'Thư Viện Ảnh'];
        foreach ($knownBases as $base) {
            if (strpos($sourcePath, $base . '/') === 0 || $sourcePath === $base) {
                $basePath = $base;
                break;
            }
        }

        if (!$sourcePath) {
            return response()->json(['success' => false, 'message' => 'Đường dẫn nguồn không hợp lệ.'], 400);
        }

        // Build full destination path
        $fullDestFolder = $destFolder ? $basePath . '/' . $destFolder : $basePath;

        // Check permission on destination
        if (!$this->userCanAction($fullDestFolder, 'can_upload')) {
            return response()->json(['success' => false, 'message' => 'Bạn không có quyền di chuyển tới thư mục này.'], 403);
        }

        try {
            $disk = Storage::disk('s3');
            $itemName = basename($sourcePath);
            $destPath = $fullDestFolder . '/' . $itemName;

            if ($type === 'folder') {
                // Move folder: copy all contents
                $files = $disk->allFiles($sourcePath);
                foreach ($files as $file) {
                    $relative = str_replace($sourcePath . '/', '', $file);
                    $newPath = $destPath . '/' . $relative;
                    $disk->copy($file, $newPath);
                }
                // Ensure folder marker exists
                if (empty($files)) {
                    $disk->put($destPath . '/.keep', '');
                }
                // Delete old folder
                $disk->deleteDirectory($sourcePath);

                // Update media_uploads records
                DB::table('media_uploads')
                    ->where('file_path', 'like', $sourcePath . '/%')
                    ->get()
                    ->each(function ($record) use ($sourcePath, $destPath) {
                        $newPath = str_replace($sourcePath, $destPath, $record->file_path);
                        DB::table('media_uploads')
                            ->where('id', $record->id)
                            ->update(['file_path' => $newPath, 'updated_at' => now()]);
                    });
            } else {
                // Move file
                $disk->copy($sourcePath, $destPath);
                $disk->delete($sourcePath);

                // Update media_uploads record
                DB::table('media_uploads')
                    ->where('file_path', $sourcePath)
                    ->update(['file_path' => $destPath, 'updated_at' => now()]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Đã di chuyển thành công.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi di chuyển: ' . $e->getMessage(),
            ], 500);
        }
    }

    // ===========================
    // FOLDER PERMISSIONS
    // ===========================

    /**
     * Get permissions for a folder (AJAX)
     */
    public function getPermissions(Request $request)
    {
        $this->checkAdmin();

        $folderPath = $request->input('folder_path', '');

        $permissions = DB::table('media_folder_permissions as p')
            ->leftJoin('users as u', function($join) {
                $join->on('u.id', '=', 'p.user_id')
                     ->where('p.permission_type', '=', 'user');
            })
            ->leftJoin('phongban as pb', function($join) {
                $join->on('pb.MaPB', '=', 'p.department_id')
                     ->where('p.permission_type', '=', 'department');
            })
            ->where('p.folder_path', $folderPath)
            ->select(
                'p.*',
                'u.name as user_name',
                'pb.TenPB as department_name'
            )
            ->orderBy('p.permission_type')
            ->orderBy('p.created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'permissions' => $permissions,
        ]);
    }

    /**
     * Store a new folder permission
     */
    public function storePermission(Request $request)
    {
        $this->checkAdmin();

        $request->validate([
            'folder_path'     => 'required|string',
            'permission_type' => 'required|in:user,department',
            'can_upload'      => 'required|boolean',
            'can_delete'      => 'required|boolean',
        ]);

        $data = [
            'folder_path'     => $request->folder_path,
            'permission_type' => $request->permission_type,
            'can_view'        => $request->can_view ?? true,
            'can_upload'      => $request->can_upload,
            'can_rename'      => $request->can_rename ?? false,
            'can_delete'      => $request->can_delete,
            'created_at'      => now(),
            'updated_at'      => now(),
        ];

        if ($request->permission_type === 'user') {
            $request->validate(['user_id' => 'required|integer']);
            $data['user_id'] = $request->user_id;

            // Check duplicate
            $exists = DB::table('media_folder_permissions')
                ->where('folder_path', $request->folder_path)
                ->where('permission_type', 'user')
                ->where('user_id', $request->user_id)
                ->first();
            if ($exists) {
                DB::table('media_folder_permissions')->where('id', $exists->id)->update([
                    'can_view'   => $request->can_view ?? true,
                    'can_upload' => $request->can_upload,
                    'can_rename' => $request->can_rename ?? false,
                    'can_delete' => $request->can_delete,
                    'updated_at' => now(),
                ]);
                return response()->json(['success' => true, 'message' => 'Đã cập nhật quyền.']);
            }
        } else {
            $request->validate(['department_id' => 'required|integer']);
            $data['department_id'] = $request->department_id;

            $exists = DB::table('media_folder_permissions')
                ->where('folder_path', $request->folder_path)
                ->where('permission_type', 'department')
                ->where('department_id', $request->department_id)
                ->first();
            if ($exists) {
                DB::table('media_folder_permissions')->where('id', $exists->id)->update([
                    'can_view'   => $request->can_view ?? true,
                    'can_upload' => $request->can_upload,
                    'can_rename' => $request->can_rename ?? false,
                    'can_delete' => $request->can_delete,
                    'updated_at' => now(),
                ]);
                return response()->json(['success' => true, 'message' => 'Đã cập nhật quyền.']);
            }
        }

        DB::table('media_folder_permissions')->insert($data);

        return response()->json([
            'success' => true,
            'message' => 'Đã thêm quyền thành công.',
        ]);
    }

    /**
     * Rename a file or folder on S3
     */
    public function rename(Request $request)
    {
        $request->validate([
            'old_path'  => 'required|string',
            'new_name'  => 'required|string|max:255',
            'type'      => 'required|in:file,folder',
        ]);

        $oldPath = $request->old_path;
        $type = $request->type;

        // Determine folder for permission check
        $folderPath = dirname($oldPath);
        if ($folderPath === '.') $folderPath = '';

        if (!$this->userCanAction($folderPath, 'can_rename')) {
            // For files: allow if the user is the uploader
            if ($type === 'file') {
                $user = auth()->user();
                $uploadRecord = DB::table('media_uploads')->where('file_path', $oldPath)->first();
                if (!$user || !$uploadRecord || $uploadRecord->uploaded_by != $user->id) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Bạn không có quyền đổi tên file này.',
                    ], 403);
                }
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn không có quyền đổi tên trong thư mục này.',
                ], 403);
            }
        }

        try {
            $disk = Storage::disk('s3');
            $newName = trim($request->new_name);
            $parentDir = dirname($oldPath);
            if ($parentDir === '.') $parentDir = '';

            if ($type === 'file') {
                // Preserve original extension if user didn't include it
                $oldExt = pathinfo($oldPath, PATHINFO_EXTENSION);
                $newExt = pathinfo($newName, PATHINFO_EXTENSION);
                if ($oldExt && (!$newExt || strtolower($newExt) !== strtolower($oldExt))) {
                    $newName = $newName . '.' . $oldExt;
                }
            }

            $newPath = $parentDir ? $parentDir . '/' . $newName : $newName;

            // Skip if same path
            if ($oldPath === $newPath) {
                return response()->json(['success' => true, 'message' => 'Tên không thay đổi.']);
            }

            if ($type === 'folder') {
                // For folders: copy all contents then delete old
                $files = $disk->allFiles($oldPath);
                foreach ($files as $file) {
                    $relativePath = substr($file, strlen($oldPath));
                    $disk->copy($file, $newPath . $relativePath);
                }
                // Explicitly delete all files first (S3-compatible fix)
                if (!empty($files)) {
                    $disk->delete($files);
                }
                $disk->deleteDirectory($oldPath);
                // Update permissions
                DB::table('media_folder_permissions')
                    ->where('folder_path', $oldPath)
                    ->update(['folder_path' => $newPath, 'updated_at' => now()]);
                // Update media_uploads records
                DB::table('media_uploads')
                    ->where('file_path', 'like', $oldPath . '/%')
                    ->get()
                    ->each(function ($record) use ($oldPath, $newPath) {
                        $newFilePath = str_replace($oldPath, $newPath, $record->file_path);
                        DB::table('media_uploads')
                            ->where('id', $record->id)
                            ->update(['file_path' => $newFilePath, 'updated_at' => now()]);
                    });
            } else {
                // For files: copy then verify before deleting old
                $disk->copy($oldPath, $newPath);
                if ($disk->exists($newPath)) {
                    $disk->delete($oldPath);
                    // Update media_uploads record
                    DB::table('media_uploads')
                        ->where('file_path', $oldPath)
                        ->update(['file_path' => $newPath, 'updated_at' => now()]);
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => 'Không thể tạo file mới, đã hủy đổi tên.',
                    ], 500);
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Đã đổi tên thành công.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi đổi tên: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Upload image from Rich Text Editor (TinyMCE) to S3
     * Images are stored in "File Tam/editor/{Y-m}/" folder
     */
    public function editorUpload(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $request->validate([
            'file' => 'required|image|max:10240', // Max 10MB
        ]);

        try {
            $disk = Storage::disk('s3');
            $file = $request->file('file');
            
            // Build path: File Tam/editor/2026-02/
            $monthFolder = now()->format('Y-m');
            $path = 'File Tam/editor/' . $monthFolder;
            
            // Generate unique filename to avoid conflicts
            $extension = $file->getClientOriginalExtension();
            $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $fileName = $originalName . '_' . uniqid() . '.' . $extension;
            
            // Upload to S3
            $disk->putFileAs($path, $file, $fileName, 'public');
            
            $fullPath = $path . '/' . $fileName;
            
            // Get the public URL
            $url = null;
            try {
                $url = $disk->temporaryUrl($fullPath, now()->addDays(7));
            } catch (\Exception $e) {
                try {
                    $url = $disk->url($fullPath);
                } catch (\Exception $e2) {}
            }

            // Return in TinyMCE expected format
            return response()->json([
                'location' => $url,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Upload thất bại: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete a folder permission
     */
    public function deletePermission($id)
    {
        $this->checkAdmin();

        DB::table('media_folder_permissions')->where('id', $id)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Đã xóa quyền thành công.',
        ]);
    }
}
