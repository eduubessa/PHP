<?php

require_once(__dir__ . '\Errors.php');

/**
  *	
  * Classe que faz a ligação com o servidor de base de dados(MySQL),
  * onde os comandos SQL são construídos conforme as necessidades de
  *	cada programador.
  * 
  * Para se usar esta classe é necessário o uso da classe abstrata "Errors"
  * classe que trata de gerar o HTML e CSS para apresentar sempre que há um erro
  *
**/
class Database extends Errors {
	
	protected $host = "127.0.0.1";					#IP ou nome do servidor de base de dados
	protected $user = "root";						#Utilizador do servidor de base de dados
	protected $pass = "C!1kRzBTdFUP";				#Password do servidor de base de dados
	protected $name = "gestccdr_db";				#Nome da base de dados
	
	private $connection = null;						#Guardar a conexão de base de dados
	protected $table = null;						#Guarda o nome da tabela
	protected $sql = null;							#Guarda o código gerado
	
	/**
	 * 	Método constructor que inicializa a ligação com o servidor de base de dados, neste caso com o servidor MySQL,
	 * 	e seleciona a base de dados e caso haja algum problema apresenta erro
	**/
	public function __construct()
	{
		try {
			
			#Verifica se a ligação ja foi iniciada
			if($this->connection === null){
				
				#Caso se a sessão não foi iniciada irá inicializar
				$this->connection = new mysqli($this->host, $this->user, $this->pass);
			
				#verifica se os dados de ligação estão corretos e se foi possivel fazer a ligação com a base de dados
				if($this->connection->connect_error){
					#Caso a ligação esteja com erro irá apresentar a mensagem
					throw new Exception("MySQL - Os dados do servidor estão incorretos");
				}else{
					#Caso a ligação seja feita com sucesso, irá selecionar a base de dados!
					$this->connection->select_db($this->name);
				
					#Verifica se a ligação ao selecionar a base de dados, tenha dado erro
					if($this->connection->error){
						#Caso a base de dados não exista, irá apresentar erro!
						throw new Exception("MySQL - A base de dados não existe");
					}
				}
				
			}else{
				#Caso se a ligação não seja possivel irá mostrar um erro de ligação
				throw new Exception("MySQL - Não foi possivel fazer a ligação com o servidor");
			}
			
		}catch(Exception $e){
			#Chama o método "errorShow" que irá apresentar a página de erro, com a mensagem de erro, ficheiro e linha onde existe o erro";
			$this->errorShow($e->getMessage(), $e->getFile(), $e->getLine());
		}
	}


    /**
     * Método responsável receber o nome da base de dados e guardar
     *
     * @param $table
     * @return $this
     */
	public function table($table)
	{
		$this->table = $table;

		return $this;
	}
	
	/**
	 * Método que está responsável para gerar o SQL apenas para mostrar todos os registos
	 * 
	 * @param $table
	 * @return $this
	 *
	**/
	public function select()
	{
		try {
			#Verifica se a variavel $table está com algum valor
			if($this->table === null){
				#Caso não tenha valor vai dar erro e apresentar o erro que não selecionou a tabela
				throw new \Exception("Não seleccionou a tabela que pretende");
			}
			
			#Gera o comando SQL para mostrar todos os registos da tabela
			$this->sql = "SELECT * FROM `" . $this->table . "`";
			
			return $this;
			
		}catch(\Exception $e){
			#Chama o método "errorShow" que irá apresentar a página de erro, com a mensagem de erro, ficheiro e linha onde existe o erro";
			$this->errorShow($e->getMessage(), $e->getFile(), $e->getLine());
		}
	}
	
	/**
	 * Método que está responsável para juntar o WHERE com o o SELECT gerado pelo método SELECT.
     * Este método q
	 * 
	 * @param $field
	 * @param $operator
	 * @param $value
	 * @return $this
	 *
	**/
	public function where($field, $operator = null, $value)
	{
		try {
			#Verifica se a variável $field (campo) está com algum valor
			if($field == null)
			{
				#Caso se não tiver irá apresentar o erro
				throw new \Exception("<i>WHERE</i> necessita de um campo, e um valor para comparar");
				
			#verifica se a variável $value (valor) está com algum valor
			}else if($value == null){ 
				#Caso se não tiver irá apresentar o erro
				throw new \Exception("<i>WHERE</i> necessita de um campo, e um valor para comparar");
			}
			
			#Procura se existe a palavra "WHERE" no comando
			if(strpos($this->sql, "WHERE") == true){
				#Quando exista ele vai adicionar o AND ao comando
				$this->sql .= " AND";			
			}
			
			#Adiciona o WHERE `campo`
			$this->sql .= " WHERE `" . $field ."`";
			#Verifica se tem operador, se não tiver ele coloca o operador "LIKE"
			$this->sql .= ($operator == null) ? " LIKE " : " " . $operator . " ";
			#Verifica se o valor é numérico caso se for apenas adiciona o valor caso se não for adiciona as tolicas
			$this->sql .= (is_numeric($value)) ? $value : "'" . $value . "'";
		
			return $this;
		
		}catch(\Exception $e){
			#Chama o método "errorShow" que irá apresentar a página de erro, com a mensagem de erro, ficheiro e linha onde existe o erro";
			$this->errorShow($e->getMessage(), $e->getFile(), $e->getLine());
		}
	}

	public function group($group)
    {
        try {
            if($this->table !== null){
                $this->sql .= " GROUP BY " . $group;
            }

            return $this;
        }catch(\Exception $e){
            #Chama o método "errorShow" que irá apresentar a página de erro, com a mensagem de erro, ficheiro e linha onde existe o erro";
            $this->errorShow($e->getMessage(), $e->getFile(), $e->getLine());
        }
    }
	
	/**
	 * Método que está responsável para juntar o ORDER BY ao comando SELECT
	 * 
	 * @param $field		#Campo
	 * @param $operator		#Operador
	 * @param $value		#Valor
	 * 
	 * @return $this
	 *
	**/
	public function order($field, $order = null)
	{
		try {
			#Verifica se a variavél $field (campo) tem valor
			if($field !== null) {
				#Caso tenha valor irá gerar o "ORDER BY `field`"
				$this->sql .= " ORDER BY `" . $field . "`";
				#Verifica se o valor $order (ordem) tem valor, caso não tenha vai adicionar ASC para fazer a ordem crescente
				$this->sql .= ($order !== null) ? " " . $order : " ASC";
				
			}else{
				#Caso a variável $field (campo) não tenha valor vai apresentar erro
				throw new \Exception("<i>WHERE</i> necessita de um campo, e um valor para comparar");
			}
			
			return $this;
			
		}catch(\Exception $e){
			#Chama o método "errorShow" que irá apresentar a página de erro, com a mensagem de erro, ficheiro e linha onde existe o erro";
			$this->errorShow($e->getMessage(), $e->getFile(), $e->getLine());
		}
	}
	
	public function limit($limit)
	{
		try {
			if($this->sql !== null){
				$this->sql .= " LIMIT " . $limit;
			}
			
			return $this;
		}catch(Exception $e){
			#Chama o método "errorShow" que irá apresentar a página de erro, com a mensagem de erro, ficheiro e linha onde existe o erro";
			$this->errorShow($e->getMessage(), $e->getFile(), $e->getLine());
		}
	}
	
	/**
	 * Método que está responsável em executar o comando SQL e guardar os registos
	 * 
	 *
	**/
	public function get()
	{
		try {
			#Verifica se o objeto tem algum valor
			if($this->sql !== null){
				#Mostra o comando SQL
				echo $this->sql;
			}else{
				#Caso a variável nao tenha valor irá apresentar um erro
				throw new \Exception("Não é possivel ir buscar valores à base de dados sem SELECT");
			}
		}catch(\Exception $e){
			#Chama o método "errorShow" que irá apresentar a página de erro, com a mensagem de erro, ficheiro e linha onde existe o erro";
			$this->errorShow($e->getMessage(), $e->getFile(), $e->getLine());
		}
	}
	
	/**
	 * Método que está responsável em executar o comando SQL e guardar apenas um registo
	 * 
	 *
	**/
	public function first()
	{
		try {
			#Verifica se o objeto tem algum valor
			if($this->sql !== null){
				#Adiciona ao comando LIMIT 1, que irá limitar o número de registo para 1
				$this->sql .= " LIMIT 1";
				#Apresenta o comando final
				echo $this->sql;
			}
		}catch(Exception $e){
			#Chama o método "errorShow" que irá apresentar a página de erro, com a mensagem de erro, ficheiro e linha onde existe o erro";
			$this->errorShow($e->getMessage(), $e->getFile(), $e->getLine());
		}
	}

    /**
     * Método responsável que gera o comando SQL para inserir dados na base de dados
     *
     * @param array $data
     *
     */
	public function insert(array $data)
	{
	    try {

            #Verifica se a variavel $table está com algum valor
            if($this->table === null){
                #Caso não tenha valor vai dar erro e apresentar o erro que não selecionou a tabela
                throw new \Exception("Não seleccionou a tabela que pretende");
            }

            #Contador de posições do array com valor 0
            $i = 0;
            #Gera o comando SQL para criar registos da tabela
            $this->sql = "INSERT INTO `" . $this->table . "` (";

            #Vai buscar o "keys" (nome ou número das posições) do array para usar para o nome da coluna
            foreach(array_keys($data) as $key => $value) {
                #Adiciona mais 1 ao valor de $i
                $i++;
                #Junta ao comando SQL o nome da coluna
                $this->sql .= "`" . $value . "`";
                #Verifica se o número de colunas se é maior que o número da posição do array
                if ($i < count($data)) {
                    #Caso se for ele acrescenta ao comando SQL a ,
                    $this->sql .= ",";
                }
            }

            #Junta ao comando SQL ") VALUES (
            $this->sql .= ") VALUES (";

            #Contador de posições do array com valor 0
            $i = 0;

            #Vai buscar os valores para adicionar ao comando SQL
            foreach($data as $key => $value) {
                #Adiciona mais 1 ao valor de $i
                $i++;
                #Junta ao comando SQL o valor, e verifica se o valor é numérico ou não
                $this->sql .= (is_numeric($value)) ? $value : "'" . $value . "'";
                #Verifica se o número de colunas se é maior que o número da posição do array
                if ($i < count($data)) {
                    #Caso se for ele acrescenta ao comando SQL a ,
                    $this->sql .= ",";
                }
            }

            #Acrescente ao comando SQL ")";
            $this->sql .= ")";

            #Mostra o comando SQL
            echo $this->sql;

        }catch(\Exception $e){
	        $this->errorShow($e->getMessage(), $e->getFile(), $e->getLine());
        }
	}

	public function update(array $data)
    {
        #Verifica se a variavel $table está com algum valor
        if($this->table === null){
            #Caso não tenha valor vai dar erro e apresentar o erro que não selecionou a tabela
            throw new \Exception("Não seleccionou a tabela que pretende");
        }

        $i = 0;

        #Gera o comando SQL para atualizar o da tabela
        $sql = "UPDATE `" . $this->table . "` SET ";

        foreach($data as $key => $value) {
            $i++;
            $sql .= "`" . $key . "`=";
            $sql .= (is_numeric($value)) ? $value : "'" . $value . "'";
            if ($i < count($data)) {
                $sql .= ", ";
            }
        }

        #Procura se existe a palavra "WHERE" no comando
        if(strpos($this->sql, "WHERE") == true){
            #Caso exista ele vai adicionar "DELETE FROM `tabela` mais o valor de WHERE (this->sql)
            $this->sql = $sql . " " . $this->sql;
        }else{
            #Caso não exista vai fazer o DELETE FROM `tabela` que irá apagar todos os registos
            $this->sql = $sql;
        }

        echo $this->sql;
    }

    public function delete()
    {
        try {

            #Verifica se a variavel $table está com algum valor
            if($this->table === null){
                #Caso não tenha valor vai dar erro e apresentar o erro que não selecionou a tabela
                throw new \Exception("Não seleccionou a tabela que pretende");
            }

            #Procura se existe a palavra "WHERE" no comando
            if(strpos($this->sql, "WHERE") == true){
                #Caso exista ele vai adicionar "DELETE FROM `tabela` mais o valor de WHERE (this->sql)
                $this->sql = "DELETE FROM `" . $this->table . "` " . $this->sql;
            }else{
                #Caso não exista vai fazer o DELETE FROM `tabela` que irá apagar todos os registos
                $this->sql = "DELETE FROM `" . $this->table . "`";
            }

            #Mostra o comando
            echo $this->sql;

        }catch(\Exception $e){
            $this->errorShow($e->getMessage(), $e->getFile(), $e->getLine());
        }
    }

    /**
     * Método responsável para gerar o comando SQL para apagar tabelas
     *
     * @param $tabld
     */
    public function drop($table)
    {
        try {
            if($table !== null){
                $this->sql = "DROP TABLE `" . $table . "`";
            }else{
                throw new \Exception("Não seleccionou a tabela");
            }

            echo $this->sql;
        }catch(\Exception $e){
            $this->errorShow($e->getMessage(), $e->getFile(), $e->getLine());
        }
    }

	/**
	 * Método que está responsável em chamar o método HTML para gerar e apresentar a página de erro
	 * 
	 *
	**/
	public function errorShow($message, $file, $line)
	{
		#Esta a chamar o método "html" o mesmo se encontra na classe "Errors"
		#Estou a usar o $this porque a classe "Errors" foi extendida para a classe "Database"
		echo $this->html($message, $file, $line);
	}
}