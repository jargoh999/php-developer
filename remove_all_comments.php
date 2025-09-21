<?php

function removeComments($source) {
    $tokens = token_get_all($source);
    $output = '';
    
    foreach ($tokens as $token) {
        if (is_string($token)) {
            $output .= $token;
        } else {
            list($id, $text) = $token;
            
            switch ($id) {
                case T_COMMENT:
                case T_DOC_COMMENT:
                    break;
                default:
                    $output .= $text;
                    break;
            }
        }
    }
    
    return $output;
}

function processDirectory($dir) {
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::SELF_FIRST
    );
    
    foreach ($iterator as $file) {
        if ($file->isFile() && $file->getExtension() === 'php') {
            $path = $file->getRealPath();
            echo "Processing: $path\n";
            
            $content = file_get_contents($path);
            $newContent = removeComments($content);
            
            if ($content !== $newContent) {
                file_put_contents($path, $newContent);
                echo "  -> Updated\n";
            }
        }
    }
}

$projectRoot = __DIR__;
echo "Starting comment removal process...\n";
processDirectory($projectRoot);

echo "\nAll PHP files have been processed. Comments have been removed.\n";
