<?php
    // Exercise 2: File Upload Gallery System
    
    class ImageGallery {
        private string $uploadDir;
        private string $thumbDir;
        private array $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        private int $maxFileSize = 5242880; // 5MB
        private int $maxWidth = 1920;
        private int $maxHeight = 1080;
        private int $thumbWidth = 200;
        private int $thumbHeight = 200;
        private array $errors = [];
        private array $images = [];
        
        public function __construct() {
            $this->uploadDir = 'uploads/';
            $this->thumbDir = 'uploads/thumbnails/';
            
            // Create directories if they don't exist
            if (!is_dir($this->uploadDir)) {
                mkdir($this->uploadDir, 0755, true);
            }
            if (!is_dir($this->thumbDir)) {
                mkdir($this->thumbDir, 0755, true);
            }
            
            // Load existing images
            $this->loadImages();
            
            // Handle upload
            if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["image"])) {
                $this->handleUpload();
            }
            
            // Handle deletion
            if (isset($_GET["delete"])) {
                $this->deleteImage($_GET["delete"]);
            }
        }
        
        private function loadImages(): void {
            $imageFiles = glob($this->uploadDir . '*.{jpg,jpeg,png,gif}', GLOB_BRACE);
            
            foreach ($imageFiles as $file) {
                $filename = basename($file);
                $thumbPath = $this->thumbDir . $filename;
                
                $this->images[] = [
                    'filename' => $filename,
                    'path' => $file,
                    'thumb_path' => $thumbPath,
                    'size' => filesize($file),
                    'uploaded' => filemtime($file),
                    'dimensions' => $this->getImageDimensions($file)
                ];
            }
            
            // Sort by upload date (newest first)
            usort($this->images, function($a, $b) {
                return $b['uploaded'] <=> $a['uploaded'];
            });
        }
        
        private function getImageDimensions(string $filepath): array {
            if (!file_exists($filepath)) {
                return ['width' => 0, 'height' => 0];
            }
            
            $info = getimagesize($filepath);
            if ($info) {
                return ['width' => $info[0], 'height' => $info[1]];
            }
            
            return ['width' => 0, 'height' => 0];
        }
        
        private function handleUpload(): void {
            $file = $_FILES["image"];
            
            // Check upload error
            if ($file["error"] !== UPLOAD_ERR_OK) {
                $this->errors[] = $this->getUploadErrorMessage($file["error"]);
                return;
            }
            
            // Validate file type
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = finfo_file($finfo, $file["tmp_name"]);
            finfo_close($finfo);
            
            if (!in_array($mimeType, $this->allowedTypes)) {
                $this->errors[] = "File type not allowed. Only JPG, PNG, and GIF images are accepted.";
                return;
            }
            
            // Validate file size
            if ($file["size"] > $this->maxFileSize) {
                $this->errors[] = "File too large. Maximum size is " . $this->formatFileSize($this->maxFileSize);
                return;
            }
            
            // Validate image
            if (!$this->isValidImage($file["tmp_name"])) {
                $this->errors[] = "Invalid image file.";
                return;
            }
            
            // Generate unique filename
            $extension = pathinfo($file["name"], PATHINFO_EXTENSION);
            $filename = uniqid('img_', true) . '.' . strtolower($extension);
            $uploadPath = $this->uploadDir . $filename;
            $thumbPath = $this->thumbDir . $filename;
            
            // Process and save image
            if ($this->processAndSaveImage($file["tmp_name"], $uploadPath, $thumbPath, $mimeType)) {
                // Reload images
                $this->loadImages();
                header("Location: " . $_SERVER["PHP_SELF"] . "?uploaded=1");
                exit;
            }
        }
        
        private function isValidImage(string $filepath): bool {
            $info = @getimagesize($filepath);
            return $info !== false;
        }
        
        private function processAndSaveImage(string $source, string $dest, string $thumbDest, string $mimeType): bool {
            // Create image resource based on MIME type
            $image = $this->createImageResource($source, $mimeType);
            if (!$image) {
                $this->errors[] = "Failed to process image.";
                return false;
            }
            
            // Get original dimensions
            $width = imagesx($image);
            $height = imagesy($image);
            
            // Resize main image if too large
            if ($width > $this->maxWidth || $height > $this->maxHeight) {
                $resized = $this->resizeImage($image, $width, $height, $this->maxWidth, $this->maxHeight);
                imagedestroy($image);
                $image = $resized;
            }
            
            // Create thumbnail
            $thumbnail = $this->createThumbnail($image);
            
            // Save images
            $saved = $this->saveImage($image, $dest, $mimeType);
            $thumbSaved = $this->saveImage($thumbnail, $thumbDest, $mimeType);
            
            // Clean up
            imagedestroy($image);
            imagedestroy($thumbnail);
            
            if (!$saved || !$thumbSaved) {
                $this->errors[] = "Failed to save image files.";
                return false;
            }
            
            return true;
        }
        
        private function createImageResource(string $filepath, string $mimeType) {
            switch ($mimeType) {
                case 'image/jpeg':
                    return imagecreatefromjpeg($filepath);
                case 'image/png':
                    return imagecreatefrompng($filepath);
                case 'image/gif':
                    return imagecreatefromgif($filepath);
                default:
                    return null;
            }
        }
        
        private function resizeImage($image, int $origWidth, int $origHeight, int $maxWidth, int $maxHeight) {
            $ratio = min($maxWidth / $origWidth, $maxHeight / $origHeight);
            $newWidth = (int)($origWidth * $ratio);
            $newHeight = (int)($origHeight * $ratio);
            
            $resized = imagecreatetruecolor($newWidth, $newHeight);
            
            // Preserve transparency for PNG
            if ($this->isPNG($image)) {
                imagealphablending($resized, false);
                imagesavealpha($resized, true);
                $transparent = imagecolorallocatealpha($resized, 255, 255, 255, 127);
                imagefilledrectangle($resized, 0, 0, $newWidth, $newHeight, $transparent);
            }
            
            imagecopyresampled($resized, $image, 0, 0, 0, 0, $newWidth, $newHeight, $origWidth, $origHeight);
            
            return $resized;
        }
        
        private function createThumbnail($image) {
            $width = imagesx($image);
            $height = imagesy($image);
            
            // Calculate thumbnail dimensions (crop to fit)
            $ratio = max($this->thumbWidth / $width, $this->thumbHeight / $height);
            $cropWidth = (int)($this->thumbWidth / $ratio);
            $cropHeight = (int)($this->thumbHeight / $ratio);
            
            $cropX = (int)(($width - $cropWidth) / 2);
            $cropY = (int)(($height - $cropHeight) / 2);
            
            $thumbnail = imagecreatetruecolor($this->thumbWidth, $this->thumbHeight);
            
            // Preserve transparency for PNG
            if ($this->isPNG($image)) {
                imagealphablending($thumbnail, false);
                imagesavealpha($thumbnail, true);
                $transparent = imagecolorallocatealpha($thumbnail, 255, 255, 255, 127);
                imagefilledrectangle($thumbnail, 0, 0, $this->thumbWidth, $this->thumbHeight, $transparent);
            }
            
            imagecopyresampled($thumbnail, $image, 0, 0, $cropX, $cropY, 
                              $this->thumbWidth, $this->thumbHeight, $cropWidth, $cropHeight);
            
            return $thumbnail;
        }
        
        private function isPNG($image): bool {
            return imageistruecolor($image) && (imagecolortransparent($image) >= 0 || imagealphablending($image));
        }
        
        private function saveImage($image, string $filepath, string $mimeType): bool {
            switch ($mimeType) {
                case 'image/jpeg':
                    return imagejpeg($image, $filepath, 90);
                case 'image/png':
                    return imagepng($image, $filepath, 9);
                case 'image/gif':
                    return imagegif($image, $filepath);
                default:
                    return false;
            }
        }
        
        private function deleteImage(string $filename): void {
            $filepath = $this->uploadDir . $filename;
            $thumbPath = $this->thumbDir . $filename;
            
            if (file_exists($filepath)) {
                unlink($filepath);
            }
            if (file_exists($thumbPath)) {
                unlink($thumbPath);
            }
            
            $this->loadImages();
            header("Location: " . $_SERVER["PHP_SELF"] . "?deleted=1");
            exit;
        }
        
        private function getUploadErrorMessage(int $errorCode): string {
            return match($errorCode) {
                UPLOAD_ERR_INI_SIZE => "File exceeds upload_max_filesize directive",
                UPLOAD_ERR_FORM_SIZE => "File exceeds MAX_FILE_SIZE directive",
                UPLOAD_ERR_PARTIAL => "File was only partially uploaded",
                UPLOAD_ERR_NO_FILE => "No file was uploaded",
                UPLOAD_ERR_NO_TMP_DIR => "Missing temporary folder",
                UPLOAD_ERR_CANT_WRITE => "Failed to write file to disk",
                UPLOAD_ERR_EXTENSION => "File upload stopped by extension",
                default => "Unknown upload error"
            };
        }
        
        private function formatFileSize(int $bytes): string {
            $units = ['B', 'KB', 'MB', 'GB'];
            $bytes = max($bytes, 0);
            $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
            $pow = min($pow, count($units) - 1);
            $bytes /= (1 << (10 * $pow));
            return round($bytes, 2) . ' ' . $units[$pow];
        }
        
        public function render(): string {
            $html = "<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Image Gallery</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 1200px; margin: 0 auto; padding: 20px; background: #f5f5f5; }
        .header { background: white; padding: 20px; border-radius: 8px; margin-bottom: 20px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .upload-section { background: white; padding: 20px; border-radius: 8px; margin-bottom: 20px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .gallery { display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 20px; }
        .image-card { background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 4px rgba(0,0,0,0.1); transition: transform 0.2s; }
        .image-card:hover { transform: translateY(-2px); }
        .image-container { position: relative; padding-top: 75%; overflow: hidden; }
        .image-container img { position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover; }
        .image-info { padding: 15px; }
        .image-info h3 { margin: 0 0 10px 0; font-size: 16px; }
        .image-info p { margin: 5px 0; font-size: 14px; color: #666; }
        .delete-btn { background: #dc3545; color: white; border: none; padding: 8px 12px; border-radius: 4px; cursor: pointer; font-size: 12px; }
        .delete-btn:hover { background: #c82333; }
        .upload-form { display: flex; flex-direction: column; gap: 15px; }
        .file-input { border: 2px dashed #ddd; padding: 20px; text-align: center; border-radius: 4px; cursor: pointer; }
        .file-input:hover { border-color: #007bff; }
        .file-input input { display: none; }
        .upload-btn { background: #007bff; color: white; padding: 12px 24px; border: none; border-radius: 4px; cursor: pointer; font-size: 16px; }
        .upload-btn:hover { background: #0056b3; }
        .error { background: #f8d7da; color: #721c24; padding: 10px; border-radius: 4px; margin-bottom: 15px; }
        .success { background: #d4edda; color: #155724; padding: 10px; border-radius: 4px; margin-bottom: 15px; }
        .empty-state { text-align: center; padding: 40px; color: #666; }
        .stats { display: flex; gap: 20px; margin-bottom: 20px; }
        .stat { background: white; padding: 15px; border-radius: 4px; text-align: center; flex: 1; }
        .stat-number { font-size: 24px; font-weight: bold; color: #007bff; }
        .stat-label { font-size: 14px; color: #666; }
    </style>
</head>
<body>
    <div class='header'>
        <h1>Image Gallery</h1>
        <p>Upload and manage your images</p>
    </div>";
            
            // Show messages
            if (isset($_GET["uploaded"])) {
                $html .= "<div class='success'>Image uploaded successfully!</div>";
            }
            if (isset($_GET["deleted"])) {
                $html .= "<div class='success'>Image deleted successfully!</div>";
            }
            
            if (!empty($this->errors)) {
                $html .= "<div class='error'><strong>Errors:</strong><ul>";
                foreach ($this->errors as $error) {
                    $html .= "<li>$error</li>";
                }
                $html .= "</ul></div>";
            }
            
            // Statistics
            $totalSize = array_sum(array_column($this->images, 'size'));
            $html .= "<div class='stats'>
                <div class='stat'>
                    <div class='stat-number'>" . count($this->images) . "</div>
                    <div class='stat-label'>Total Images</div>
                </div>
                <div class='stat'>
                    <div class='stat-number'>" . $this->formatFileSize($totalSize) . "</div>
                    <div class='stat-label'>Total Size</div>
                </div>
                <div class='stat'>
                    <div class='stat-number'>" . $this->formatFileSize($this->maxFileSize) . "</div>
                    <div class='stat-label'>Max File Size</div>
                </div>
            </div>";
            
            // Upload form
            $html .= "<div class='upload-section'>
                <h2>Upload New Image</h2>
                <form method='post' enctype='multipart/form-data' class='upload-form'>
                    <div class='file-input'>
                        <input type='file' name='image' id='image' accept='image/*' required>
                        <label for='image'>
                            <strong>Click to choose an image</strong><br>
                            <small>Supported formats: JPG, PNG, GIF (Max: " . $this->formatFileSize($this->maxFileSize) . ")</small>
                        </label>
                    </div>
                    <button type='submit' class='upload-btn'>Upload Image</button>
                </form>
            </div>";
            
            // Gallery
            $html .= "<div class='gallery'>";
            
            if (empty($this->images)) {
                $html .= "<div class='empty-state'>
                    <h3>No images uploaded yet</h3>
                    <p>Upload your first image to get started!</p>
                </div>";
            } else {
                foreach ($this->images as $image) {
                    $html .= "<div class='image-card'>
                        <div class='image-container'>
                            <img src='{$image['thumb_path']}' alt='Image' loading='lazy'>
                        </div>
                        <div class='image-info'>
                            <h3>" . htmlspecialchars($image['filename']) . "</h3>
                            <p>Size: " . $this->formatFileSize($image['size']) . "</p>
                            <p>Dimensions: {$image['dimensions']['width']} × {$image['dimensions']['height']}</p>
                            <p>Uploaded: " . date('M j, Y', $image['uploaded']) . "</p>
                            <button class='delete-btn' onclick=\"if(confirm('Delete this image?')) window.location.href='?delete=" . urlencode($image['filename']) . "'\">Delete</button>
                        </div>
                    </div>";
                }
            }
            
            $html .= "</div>
    <script>
        // Update file input label when file is selected
        document.getElementById('image').addEventListener('change', function(e) {
            const label = this.nextElementSibling;
            const fileName = e.target.files[0]?.name || 'Click to choose an image';
            label.innerHTML = '<strong>' + fileName + '</strong><br><small>Supported formats: JPG, PNG, GIF (Max: " . $this->formatFileSize($this->maxFileSize) . ")</small>';
        });
    </script>
</body>
</html>";
            
            return $html;
        }
    }
    
    // Display the gallery
    $gallery = new ImageGallery();
    echo $gallery->render();
?>
