<?php

/**
 * Classe de comunicação com os WebServices da Tray (http://tray.com.br)
 * @author Fábio Rodriguez <brajola@gmail.com>
 */
class TrayWS {

	/**
	 * URL do WebService da Loja
	 * @author Fábio Rodriguez <brajola@gmail.com>
	 */
	private $WSURL;
	
	/**
	 * Objeto SOAP da Classe
	 * @author Fábio Rodriguez <brajola@gmail.com>
	 */
	private $SOAP;
	
	/**
	 * ID da Loja Tray
	 * @author Fábio Rodriguez <brajola@gmail.com>
	 */
	public $StoreID;
	
	/**
	 * Nome do Usuário da Loja
	 * @author Fábio Rodriguez <brajola@gmail.com>
	 * 
	 * @description	Geralmente utiliza-se um usuário chamado "webservice"
	 *				Você pode incluir um usuário acessando a url:
	 *				http://ID_DA_SA_LOJA.corpsuite.com.br/adm/configuracoes/incluir_usuario.php
	 *
	 */
	public $UserName;
	
	/**
	 * Senha do usuário da loja
	 * @author Fábio Rodriguez <brajola@gmail.com>
	 */
	public $Password;

	/**
	 * Construtor da class
	 * @author Fábio Rodriguez <brajola@gmail.com>
	 */
	function __construct($StoreID, $UserName, $Password)
	{
		try{
			$this->WSURL	= "https://{$StoreID}.corpsuite.com.br/webservice/v2/ws_servidor.php?wsdl";
			$this->StoreID	= $StoreID;
			$this->UserName	= $UserName;
			$this->Password	= $Password;
		} catch (Exception $e) {
			return $e->getMessage();
		}
	}
	
	/**
	 * Envia e recebe requisições para o servidor da loja Tray
	 * @author Fábio Rodriguez <brajola@gmail.com>
	 */
	private final function __doRequest($SOAP_Method, $SOAP_Data)
	{
		try{
			$config = array(
				'trace'			=> 0,
				'exceptions'	=> true,
				'cache_wsdl'	=> WSDL_CACHE_NONE
			);
			
			$this->SOAP = new SoapClient($this->WSURL, $config);
			
			if(!$this->SOAP){
				throw new Exception("[CLASS INSTANCE ERROR]");
			}
			
			$result = $this->SOAP->__soapCall($SOAP_Method, $SOAP_Data);
		
			if(is_soap_fault($result)){
				throw new Exception("{$result->faultcode}:{$result->faultstring}");
			}
			
			return $result;
		} catch (Exception $e) {
			return $e->getMessage();
		}
	}
	
	/**
	 * Retorna todas as formas de pagamento disponíveis
	 * @author Fábio Rodriguez <brajola@gmail.com>
	 */
	public final function getFormasPagamentosDisponiveis()
	{
		return $this->__doRequest("fWSFormasPagamentosDisponiveis", array(
			'pid_loja'	=> $this->StoreID,
			'plogin'	=> $this->UserName,
			'psenha'	=> $this->Password
		));
	}
	
	/**
	 * Atualiza o estoque de um produto
	 * @author Fábio Rodriguez <brajola@gmail.com>
	 * 
	 * @param string $Produto
	 * @param string $Estoque
	 */
	public final function doAtualizaEstoqueProduto($Produto, $Estoque)
	{
		return $this->__doRequest("fWSAtualizaEstoqueProduto", array(
			'pid_loja'		=> $this->StoreID,
			'plogin'		=> $this->UserName,
			'psenha'		=> $this->Password,
			'is_grade'		=> 'N',
			'id_produto'	=> $Produto,
			'estoque'		=> $Estoque
		));
	}
	
	/**
	 * Atualiza a disponibilidade de um produto
	 * @author Fábio Rodriguez <brajola@gmail.com>
	 * 
	 * @param string $Produto
	 * @param string $Disponivel
	 */
	public final function doAtualizaProdutoDisponivel($Produto, $Disponivel)
	{
		return $this->__doRequest("fWSAtualizaProdutoDisponivel", array(
			'pid_loja'		=> $this->StoreID,
			'plogin'		=> $this->UserName,
			'psenha'		=> $this->Password,
			'is_grade'		=> 'N',
			'id_produto'	=> $Produto,
			'disponivel'	=> $Disponivel
		));
	}
	
	/**
	 * Retorna todos os pedidos
	 * @author Fábio Rodriguez <brajola@gmail.com>
	 */
	public final function getPedidos()
	{
		return $this->__doRequest("fWSImportaPedidos", array(
			'pid_loja'		=> $this->StoreID,
			'plogin'		=> $this->UserName,
			'psenha'		=> $this->Password,
			'status_atual'	=> ''
		));
	}
	
	/**
	 * Retorna os dados do cliente por Id
	 * @author Fábio Rodriguez <brajola@gmail.com>
	 */
	public final function getClienteId($clienteID)
	{
		return $this->__doRequest("fWSImportaClientePorId", array(
			'pid_loja'		=> $this->StoreID,
			'plogin'		=> $this->UserName,
			'psenha'		=> $this->Password,
			'id_cliente'	=> $clienteID
		));
	}
	
	/**
	 * Retorna todos os itens de um pedido
	 * @author Fábio Rodriguez <brajola@gmail.com>
	 */
	public final function getItensPedido($pedidoID)
	{
		return $this->__doRequest("fWSImportaItensPedidoPorId", array(
			'pid_loja'		=> $this->StoreID,
			'plogin'		=> $this->UserName,
			'psenha'		=> $this->Password,
			'id_pedido'		=> $pedidoID
		));
	}
	
	/**
	 * Retorna todas as parcelas de um pedido
	 * @author Fábio Rodriguez <brajola@gmail.com>
	 */
	public final function getParcelasPedido($pedidoID)
	{
		return $this->__doRequest("fWSImportaParcelasPedidoPorId", array(
			'pid_loja'		=> $this->StoreID,
			'plogin'		=> $this->UserName,
			'psenha'		=> $this->Password,
			'id_pedido'		=> $pedidoID
		));
	}
	
	/**
	 * Retorna todos os pedidos completos
	 * @author Fábio Rodriguez <brajola@gmail.com>
	 */
	public final function getPedidosCompletos()
	{
		$pedidos = $this->getPedidos();

		foreach($pedidos as $p){
			$return[] = array(
				'pedido'	=> $p,
				'cliente'	=> $this->getClienteId($p->id_cliente),
				'itens'		=> $this->getItensPedido($p->id_pedido),
				'parcelas'	=> $this->getParcelasPedido($p->id_pedido)
			);
		}
		
		return $return;
	}
}
?>
