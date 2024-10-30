<?php
/**
 * A classe Apiki_Buscape_API foi criada para ajudar no desenvolviemnto de
 * aplicações usando os webservices disponibilizados pela API do BuscaPé.
 *
 * As funções desta classe tem os mesmos nomes dos serviços disponibilizados pelo
 * BuscaPé.
 *
 * @author Apiki
 * @version 1.0
 * @license Creative Commons Atribuição 3.0 Brasil. http://creativecommons.org/licenses/by/3.0/br/
 */
class Apiki_Buscape_API {

    /**
     * Id da aplicação
     * @var string
     */
    protected $_applicationId   = '';

    /**
     * Código do país
     * @var string
     */
    protected $_countryCode     = '';

    /**
     * Formato de retorno
     * @var string
     */
    protected $_format          = '';

    /**
     * Usando JSON
     * @var string
     */
    protected $_isJson          = '';

    /**
     * Ambiente de produção
     * @var string
     */
    protected $_server          = 'sandbox';

    /**
     * A cada instância criada, deverá ser passado como parâmetro obrigatório o
     * ID da aplicação. Outros parâmetros de configuração como o código do país,
     * formato de resposta e qual ambiente se está trabalhando também podem ser
     * passados.
     *
     * O parâmetro <countryCode> é onde será definido o código do país onde a API
     * do BuscaPé buscará os dados. Por padrão esse parâmetro é BR (Brasil). E os
     * códigos que podem ser usados e disponibilizados pela API são: AR, BR, CL,
     * CO, MX, PE, VE.
     *
     * No parâmetro <format> é definido o formato de resposta que a API do BuscaPé
     * deverá retornar para sua aplicação. Por padrão a API retorna em XML, mas
     * pode ser retornado também em formato JSON.
     *
     * Em <sandbox> você define também se está em ambiente de testes pi produção,
     * true para ambiente de teste e false para ambiente de produção.
     *
     * @param str   $applicationId  ID da aplicação
     * @param str   $countryCode    Código do estado
     * @param str   $format         Formato de resposta
     * @param bool  $sandbox        True para ambiente de testes
     */
    public function __construct( $applicationId, $countryCode = 'BR', $format = 'xml', $sandbox = true )
    {
        $this->_applicationId   = $applicationId;
        $this->_countryCode     = $countryCode;
        $this->_format          = $format;
        $this->_isJson          = ( $this->_format == 'json' ) ? '&format=json' : '';
        
        if( !$sandbox )
            $this->_server = 'wbs';

        if( empty( $this->_applicationId ) )
            $this->_showErrors('ID da aplicação não pode ser vazio.');
        
        if( !in_array( $this->_countryCode, array( 'AR', 'BR', 'CL', 'CO', 'MX', 'PE', 'VE' ) ) )
            $this->_showErrors( sprintf( 'O código do país <b>%s</b> não existe.', $this->_countryCode ) );

        if( !in_array( $this->_format, array( 'xml', 'json' ) ) )
            $this->_showErrors( sprintf( 'O formato de retorno <b>%s</b> não existe.', $this->_format ) );
    }

    /**
     * Função faz pesquisa de categorias, permite que você exiba informações
     * relativas às categorias. É possível obter categorias, produtos ou ofertas
     * informando apenas um ID de categoria.
     *
     * Os parâmetros permitidos e aceitos por esta função deverão ser passados em
     * um array como parâmetro, são eles:
     * 
     * categoryId   = Id da categoria
     * keyword      = Palavra-chave buscada entre as categorias
     * callback     = Função de retorno a ser executada caso esteja usando o método
     * json como retorno.
     *
     * Pelo menos um dos parâmetros, <categoryID> ou <keyword> são requeridos para
     * funcionamento desta função.
     *
     * @param   array   $args Parâmetros passados para gerar a url de requisição
     * @return  string  Retorno da pesquisa feita no BuscaPé, no formato requerido.
     */
    public function findCategoryList( $args )
    {
        $serviceName = 'findCategoryList';
        $default     = array();
        $args        = array_merge( $default , $args );

        if( !empty( $args['categoryId'] ) or $args['categoryId'] == 0 )
            $param = '?categoryId=' . (int)$args['categoryId'];

        if( !empty( $args['keyword'] ) )
            $param = '?keyword=' . (string)$args['keyword'];

        if( empty( $param ) )
            $this->_showErrors( sprintf( 'Pelo menos um parâmetro de pesquisa é requerido na função <b>%s</b>.', $serviceName ) );

        $callback   = ( !empty( $args['callback'] ) ) ? '&callback=' . $args['callback'] : '';
        $url        = sprintf( 'http://%s.buscape.com/service/%s/%s/%s/%s%s%s', $this->_server, $serviceName, $this->_applicationId, $this->_countryCode, $param, $this->_isJson, $callback );

        return $this->_getContent( $url );
    }

    /**
     * Função permite que você pesquise uma lista de produtos únicos
     * utilizando o id da categoria final ou um conjunto de palavras-chaves
     * ou ambos.
     * 
     * Os parâmetros permitidos e aceitos por esta função deverão ser passados em
     * um array como parâmetro, são eles:
     *
     * categoryId   = Id da categoria
     * keyword      = Palavra-chave buscada entre as categorias
     * callback     = Função de retorno a ser executada caso esteja usando o método
     * json como retorno.
     *
     * Pelo menos um dos parâmetros, <categoryID> ou <keyword> são requeridos para
     * funcionamento desta função. Os dois também podem ser usados em conjunto.
     * Ou seja, podemos buscar uma palavra-chave em apenas uma determinada categoria.
     *
     * @param   array   $args Parâmetros passados para gerar a url de requisição
     * @return  string  Retorno da pesquisa feita no BuscaPé, no formato requerido.
     */
    public function findProductList( $args )
    {
        $serviceName = 'findProductList';
        $default     = array();
        $args        = array_merge( $default, $args );

        if( !empty( $args['categoryId'] ) )
            $param = '?categoryId=' . (int)$args['categoryId'];

        if( isset( $param ) and !empty( $args['keyword'] ) )
            $param = $param . '&keyword=' . (string)$args['keyword'];

        if( !empty( $args['keyword'] ) )
            $param = '?keyword=' . (string)$args['keyword'];

        if( empty( $param ) )
            $this->_showErrors( sprintf( 'Pelo menos um parâmetro de pesquisa é requerido na função <b>%s</b>.', $serviceName ) );

        $callback   = ( !empty( $args['callback'] ) ) ? '&callback=' . $args['callback'] : '';
        $url        = sprintf( 'http://%s.buscape.com/service/%s/%s/%s/%s%s%s', $this->_server, $serviceName, $this->_applicationId, $this->_countryCode, $param, $this->_isJson, $callback );

        return $this->_getContent( $url );
    }

    /**
     * Função pesquisa uma lista de ofertas. É possível obter a lista de ofertas
     * informando o ID do produto.
     *
     * Os parâmetros permitidos e aceitos por esta função deverão ser passados em
     * um array como parâmetro, são eles:
     * 
     * categoryId   = Id da categoria
     * keyword      = Palavra-chave buscada entre as categorias
     * productId    = Id do produto
     * barcode      = Código de barras do produto
     * callback     = Função de retorno a ser executada caso esteja usando o método
     * json como retorno.
     *
     * Pelo menos um dos parâmetros de pesquisa devem ser informados para o retorno
     * da função. Os parâmetros <categoryId> e <keyword> podem ser usados em conjunto.
     * 
     * @param   array   $args Parâmetros passados para gerar a url de requisição.
     * @return  string  Retorno da pesquisa feita no BuscaPé, no formato requerido.
     */
    public function findOfferList( $args )
    {
        $serviceName = 'findOfferList';
        $default     = array();
        $args        = array_merge( $default, $args );

        if( !empty( $args['categoryId'] ) )
            $param = '?categoryId=' . $args['categoryId'];

        if( isset( $param ) and !empty( $args['keyword'] ) )
            $param = $param . '&keyword=' . $args['keyword'];
        elseif( !isset( $param ) and !empty( $args['keword'] ) )
            $param = '?keyword=' . $args['keyword'];

        if( !empty( $args['productId'] ) )
            $param = '?productId=' . $args['productId'];

        if( !empty( $args['barcode'] ) )
            $param = '?barcode=' . $args['barcode'];

        if( empty( $param ) )
            $this->_showErrors( sprintf( 'Pelo menos um parâmetro de pesquisa é requerido na função <b>%s</b>.', $serviceName ) );

        $callback   = ( !empty( $args['callback'] ) ) ? '&callback=' . $args['callback'] : '';
        $url        = sprintf( 'http://%s.buscape.com/service/%s/%s/%s/%s%s%s', $this->_server, $serviceName, $this->_applicationId, $this->_countryCode, $param, $this->_isJson, $callback );

        return $this->_getContent( $url );
    }

    /**
     * Função retorna os produtos mais populares do BuscaPé
     *
     * O parâmeto permitido e aceito por esta função deverá ser passado em
     * um array como parâmetro:
     *
     * callback = Função de retorno a ser executada caso esteja usando o método
     * json como retorno.
     * 
     * @param   array   $args Parâmetros passados para gerar a url de requisição.
     * @return  string  Retorno da pesquisa feita no BuscaPé, no formato requerido.
     */
    public function topProducts( $args = array() )
    {        
        $serviceName = 'topProducts';
        $isJson      = ( $this->_format == 'json' ) ? '?format=json' : '';
        $callback    = ( !empty( $isJson ) and !empty( $args['callback'] ) ) ? '&callback=' . $args['callback'] : '';
        $url         = sprintf( 'http://%s.buscape.com/service/%s/%s/%s/%s%s%s', $this->_server, $serviceName, $this->_applicationId, $this->_countryCode, $param, $isJson, $callback );

        return $this->_getContent( $url );
    }

    /**
     * Função retorna as avaliações dos usuários sobre um determinado produto
     *
     * Os parâmetros permitidos e aceitos por esta função deverão ser passados em
     * um array como parâmetro, são eles:
     *
     * productId    = Id do produto
     * callback     = Função de retorno a ser executada caso esteja usando o método
     * json como retorno.
     *
     * O parâmetro <productId> é obrigatório.
     * 
     * @param   args    $args Parâmetros passados para gerar a url de requisição.
     * @return  string  Retorno da pesquisa feita no BuscaPé, no formato requerido.
     */
    public function viewUserRatings( $args )
    {
        $serviceName = 'viewUserRatings';
        $default     = array();
        $args        = array_merge( $default, $args );

        if( !empty( $args['productId'] ) )
            $param = '?productId=' . $args['productId'];

        if( empty( $param ) )
            $this->_showErrors( sprintf( 'ID do produto requerido na função <b>%s</b>.', $serviceName ) );

        $callback   = ( !empty( $args['callback'] ) ) ? '&callback=' . $args['callback'] : '';
        $url        = sprintf( 'http://%s.buscape.com/service/%s/%s/%s/%s%s%s', $this->_server, $serviceName, $this->_applicationId, $this->_countryCode, $param, $this->_isJson, $callback );

        return $this->_getContent($url);
    }

    /**
     * Função retorna os detalhes técnicos de um determinado produto.
     *
     * Os parâmetros permitidos e aceitos por esta função deverão ser passados em
     * um array como parâmetro, são eles:
     *
     * productId    = Id do produto
     * callback     = Função de retorno a ser executada caso esteja usando o método
     * json como retorno.
     * 
     * @param  array    $args Parâmetros passados para gerar a url de requisição.
     * @return string   Função de retorno a ser executada caso esteja usando o método
     */
    public function viewProductDetails( $args )
    {
        $serviceName = 'viewProductDetails';
        $default     = array();
        $args        = array_merge( $default, $args );

        if( !empty( $args['productId'] ) )
            $param = '?productId=' . $args['productId'];

        if( empty( $param ) )
            $this->_showErrors( sprintf( 'ID do produto requerido na função <b>%s</b>.', $serviceName ) );

        $callback   = ( !empty( $args['callback'] ) ) ? '&callback=' . $args['callback'] : '';
        $url        = sprintf( 'http://%s.buscape.com/service/%s/%s/%s/%s%s%s', $this->_server, $serviceName, $this->_applicationId, $this->_countryCode, $param, $this->_isJson, $callback );

        return $this->_getContent($url);
    }

    /**
     * Função retorna os detalhes da loja/empresa como: endereços, telefones de
     * contato etc...
     *
     * Os parâmetros permitidos e aceitos por esta função deverão ser passados em
     * um array como parâmetro, são eles:
     *
     * sallerId = Id da loja/empresa
     * callback = Função de retorno a ser executada caso esteja usando o método
     * json como retorno.
     *
     * O parâmetro <sellerId> é requerido e obrigatório.
     *
     * @param   array   $args Parâmetros passados para gerar a url de requisição.
     * @return  string  Função de retorno a ser executada caso esteja usando o método.
     */
    public function viewSellerDetails( $args )
    {
        $serviceName = 'viewSellerDetails';
        $default     = array();
        $args        = array_merge( $default, $args );

        if( !empty( $args['sellerId'] ) )
            $param = '?sellerId=' . $args['sellerId'];

        if( empty( $param ) )
            $this->_showErrors( sprintf( 'ID da loja/empresa requerido na função <b>%s</b>.', $serviceName ) );

        $callback   = ( !empty( $args['callback'] ) ) ? '&callback=' . $args['callback'] : '';
        $url        = sprintf( 'http://%s.buscape.com/service/%s/%s/%s/%s%s%s', $this->_server, $serviceName, $this->_applicationId, $this->_countryCode, $param, $this->_isJson, $callback );

        return $this->_getContent($url);
    }

    /**
     * Função exibe os erros
     *
     * @param string $error
     */
    protected function _showErrors( $error )
    {
        echo "<meta http-equiv='Content-Type' content='text/html; charset=UTF-8'/>";
        echo $error;
        exit;
    }

    /**
     * Função busca retorna os dados da url requisitada
     *
     * @param   str $url URL de acesso via CURL
     * @return  str Dados de retorno da URL requisitada
     */
    protected function _getContent( $url )
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_USERAGENT, "Mozilla/4.0");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $retorno = curl_exec($curl);
        curl_close($curl);

        return $retorno;
    }
}
?>