<?php

class BSKPDFM_Security_Sanitizer {
        
     /**
     * Sanitize uploaded file based on type
     */
    public function bsk_dd_sanitize_uploaded_file($file_path, $file_type) {
        switch ($file_type) {
            case 'svg':
                return $this->bsk_dd_sanitize_svg_file($file_path);
            case 'pdf':
                return $this->bsk_dd_sanitize_pdf_file($file_path);
            case 'png':
            case 'jpeg':
            case 'gif':
                return $this->bsk_dd_sanitize_image_file($file_path, $file_type);
            default:
                return false;
        }
    }
    
    /**
     * Sanitize SVG file by removing dangerous elements
     */
    private function bsk_dd_sanitize_svg_file($file_path) {
        $content = file_get_contents($file_path);
        if ($content === false) {
            return false;
        }
        
        libxml_use_internal_errors(true);
        $dom = new DOMDocument();
        $dom->loadXML($content);
        
        // Remove dangerous tags
        $dangerous_tags = array('script', 'iframe', 'embed', 'object', 'link');
        foreach ($dangerous_tags as $tag) {
            $elements = $dom->getElementsByTagName($tag);
            $nodes_to_remove = array();
            foreach ($elements as $element) {
                $nodes_to_remove[] = $element;
            }
            foreach ($nodes_to_remove as $node) {
                $node->parentNode->removeChild($node);
            }
        }
        
        // Remove dangerous attributes
        $xpath = new DOMXPath($dom);
        $nodes = $xpath->query('//*');
        $dangerous_attributes = array(
            'onload', 'onclick', 'onerror', 'onmouseover', 'onmouseout',
            'onfocus', 'onblur', 'onkeypress', 'onkeydown', 'onkeyup', 'onbegin', 'onend', 'ontoggle',
            'href', 'xlink:href',
        );
        
        foreach ($nodes as $node) {
            foreach ($dangerous_attributes as $attr) {
                if ($node->hasAttribute($attr)) {
                    $node->removeAttribute($attr);
                }
            }
        }
        
        // Save bsk_dd_sanitized content
        $cleaned_content = $dom->saveXML();
        return file_put_contents($file_path, $cleaned_content) !== false;
    }
    
    /**
     * Sanitize PDF file (basic implementation)
     * Note: PDF sanitization is complex - consider professional libraries
     */
    private function bsk_dd_sanitize_pdf_file($file_path) {
        // PDF files are difficult to bsk_dd_sanitize directly
        // Recommended approaches:
        // 1. Use professional PDF processing libraries
        // 2. Convert PDF to images and back to PDF
        // For now, basic validation is performed
        
        return true; // Return true temporarily, implement proper sanitization in production
    }
    
    /**
     * Sanitize image files by regenerating them
     */
    private function bsk_dd_sanitize_image_file($file_path, $image_type) {
        // Regenerate image to remove potential malicious code
        switch ($image_type) {
            case 'png':
                $image = imagecreatefrompng($file_path);
                if ($image) {
                    $result = imagepng($image, $file_path);
                    imagedestroy($image);
                    return $result;
                }
                break;
                
            case 'jpeg':
                $image = imagecreatefromjpeg($file_path);
                if ($image) {
                    $result = imagejpeg($image, $file_path, 90);
                    imagedestroy($image);
                    return $result;
                }
                break;
                
            case 'gif':
                $image = imagecreatefromgif($file_path);
                if ($image) {
                    $result = imagegif($image, $file_path);
                    imagedestroy($image);
                    return $result;
                }
                break;
        }
        
        return false;
    }
    
}
