<?php

/**
  *	
  * Classe que trata de todos os erros em apresenta-los e mostra a informação detalhada
  *	
  *
**/
abstract class Errors {
	
	protected function html($message, $file, $line)
	{
		$html = '<!docytype html>
					<html>
						<head>
							<meta charset="utf-8" />
							<meta name="viewport" content="width=device-width, initial-scale=1" />
							<link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet">
							<style class="text/css">
								* { padding: 0; margin: 0; }
								html, body { padding: 0; margin: 0; width: 100vw; min-height: 100vh; background: #eee; }
								div.container { margin: 0 auto; width: 70vw; }
								header { margin: 3% 0; padding: 20px; color: #c0392b; background: #fff; font-family: "PT Sans", sans-serif; border: 3px solid #c0392b; border-radius: 3px; }
							</style>
							<title>Pizza na brazza // Erro no código</title>
						</head>
						<body>
							<div class="container">
								<header class="header">
									<h3>ERRO: ' . $message . '</h3>
								</header>
								
								<main class="main">
									<b>Ficheiro:</b> ' . $file .'<br />
									<b>Linha:</b> ' . $line . '
								</main">
							
							</div>
						</body>
					</html>';
					
		return $html;
	}
	
	abstract public function errorShow($message, $file, $line);
}