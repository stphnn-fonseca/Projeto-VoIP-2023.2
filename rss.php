#!/usr/bin/php
<?php

require('phpagi.php');

$agi = new AGI();

$agi->answer();

function sendMessageToUsername($username, $message) {
    global $agi;
    $botToken = '6683963944:AAGLXvJPU2Qj307AIvy9ifNviIQsTHcQ-vQ';
    $apiUrl = "https://api.telegram.org/bot{$botToken}/getUpdates";
    $updates = json_decode(file_get_contents($apiUrl), true);

    $chatId = null;
    foreach ($updates['result'] as $update) {
        $user = $update['message']['chat'];
        if (isset($user['username']) && $user['username'] === $username) {
            $chatId = $user['id'];
           // break;
        }
    }

    if ($chatId === null) {
        $$agi->stream_file('/root/aluno/telegram/audios/nao');
    } else {
        $sendMessageUrl = "https://api.telegram.org/bot{$botToken}/sendMessage";
        $data = array(
            'chat_id' => $chatId,
            'text' => $message,
        );

        $options = array(
            'http' => array(
                'method'  => 'POST',
                'content' => json_encode($data),
                'header' => "Content-Type: application/json\n",
            ),
        );

        $context  = stream_context_create($options);
        $result = file_get_contents($sendMessageUrl, false, $context);
      
        if ($result === FALSE) {
            $agi->verbose('Erro ao enviar a mensagem.');
            $agi->stream_file('/root/aluno/telegram/audios/pro');
        } else {
            $agi->stream_file('/root/aluno/telegram/audios/enviada');
            $agi->verbose('Mensagem enviada com sucesso.');
        }
  
    
       
    }
    
}


function removerCaracteresEspeciais($str) {
    // Substitui todos os caracteres especiais por uma string vazia
    $strSemEspeciais = preg_replace('/[^\p{L}\p{N}\s]/u', '', $str);

    return $strSemEspeciais;
}




function mostrarTitulosNoticias($rssLink) {
    global $agi;
    $rss = simplexml_load_file($rssLink);
       
    if ($rss === false) {
        $agi->verbose('Erro ao carregar o RSS.');
        return array();
    }

    $count = min(count($rss->channel->item), 5);
    for ($i = 0; $i < $count; $i++) {
        $titulo = $rss->channel->item[$i]->title;
        $n = $i + 1;
        $agi->say_digits($n); 
        $string = removerCaracteresEspeciais((string) $titulo);  
        $agi->verbose(gettype($string)); 
        
        exec('espeak --clearcache'); 
        $agi->exec('espeak', "\"$string\"", 'any');
                
        sleep(1);
        $agi->verbose("$n - $titulo");
    }
 
    return $rss->channel->item;
}


function obterLinkNoticia($noticias, $indice) {
    return $noticias[$indice]->link;
}

$links = array(
    'https://g1.globo.com/dynamo/educacao/rss2.xml',
    'https://g1.globo.com/dynamo/politica/mensalao/rss2.xml',
    'https://g1.globo.com/dynamo/tecnologia/rss2.xml',
    'https://g1.globo.com/dynamo/rn/rio-grande-do-norte/rss2.xml'
);

function menu1($links){
  global $agi;
  do {
      $agi->stream_file('/root/aluno/telegram/audios/inic');
      $resultado = $agi->get_data('beep', 5000, 1);
      $escolha = intval($resultado['result']);
      $agi->verbose("Escolha 1: $escolha");
  } while ($escolha < 1 || $escolha > count($links));
  $linkEscolhido = $links[$escolha - 1];
  

  return $linkEscolhido;

};

$escolhaNoticia = 0;
while ($escolhaNoticia != 9) {
  do {
    
    if ($escolhaNoticia == 0) {
      $linkEscolhido = menu1($links);
    }
    
    $agi->stream_file('/root/aluno/telegram/audios/notitele');
    $agi->stream_file('/root/aluno/telegram/audios/voltar');
    $noticias = mostrarTitulosNoticias($linkEscolhido);
    
    $resultado = $agi->get_data('beep', 5000, 1);
    $escolhaNoticia = intval($resultado['result']);
    $agi->verbose("Escolha 2: $escolhaNoticia");
  } while ($escolhaNoticia < 1 || $escolhaNoticia > count($noticias));
    $linkNoticiaEscolhida = (string) obterLinkNoticia($noticias, $escolhaNoticia - 1);
    $tituloNoticia = (string) $noticias[$escolhaNoticia - 1]->title;
    $username = $agi->get_variable('username');
    $user = $username['data'];
    $agi->verbose((string) $linkNoticiaEscolhida);
    $agi->verbose((string) $tituloNoticia);
    $agi->verbose((string) $user);
    $message = "Olá! Eu sou o seu bot de notícias.\n\nAqui está a notícia escolhida\n\n$tituloNoticia\n\n $linkNoticiaEscolhida ";
    $agi->verbose($message); 
    sendMessageToUsername($user, $message);
    //$agi->stream_file('/root/aluno/telegram/audios/enviada');
}
$agi->stream_file('/root/aluno/telegram/audios/fim');
$agi->hangup();
    
?>
