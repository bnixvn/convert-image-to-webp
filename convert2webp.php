<?php
// thêm đoạn bên dưới vào functions.php của theme đang dùng
add_filter('wp_handle_upload', 'chuc_nang_convert_to_webp');
function chuc_nang_convert_to_webp($upload) {
    $image_path = $upload['file'];

    // cài đặt mức độ nén WebP.
    $compression_quality = 20; // Mức độ nén mặc định

    $supported_mime_types = array(
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
    );

    $image_size = getimagesize($image_path);

    if ($image_info !== false && array_key_exists($image_size['mime'], $supported_mime_types)) {
        // Sử dụng hàm wp_get_image_editor để chỉnh kích thước ảnh
        $editor = wp_get_image_editor($image_path);

        if (!is_wp_error($editor)) {
            // Lấy kích thước của ảnh gốc
            $original_size = $editor->get_size();
			
			$editor->resize($original_size['width'], $original_size['height'], true);
			
            //$editor->set_quality($compression_quality);

            // Lưu lại ảnh với mức độ nén mới
            $editor->save($image_path);

            // Tạo ảnh WebP từ ảnh đã chỉnh sửa
            $image = imagecreatefromstring(file_get_contents($image_path));

            if ($image) {
                $webp_path = preg_replace('/\.(jpg|jpeg|png)$/', '.webp', $image_path);
                imagewebp($image, $webp_path, $compression_quality);
                $upload['file'] = $webp_path;
                $upload['type'] = 'image/webp';
            }
        }
    }

    return $upload;
}
