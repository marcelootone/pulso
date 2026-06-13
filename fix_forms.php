<?php
$files = [
    'resources/views/alunos/edit.blade.php',
    'resources/views/eletivas/create.blade.php',
    'resources/views/eletivas/edit.blade.php',
    'resources/views/espacos/create.blade.php',
    'resources/views/espacos/edit.blade.php',
    'resources/views/estudo-orientado/solicitacoes/create.blade.php',
    'resources/views/notas/create.blade.php',
    'resources/views/turmas/edit.blade.php',
    'resources/views/vinculos/create.blade.php',
];

foreach ($files as $file) {
    if (!file_exists($file)) {
        echo "File not found: $file\n";
        continue;
    }
    $content = file_get_contents($file);
    
    if (strpos($content, '<x-slot name="footer">') !== false && strpos($content, '<form') !== false) {
        $id = 'form-' . substr(md5($file), 0, 6);
        
        if (strpos($content, 'id="form-') === false) {
            // Add ID to the main form (the one with method="POST" or similar, not a generic inline form, 
            // but in these files the first form is usually the main one).
            $content = preg_replace('/<form\s+(?!id=)/', '<form id="'.$id.'" ', $content, 1);
            
            // Add form="id" to submit button
            $content = preg_replace('/(type="submit"[^>]*?)(?<!form=")(?<!form=)/', '$1 form="'.$id.'"', $content);
            
            file_put_contents($file, $content);
            echo "Fixed $file\n";
        } else {
            echo "Already fixed or has ID: $file\n";
        }
    } else {
        echo "Pattern not found in: $file\n";
    }
}
