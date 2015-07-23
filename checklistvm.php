<?php
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
jimport( 'joomla.plugin.plugin' );

class plgSystemChecklistvm extends JPlugin {   
    function plgSystemChecklistvm( &$subject, $config ) {
       parent::__construct( $subject, $config );
    }    

    function onAfterDispatch() {
        $app    = JFactory::getApplication();
        $doc    = JFactory::getDocument();
        $option = JRequest::getVar('option');
        $view   = JRequest::getVar('view'); 
        $layout = JRequest::getVar('layout');
        $task   = JRequest::getVar('task');        

        if($app->isAdmin()) {      
            if ($option == 'com_virtuemart' and ($view == 'virtuemart' or $view == '')) {
                $html_mensagem = '<h3>Checklist E-commerce</h3>';

                // verificação de produtos
                $query = "SELECT count(*) as total FROM #__virtuemart_products WHERE published = 1;";
                $result = $this->relatorioSql($query);
                $texto = "Não há produtos publicados";
                if ($result->total == 0) {
                    $html_mensagem .= $this->linhaChecklist($texto, "index.php?option=com_virtuemart&view=product", false);
                } else {
                    $html_mensagem .= $this->linhaChecklist($texto, "", true);
                }

                // verificação de categorias
                $query = "SELECT count(*) as total FROM #__virtuemart_categories WHERE published = 1;";
                $result = $this->relatorioSql($query);
                $texto = "Não há categorias publicadas";
                if ($result->total == 0) {
                    $html_mensagem .= $this->linhaChecklist($texto, "index.php?option=com_virtuemart&view=category", false);
                } else {
                    $html_mensagem .= $this->linhaChecklist($texto, "", true);
                }

                // verificação de método de pagamento
                $query = "SELECT count(*) as total FROM #__virtuemart_paymentmethods WHERE published = 1;";
                $result = $this->relatorioSql($query);
                $texto = "Não há métodos de pagamento publicados";
                if ($result->total == 0) {
                    $html_mensagem .= $this->linhaChecklist($texto, "index.php?option=com_virtuemart&view=paymentmethod", false);
                } else {
                    $html_mensagem .= $this->linhaChecklist($texto, "", true);
                }

                // verificação de método de envio
                $query = "SELECT count(*) as total FROM #__virtuemart_shipmentmethods WHERE published = 1;";
                $result = $this->relatorioSql($query);
                $texto = "Não há métodos de envio publicados";
                if ($result->total == 0) {
                    $html_mensagem .= $this->linhaChecklist($texto, "index.php?option=com_virtuemart&view=shipmentmethod", false);
                } else {
                    $html_mensagem .= $this->linhaChecklist($texto, "", true);
                }

                // verificação de status de pedido
                $query = "SELECT count(*) as total FROM #__virtuemart_orderstates WHERE published = 1;";
                $result = $this->relatorioSql($query);
                $texto = "Não há status de pedido publicados";
                if ($result->total == 0) {
                    $html_mensagem .= $this->linhaChecklist($texto, "index.php?option=com_virtuemart&view=orderstatus", false);
                } else {
                    $html_mensagem .= $this->linhaChecklist($texto, "", true);
                }
                
                // verificação de status de pedido
                $query = "SELECT count(*) as total FROM #__virtuemart_orderstates WHERE order_status_code in ('P','X','C');";
                $result = $this->relatorioSql($query);
                $texto = "Não há os status de pedido padrão da loja ( P, C, X )";
                if ($result->total == 0) {
                    $html_mensagem .= $this->linhaChecklist($texto, "index.php?option=com_virtuemart&view=orderstatus", false);
                } else {
                    $html_mensagem .= $this->linhaChecklist($texto, "", true);
                }

                // verificação de moedas
                $query = "SELECT count(*) as total FROM #__virtuemart_currencies WHERE published = 1;";
                $result = $this->relatorioSql($query);
                $texto = "Não há moedas publicadas";
                if ($result->total == 0) {
                    $html_mensagem .= $this->linhaChecklist($texto, "index.php?option=com_virtuemart&view=currency", false);
                } else {
                    $html_mensagem .= $this->linhaChecklist($texto, "", true);
                }

                // verificação de vendedores
                $query = "SELECT count(*) as total FROM #__virtuemart_vendors;";
                $result = $this->relatorioSql($query);
                $texto = "Não há lojista cadastrado";
                if ($result->total == 0) {
                    $html_mensagem .= $this->linhaChecklist($texto, "index.php?option=com_virtuemart&view=user&task=editshop", false);
                } else {
                    $html_mensagem .= $this->linhaChecklist($texto, "", true);
                }

                // verificação dos dados do vendedor
                $query = "SELECT count(v.virtuemart_vendor_id) AS total FROM #__virtuemart_vendors v INNER JOIN #__virtuemart_vmusers vu on vu.virtuemart_vendor_id = v.virtuemart_vendor_id;";
                $result = $this->relatorioSql($query);
                $texto = "Não há dados de remetente do lojista cadastrados corretamente";
                if ($result->total == 0) {
                    $html_mensagem .= $this->linhaChecklist($texto, "index.php?option=com_virtuemart&view=user&task=editshop", false);
                } else {
                    $html_mensagem .= $this->linhaChecklist($texto, "", true);
                }

                // verificação de moedas
                $query = "SELECT count(*) as total FROM #__virtuemart_vendors WHERE (vendor_currency is not null or vendor_currency != '') and (vendor_accepted_currencies is not null or vendor_accepted_currencies != '');";
                $result = $this->relatorioSql($query);
                $texto = "Não há moeda configurada nos dados do lojista ";
                if ($result->total == 0) {
                    $html_mensagem .= $this->linhaChecklist($texto, "index.php?option=com_virtuemart&view=user&task=editshop", false);
                } else {
                    $html_mensagem .= $this->linhaChecklist($texto, "", true);
                }

                // verificação da linguagem
                try {
                    $query = "SELECT count(*) as total FROM #__virtuemart_vendors_".VMLANG.";";
                    $result_try = $this->relatorioSql($query);

                    $query = "SELECT count(*) as total FROM #__virtuemart_categories_".VMLANG.";";
                    $result_try = $this->relatorioSql($query);

                    $query = "SELECT count(*) as total FROM #__virtuemart_manufacturercategories_".VMLANG.";";
                    $result_try = $this->relatorioSql($query);

                    $query = "SELECT count(*) as total FROM #__virtuemart_manufacturers_".VMLANG.";";
                    $result_try = $this->relatorioSql($query);

                    $query = "SELECT count(*) as total FROM #__virtuemart_paymentmethods_".VMLANG.";";
                    $result_try = $this->relatorioSql($query);

                    $query = "SELECT count(*) as total FROM #__virtuemart_products_".VMLANG.";";
                    $result_try = $this->relatorioSql($query);

                    $query = "SELECT count(*) as total FROM #__virtuemart_shipmentmethods_".VMLANG.";";
                    $result_try = $this->relatorioSql($query);
                } catch (Exception $e) {
                    $texto = "É necessário executar a atualização das tabelas do VirtueMart";
                    if ($mail_from == "" or $mail_from == "teste@teste.com.br") {
                        $html_mensagem .= $this->linhaChecklist($texto, "index.php?option=com_config", false);
                    } else {
                        $html_mensagem .= $this->linhaChecklist($texto, "", true);
                    }
                }

                $config = JFactory::getConfig();
                $mail_from = $config->get('mailfrom');
                
                $texto = "Você deve trocar o endereço de e-mail de origem dos pedidos";
                if ($mail_from == "" or $mail_from == "teste@teste.com.br") {
                    $html_mensagem .= $this->linhaChecklist($texto, "index.php?option=com_config", false);
                } else {
                    $html_mensagem .= $this->linhaChecklist($texto, "", true);
                }

                $html_mensagem .= "<h4>Multi-idioma</h4>";

                // verificação de categorias
                $query = "SELECT ( count(c.virtuemart_category_id) - count(c2.virtuemart_category_id) ) as total FROM #__virtuemart_categories c LEFT JOIN #__virtuemart_categories_".VMLANG." c2 ON c2.virtuemart_category_id = c.virtuemart_category_id;";
                $result = $this->relatorioSql($query);
                $texto = "Há <b>".(int)$result->total."</b> categoria(s) inconsistente(s) (".VMLANG.")";
                if ($result->total != 0) {
                    $html_mensagem .= $this->linhaChecklist($texto, "index.php?option=com_virtuemart&view=category", false);
                } else {
                    $html_mensagem .= $this->linhaChecklist($texto, "", true);
                }

                $query = "SELECT ( count(c.virtuemart_product_id) - count(c2.virtuemart_product_id) ) as total FROM #__virtuemart_products c LEFT JOIN #__virtuemart_products_".VMLANG." c2 ON c2.virtuemart_product_id = c.virtuemart_product_id;";
                $result = $this->relatorioSql($query);
                $texto = "Há <b>".(int)$result->total."</b> produtos(s) inconsistente(s) (".VMLANG.")";
                if ($result->total != 0) {
                    $html_mensagem .= $this->linhaChecklist($texto, "index.php?option=com_virtuemart&view=product", false);
                } else {
                    $html_mensagem .= $this->linhaChecklist($texto, "", true);
                }

                $query = "SELECT ( count(c.virtuemart_vendor_id) - count(c2.virtuemart_vendor_id) ) as total FROM #__virtuemart_vendors c LEFT JOIN #__virtuemart_vendors_".VMLANG." c2 ON c2.virtuemart_vendor_id = c.virtuemart_vendor_id;";
                $result = $this->relatorioSql($query);
                $texto = "Há <b>".(int)$result->total."</b> dado(s) de lojista(s) inconsistente(s) (".VMLANG.")";
                if ($result->total != 0) {
                    $html_mensagem .= $this->linhaChecklist($texto, "index.php?option=com_virtuemart&view=user&task=editshop", false);
                } else {
                    $html_mensagem .= $this->linhaChecklist($texto, "", true);
                }

                $query = "SELECT ( count(c.virtuemart_manufacturercategories_id) - count(c2.virtuemart_manufacturercategories_id) ) as total FROM #__virtuemart_manufacturercategories c LEFT JOIN #__virtuemart_manufacturercategories_".VMLANG." c2 ON c2.virtuemart_manufacturercategories_id = c.virtuemart_manufacturercategories_id;";
                $result = $this->relatorioSql($query);
                $texto = "Há <b>".(int)$result->total."</b> categoria(s) de fabricante(s) inconsistente(s) (".VMLANG.")";
                if ($result->total != 0) {
                    $html_mensagem .= $this->linhaChecklist($texto, "index.php?option=com_virtuemart&view=manufacturercategories", false);
                } else {
                    $html_mensagem .= $this->linhaChecklist($texto, "", true);
                }

                $query = "SELECT ( count(c.virtuemart_manufacturer_id) - count(c2.virtuemart_manufacturer_id) ) as total FROM #__virtuemart_manufacturers c LEFT JOIN #__virtuemart_manufacturers_".VMLANG." c2 ON c2.virtuemart_manufacturer_id = c.virtuemart_manufacturer_id;";
                $result = $this->relatorioSql($query);
                $texto = "Há <b>".(int)$result->total."</b> fabricante(s) inconsistente(s) (".VMLANG.")";
                if ($result->total != 0) {
                    $html_mensagem .= $this->linhaChecklist($texto, "index.php?option=com_virtuemart&view=manufacturer", false);
                } else {
                    $html_mensagem .= $this->linhaChecklist($texto, "", true);
                }

                $query = "SELECT ( count(c.virtuemart_paymentmethod_id) - count(c2.virtuemart_paymentmethod_id) ) as total FROM #__virtuemart_paymentmethods c LEFT JOIN #__virtuemart_paymentmethods_".VMLANG." c2 ON c2.virtuemart_paymentmethod_id = c.virtuemart_paymentmethod_id;";
                $result = $this->relatorioSql($query);
                $texto = "Há <b>".(int)$result->total."</b> método(s) de pagamento inconsistente(s) (".VMLANG.")";
                if ($result->total != 0) {
                    $html_mensagem .= $this->linhaChecklist($texto, "index.php?option=com_virtuemart&view=paymentmethod", false);
                } else {
                    $html_mensagem .= $this->linhaChecklist($texto, "", true);
                }

                $query = "SELECT ( count(c.virtuemart_shipmentmethod_id) - count(c2.virtuemart_shipmentmethod_id) ) as total FROM #__virtuemart_shipmentmethods c LEFT JOIN #__virtuemart_shipmentmethods_".VMLANG." c2 ON c2.virtuemart_shipmentmethod_id = c.virtuemart_shipmentmethod_id;";
                $result = $this->relatorioSql($query);
                $texto = "Há <b>".(int)$result->total."</b> método(s) de pagamento inconsistente(s) (".VMLANG.")";
                if ($result->total != 0) {
                    $html_mensagem .= $this->linhaChecklist($texto, "index.php?option=com_virtuemart&view=shipmentmethod", false);
                } else {
                    $html_mensagem .= $this->linhaChecklist($texto, "", true);
                }

                $query = "SELECT ( count(c.virtuemart_shipmentmethod_id) - count(c2.virtuemart_shipmentmethod_id) ) as total FROM #__virtuemart_shipmentmethods c LEFT JOIN #__virtuemart_shipmentmethods_".VMLANG." c2 ON c2.virtuemart_shipmentmethod_id = c.virtuemart_shipmentmethod_id;";
                $result = $this->relatorioSql($query);
                $texto = "Há <b>".(int)$result->total."</b> método(s) de pagamento inconsistente(s) (".VMLANG.")";
                if ($result->total != 0) {
                    $html_mensagem .= $this->linhaChecklist($texto, "index.php?option=com_virtuemart&view=shipmentmethod", false);
                } else {
                    $html_mensagem .= $this->linhaChecklist($texto, "", true);
                }

                /*
                - Pré-configuração dos produtos
                - Categorias
                - Atributos com controle de estoque
                - Correios integrado e configurado
                - Endereço da loja
                - Métodos de envio
                - Métodos de pagamento
                - Produtos de teste
                - Ativar o cache do VirtueMart
                - Configurar o template
                - Trocar logo do site
                - Atualizar dados do endereço
                */
                JFactory::getApplication()->enqueueMessage($html_mensagem);
            }
        }
    }

    private function relatorioSql($query) {
        $db = JFactory::getDBO();         
        $db->setQuery($query);
        $result = $db->loadObject(); 
        return $result;
    }

    private function linhaChecklist($texto, $link="", $ok=false) {        
        if ($ok) {
            $mensagem = "<div style='color: #616161;'>- <i style='text-decoration: line-through'>".$texto."</i>&nbsp;<span class='badge badge-success'>ok</span></div>";
        } else {
            $mensagem = "<div>- <i style='color: #000; font-weight: bolder'>".$texto."</i> <span class='badge badge-warning'><a href='".$link."'>corrigir</a></span> </div>";
        }
        return $mensagem;
    }

}

?>