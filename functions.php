<?php

/**
 * Save the image on the server.
 *
 * @param string $base64_img
 * @param string $title
 * @return int|WP_Error The attachment ID on success. The value 0 or WP_Error on failure.
 */
function save_image($base64_img, $title)
{
	// Upload dir.
	$upload_dir  = wp_upload_dir();
	$upload_path = str_replace('/', DIRECTORY_SEPARATOR, $upload_dir['path']) . DIRECTORY_SEPARATOR;

	$img             = str_replace('data:image/jpeg;base64,', '', $base64_img);
	$img             = str_replace(' ', '+', $img);
	$decoded         = base64_decode($img);
	$filename        = $title . '.jpeg';
	$file_type       = 'image/jpeg';
	$hashed_filename = md5($filename . microtime()) . '_' . $filename;

	// Save the image in the uploads directory.
	$upload_file = file_put_contents($upload_path . $hashed_filename, $decoded);
	if (false == $upload_file) {
		return false;
	}

	$attachment = array(
		'post_mime_type' => $file_type,
		'post_title'     => preg_replace('/\.[^.]+$/', '', basename($hashed_filename)),
		'post_content'   => '',
		'post_status'    => 'inherit',
		'guid'           => $upload_dir['url'] . '/' . basename($hashed_filename)
	);

	$attach_id = wp_insert_attachment($attachment, $upload_dir['path'] . '/' . $hashed_filename);

	return $attach_id;
}
