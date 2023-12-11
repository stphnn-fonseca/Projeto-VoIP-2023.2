<?php
$url = 'https://g1.globo.com/dynamo/tecnologia/rss2.xml'; // Substitua pelo URL real do seu feed RSS

// Lê o conteúdo do feed RSS
$xml = simplexml_load_file($url);

// Verifica se a leitura foi bem-sucedida
if ($xml) {
    $titulos = array(); // Array para armazenar os títulos

    // Itera sobre cada item no feed e adiciona os títulos ao array
    foreach ($xml->channel->item as $item) {
        $titulos[] = $item->title;
    }

    // Verifica se há pelo menos um título no array antes de tentar acessar o índice 1
    if (isset($titulos)) {
      for ($contador = 0; $contador <= 5; $contador++) {
        echo $titulos[$contador];
      }
         ;
    } else {
        echo 'Não há título na posição 1.';
    }

} else {
    echo 'Não foi possível carregar o feed RSS.';
}
?>
