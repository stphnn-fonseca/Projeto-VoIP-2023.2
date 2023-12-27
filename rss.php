<?php

ini_set('default_charset','UTF-8');
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Carregue a biblioteca PHPMailer
require 'Exception.php';
require 'PHPMailer.php';
require 'SMTP.php';

function enviarEmail($emailDestino, $linkNoticia, $tituloNoticia) {
    $mail = new PHPMailer(true);

    try {
        // Configurações do servidor SMTP do Gmail
        $mail->isSMTP();
        $mail->Host       = 'smtp-mail.outlook.com';
        $mail->SMTPAuth   =  true;
        $mail->Username   = 'voipnoticias@outlook.com';
        $mail->Password   = 'Voip2023';
        $mail->SMTPSecure = 'tls'; // Ou use 'ssl'
        $mail->Port       = 587;

        // Configurações do email
        $mail->setFrom('voipnoticias@outlook.com', 'Seu Nome');
        $mail->addAddress($emailDestino);
        $mail->Subject = 'Assunto do Email';

        // Mensagem do e-mail
        $mail->Body = "Prezado(a),

        Espero que este e-mail encontre você bem e em ótimo espírito! É um prazer compartilhar essa notícia com você. Para acessar a notícia completa, basta clicar no link abaixo:

        $tituloNoticia
        $linkNoticia

        Por favor, note que esta é uma mensagem automática.

        Atenciosamente,";

        // Envia o email
        $mail->send();
        echo 'Email enviado com sucesso!';
    } catch (Exception $e) {
        echo "Erro ao enviar o email: {$mail->ErrorInfo}";
    }
}

function mostrarTitulosNoticias($rssLink) {
    // Inclui a biblioteca SimpleXML para análise de XML
    $rss = simplexml_load_file($rssLink);

    // Verifica se o RSS foi carregado corretamente
    if ($rss === false) {
        echo "Erro ao carregar o RSS.";
        return array(); // Retorna um array vazio se houver erro
    }

    // Mostra os títulos das 5 principais notícias
    $count = min(count($rss->channel->item), 5);
    for ($i = 0; $i < $count; $i++) {
        $titulo = $rss->channel->item[$i]->title;
        echo ($i + 1) . ". $titulo\n";
    }

    return $rss->channel->item;
}

function obterLinkNoticia($noticias, $indice) {
    return $noticias[$indice]->link;
}

// Array de links
$links = array(
    'https://g1.globo.com/dynamo/educacao/rss2.xml',
    'https://g1.globo.com/dynamo/politica/mensalao/rss2.xml',
    'https://g1.globo.com/dynamo/tecnologia/rss2.xml',
    'https://g1.globo.com/dynamo/rn/rio-grande-do-norte/rss2.xml'
);

// Pergunta ao usuário qual link ele deseja
do {
    echo "Escolha um link (1 a 4): ";
    $escolha = (int)readline(); // Leitura da entrada do usuário
} while ($escolha < 1 || $escolha > count($links));

$linkEscolhido = $links[$escolha - 1];
$noticias = mostrarTitulosNoticias($linkEscolhido);

// Verifica se há notícias disponíveis
if (!empty($noticias)) {
    // Pergunta ao usuário qual notícia ele deseja
    do {
        echo "Escolha uma notícia (1 a 5): ";
        $escolhaNoticia = (int)readline(); // Leitura da entrada do usuário
    } while ($escolhaNoticia < 1 || $escolhaNoticia > count($noticias));

    $linkNoticiaEscolhida = obterLinkNoticia($noticias, $escolhaNoticia - 1);
    $emailDestino = 'ESTEPHANNYbFONSECA@hotmail.com';
    $linkNoticia = $linkNoticiaEscolhida;
    $tituloNoticia = $noticias[$escolhaNoticia - 1]->title;
    enviarEmail($emailDestino, $linkNoticia, $tituloNoticia);
} else {
    echo "Nenhuma notícia disponível.";
}
?>
