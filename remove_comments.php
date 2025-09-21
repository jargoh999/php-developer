<?php

$directory = __DIR__;
$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory));

foreach ($iterator as $file) {
    if ($file->isFile() && $file->getExtension() === 'php') {
        $filePath = $file->getPathname();
        
        $content = file_get_contents($filePath);
        
        $tokens = token_get_all($content);
        $output = '';
        
        foreach ($tokens as $token) {
            if (is_array($token)) {
                if ($token[0] === T_COMMENT || $token[0] === T_DOC_COMMENT) {
                    continue;
                }
                $output .= $token[1];
            } else {
                $output .= $token;
            }
        }
        
        file_put_contents($filePath, $output);
    }
}

echo "Comments removed from all PHP files.\n";
