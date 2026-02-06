<?php

class BSKPDFM_Security_Validator {
    
    private $allowed_types = array(
        'svg' => array(
            'mimes' => array('image/svg+xml', 'text/xml', 'application/xml', 'text/plain'),
            'extensions' => array('svg', 'svgz'),
            'check_content' => true
        ),
        'pdf' => array(
            'mimes' => array('application/pdf'),
            'extensions' => array('pdf'),
            'check_content' => true
        ),
        'png' => array(
            'mimes' => array('image/png'),
            'extensions' => array('png'),
            'check_content' => true
        ),
        'jpeg' => array(
            'mimes' => array('image/jpeg', 'image/jpg'),
            'extensions' => array('jpg', 'jpeg'),
            'check_content' => true
        ),
        'gif' => array(
            'mimes' => array('image/gif'),
            'extensions' => array('gif'),
            'check_content' => true
        )
    );
    
    /**
     * Validate uploaded file
     */
    public function bsk_dd_validate_uploaded_file($file_path, $original_filename) {

        $return_array = array( 'success' => false, 'message' => 'Not a pdf, png, jpeg, jpg, gif, svg file.' );
        
        // Detect file type
        $file_type = $this->bsk_dd_detect_file_type($file_path, $original_filename);
        if (!$file_type) {
            $return_array['message'] = 'Unable to detect file type';

            return $return_array;
        }
        
        // Type-specific validation
        switch ($file_type) {
            case 'svg':
                $return_array = $this->bsk_dd_validate_svg_file($file_path, $original_filename);
            case 'pdf':
                $return_array = $this->bsk_dd_validate_pdf_file($file_path, $original_filename);
            case 'png':
                $return_array = $this->bsk_dd_validate_png_file($file_path, $original_filename);
            case 'jpeg':
                $return_array = $this->bsk_dd_validate_jpeg_file($file_path, $original_filename);
            case 'gif':
                $return_array = $this->bsk_dd_validate_gif_file($file_path, $original_filename);
        }

        $return_array['success'] = true;
        $return_array['message'] = 'Validating passed.';
        
        return $return_array;
    }
    
     /**
     * Detect file type using multiple methods
     */
    public function bsk_dd_detect_file_type($file_path, $filename) {
        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        // Preliminary detection by extension
        foreach ($this->allowed_types as $type => $config) {
            if (in_array($extension, $config['extensions'])) {
                // Verify MIME type matches
                if ($this->verify_mime_type($file_path, $type)) {
                    return $type;
                }
            }
        }
        
        return false;
    }
    
    /**
     * Verify MIME type using multiple detection methods
     */
    private function verify_mime_type($file_path, $expected_type) {
        if (!isset($this->allowed_types[$expected_type])) {
            return false;
        }
        
        $allowed_mimes = $this->allowed_types[$expected_type]['mimes'];
        
        // Method 1: finfo (most reliable)
        if (function_exists('finfo_open')) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $detected_mime = finfo_file($finfo, $file_path);
            finfo_close($finfo);
            
            if (in_array($detected_mime, $allowed_mimes)) {
                return true;
            }
        }
        
        // Method 2: File signature verification
        return $this->verify_file_signature($file_path, $expected_type);
    }
    
    /**
     * Verify file signature (magic numbers)
     */
    private function verify_file_signature($file_path, $expected_type) {
        $file_header = $this->get_file_header($file_path, 20);
        
        switch ($expected_type) {
            case 'pdf':
                return strpos($file_header, '%PDF') === 0;
                
            case 'png':
                return $file_header === "\x89PNG\r\n\x1a\n";
                
            case 'jpeg':
                return strpos($file_header, "\xFF\xD8\xFF") === 0;
                
            case 'gif':
                return strpos($file_header, 'GIF8') === 0;
                
            case 'svg':
                return $this->check_svg_signature($file_header);
                
            default:
                return false;
        }
    }
    
    
    /**
     * SVG file validation
     */
    private function bsk_dd_validate_svg_file($file_path, $filename) {

        $return_array = array( 'success' => false, 'message' => 'Not a svg file.' );

        $config = $this->allowed_types['svg'];
        
        // Check SVG signature
        $file_header = $this->get_file_header($file_path, 500);
        if (!$this->check_svg_signature($file_header)) {
            $return_array['message'] = ('Invalid SVG signature');

            return $return_array;
        }
        
        // Validate XML structure
        if (!$this->bsk_dd_validate_xml_structure($file_path)) {
            $return_array['message'] = ('Invalid SVG XML structure');

            return $return_array;
        }
        
        $return_array['success'] = true;
        $return_array['message'] = 'Validating passed.';

        return $return_array;
    }
    
    /**
     * PDF file validation
     */
    private function bsk_dd_validate_pdf_file($file_path, $filename) {

        $return_array = array( 'success' => false, 'message' => 'Not a pdf file.' );

        $config = $this->allowed_types['pdf'];
                
        // Check PDF signature
        $file_header = $this->get_file_header($file_path, 20);
        if (strpos($file_header, '%PDF') !== 0) {

            $return_array['message'] = ('Invalid PDF signature');

            return $return_array;
        }
        
        // Check file end marker
        $file_tail = $this->get_file_tail($file_path, 100);
        if (strpos($file_tail, '%%EOF') === false) {

            $return_array['message'] = ('PDF file incomplete');

            return $return_array;
        }
        
        $return_array['success'] = true;
        $return_array['message'] = 'Validating passed.';

        return $return_array;
    }
    
    /**
     * PNG file validation using GD library
     */
    private function bsk_dd_validate_png_file($file_path, $filename) {

        $return_array = array( 'success' => false, 'message' => 'Not a pdf file.' );

        $config = $this->allowed_types['png'];
        
        // Validate PNG using GD
        if (function_exists('imagecreatefrompng')) {
            $image = @imagecreatefrompng($file_path);
            if ($image === false) {
                $return_array['message'] = ('Invalid PNG file');

                return $return_array;
            }
            imagedestroy($image);
        }
        
        $return_array['success'] = true;
        $return_array['message'] = 'Validating passed.';

        return $return_array;
    }
    
     /**
     * JPEG file validation using GD library
     */
    private function bsk_dd_validate_jpeg_file($file_path, $filename) {
        $return_array = array( 'success' => false, 'message' => 'Not a pdf file.' );

        $config = $this->allowed_types['jpeg'];
                
         // Validate JPEG using GD
        if (function_exists('imagecreatefromjpeg')) {
            $image = @imagecreatefromjpeg($file_path);
            if ($image === false) {
                $return_array['message'] = ('Invalid JPEG file');

                return $return_array;
            }
            imagedestroy($image);
        }
        
        $return_array['success'] = true;
        $return_array['message'] = 'Validating passed.';

        return $return_array;
    }
    
    /**
     * GIF file validation using GD library
     */
    private function bsk_dd_validate_gif_file($file_path, $filename) {
        $return_array = array( 'success' => false, 'message' => 'Not a pdf file.' );

        $config = $this->allowed_types['gif'];
                
        // Validate GIF using GD
        if (function_exists('imagecreatefromgif')) {
            $image = @imagecreatefromgif($file_path);
            if ($image === false) {
                $return_array['message'] = ('Invalid GIF file');

                return $return_array;
            }
            imagedestroy($image);
        }
        
        $return_array['success'] = true;
        $return_array['message'] = 'Validating passed.';

        return $return_array;
    }
    
    /**
     * Check SVG file signature patterns
     */
    private function check_svg_signature($content) {
        $content = preg_replace('/^\xEF\xBB\xBF/', '', $content);
        $content = trim($content);
        
        $svg_patterns = array(
            '/^<\?xml\s+[^>]*\?>\s*<svg/is',
            '/^<\s*svg[^>]*>/is',
            '/^<\?xml[^>]*\?>\s*<\s*!DOCTYPE\s+svg[^>]*>\s*<svg/is'
        );
        
        foreach ($svg_patterns as $pattern) {
            if (preg_match($pattern, $content)) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Validate XML structure for SVG files
     */
    private function bsk_dd_validate_xml_structure($file_path) {
        $content = file_get_contents($file_path);
        if ($content === false) {
            return false;
        }
        
        libxml_use_internal_errors(true);
        $dom = new DOMDocument();
        $loaded = $dom->loadXML($content);
        libxml_clear_errors();
        
        return $loaded;
    }
    
    /**
     * Get file header content
     */
    private function get_file_header($file_path, $length) {
        $handle = fopen($file_path, 'r');
        if (!$handle) return '';
        
        $header = fread($handle, $length);
        fclose($handle);
        return $header;
    }
    
    /**
     * Get file tail content
     */
    private function get_file_tail($file_path, $length) {
        $handle = fopen($file_path, 'r');
        if (!$handle) return '';
        
        fseek($handle, -$length, SEEK_END);
        $tail = fread($handle, $length);
        fclose($handle);
        return $tail;
    }
    
    /**
     * Get allowed MIME types array
     */
    public function get_allowed_mimes() {
        $mimes = array();
        foreach ($this->allowed_types as $type => $config) {
            foreach ($config['extensions'] as $ext) {
                $mimes[$ext] = $config['mimes'][0];
            }
        }
        return $mimes;
    }
    

}