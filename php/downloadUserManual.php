<?php
if (isset($_REQUEST["File"])) {
	// Get parameter and decode URL-encoded string
	$file = urldecode($_REQUEST["File"]);

	// Test whether the file name contains illegal characters
	if (preg_match('/^[^.][-a-z0-9_.]+[a-z]$/i', $file)) {
		$filepath = "../assets/User_Manual/" . $file;

		// Process download
		if (file_exists($filepath)) {
			header('Content-Description: File Transfer');
			header('Content-Type: application/octet-stream');
			header('Content-Disposition: attachment; filename="' . basename($filepath) . '"');
			header('Expires: 0');
			header('Cache-Control: must-revalidate');
			header('Pragma: public');
			header('Content-Length: ' . filesize($filepath));

			// Flush system output buffer
			flush();
			readfile($filepath);
			die();
		} else {
			http_response_code(404);
			die();
		}
	} else {
		die("Download cannot be processed!!!");
	}
}

?>
