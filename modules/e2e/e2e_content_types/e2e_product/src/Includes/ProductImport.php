<?php

namespace Drupal\e2e_product\Includes;

use \Drupal\node\Entity\Node;
use \Drupal\taxonomy\Entity\Term;

class ProductImport {
  
  private $api;
  private $ipdcproducts;
  private $productnodes;
  private $tids;
  private $xml;
  
  //constructor
  public function __construct() {
    $this->ipdcproducts = [];
    $this->productnodes = [];
    $this->tids = [];
  }
  
  //update all products based on IPDC date
  public function update(){
    
    $xml = $this->getXml();
    
    if($xml){
    
      foreach($xml->children() as $product){
        
        if($product->attributes()->action == 'modified'){
        //if($product->attributes()->action == 'unmodified'){
          
          $productxml = $this->getProductXml((string)$product->attributes()->id);
          
          if($productxml){
            foreach($productxml->children() as $productdetail){
              $this->updateProduct($productdetail);
            }
          }
          
        }
      }
    }
  }
  
  //get api
  private function getApi(){
    
    if(!$this->api)
      $this->setApi();
    
    return $this->api;
    
  }
  
  //get product node based on webid and date
  private function getProductNode($webid, $date, $addnew = TRUE){
    
    if(!isset($this->productnodes[$webid]))
      $this->setProductNode($webid, $date, $addnew);
    
    if(isset($this->productnodes[$webid]))
      return $this->productnodes[$webid];
    
    return FALSE;
  }

  
  //get ipdc product xml
  private function getProductXml($id){
    
    if(!isset($this->ipdcproducts[$id]))
      $this->setProductXml($id);
    
    if(isset($this->ipdcproducts[$id]))
      return $this->ipdcproducts[$id];
    
    return FALSE;
  }
  
  //get taxonomy term id based on term name and vid
  private function getTid($name, $vid){
    
    if(!isset($this->tids[$vid][$name]))
      $this->setTid($name, $vid);
    
    if(isset($this->tids[$vid][$name]))
      return $this->tids[$vid][$name];
    
    return FALSE;
  }
  
  //get ipdc xml
  private function getXml(){
    
    if(!$this->xml)
      $this->setXml();
    
    return $this->xml;
  }
  
  //set api based on product settings
  private function setApi(){
    
    $config = \Drupal::config('e2e_product.settings');
    $baseurl = $config->get('e2e_product.baseurl', '');
    $uuid = $config->get('e2e_product.uuid', '');
    
    $this->api = trim($baseurl, ' /') . '/{action}/' . trim($uuid, ' /');
  }
  
  //select existing or create new product node based on webid, set addnew to FALSE for only retrieving existing nodes
  private function setProductNode($webid, $date, $addnew = TRUE){
    
    $node = FALSE;
    
    $query = \Drupal::entityQuery('node');
    $nids = $query
      ->condition('type', 'product', '=')
      ->condition('field_ipdc_webid.value', $webid)
      ->range(0, 1)
      ->execute();
    
    if(!empty($nids)){
      $node = \Drupal::entityTypeManager()->getStorage('node')->load(reset($nids));
      
      if($addnew){
        //only return node if changed date is past timestamp 
        if($node->field_timestamp->value == $date)
         $node = FALSE;
        else
          \Drupal::logger('e2e_product')->notice('Update node '. reset($nids) .', field timestamp: '.$node->field_timestamp->value .', xmlDatum: '. $date);
      }
      
    }elseif($addnew){
      $node = Node::create([
        'title' => 'product ' . $webid,
        'type' => 'product', 
        'status' => 1,
        'created' => time(),
        'language' => 'und',
        'uid' => 1,
      ]);
      
      $node->set('field_ipdc_webid', [['value' => $webid]]);
      
      $node->save();
    }
    
    $this->productnodes[$webid] = $node;
  }
  
  //store ipdc product detail xml in array
  private function setProductXml($id){
    
    //$uri = $this->getApi() . 'GeefProduct/' . $id;
    $uri = str_replace('{action}', 'GeefProduct/' . $id, $this->getApi());
    //$response = \Drupal::httpClient()->get($uri, array('headers' => array('Content-Type' => 'text/xml; charset=UTF-8')));
    $response = \Drupal::httpClient()->get($uri, ['headers' => ['Accept' => 'application/xml'], 'query' => ['_format' => 'xml_extended']]);
    
    if (!empty($response->getBody()) && $response->getStatusCode() == 200)
      $this->ipdcproducts[$id] = simplexml_load_string((string)$response->getBody());
    else
      $this->ipdcproducts[$id] = FALSE;
    
  }
  
  //store taxonomy term id based on term name and vid. Create new term when it doesn't exist.
  private function setTid($name, $vid){
   
    $query = \Drupal::entityQuery('taxonomy_term');
    
    $tids = $query
      ->condition('name', $name, '=')
      ->condition('vid', $vid)
      ->range(0, 1)
      ->execute();
    
    if(!empty($tids)){
      $tid = reset($tids);
    }else{
      
      $term = Term::create(['name' => $name, 'vid' => $vid]);
      $term->save();
      
      $tid = $term->id();
    }
    
    $this->tids[$vid][$name] = $tid;
    
  }
  
  //store ipdc products in xml
  private function setXml(){
    
    $uri = str_replace('{action}', 'ZoekProducten', $this->getApi());
    
    $response = \Drupal::httpClient()->get($uri, ['headers' => ['Accept' => 'application/xml'], 'query' => ['_format' => 'xml_extended']]);
    
    if(!empty($response->getBody()) && $response->getStatusCode() == 200)
      $this->xml = simplexml_load_string($response->getBody());
    
    
  }
  
  //update existing or add new node based on ipdc product data
  private function updateProduct($ipdcproduct){
    
    $attributes = $ipdcproduct->attributes();
    
    $node = $this->getProductNode((string)$ipdcproduct->productId, (string)$attributes->datumLaatsteAanpassing);
    
    if($node){
      
      $config = \Drupal::config('e2e_product.settings');
      
      $node->set('title', (string)$ipdcproduct->titel);
      $node->set('changed', time());
      $node->set('field_section', [['target_id' => $config->get('e2e_product.section')]]);
      $node->set('show_title', [['value' => 1]]);
      
      $textfields = [
        'what' => $ipdcproduct->inhoud, 'conditions' => $ipdcproduct->voorwaarden,
        'procedure' => $ipdcproduct->procedure, 'bringwhat' => $ipdcproduct->watMeebrengen,
        'amount' => $ipdcproduct->bedrag, 'exceptions' => $ipdcproduct->uitzonderingen,
        'rules' => $ipdcproduct->regelgeving,
      ];
      
      foreach($textfields as $fieldname => $fieldvalue){
        $node->set('field_ipdc_' . $fieldname, [['format' => 'full_html', 'value' => (string)$fieldvalue]]);
      }
      
      $termfields = [
        'type' => ['values' => [$ipdcproduct->productType], 'vid' => 'product_type'],
        'target' => ['values' => $ipdcproduct->doelgroepen->children(), 'vid' => 'product_target'],
        'scope' => ['values' => [], 'vid' => 'product_scope'],
        'theme' => ['values' => $ipdcproduct->themas->children(), 'vid' => 'product_theme'],
        'keywords' => ['values' => $ipdcproduct->trefwoorden->children(), 'vid' => 'product_keyword'],
      ];
      
      if(isset($ipdcproduct->geografischeToepassingsgebieden))
        $termfields['scope']['values'] = $ipdcproduct->geografischeToepassingsgebieden->children();
      
      foreach($termfields as $fieldname => $termfield){
        $tids = [];
        
        foreach($termfield['values'] as $ipdcterm){
          
          if($ipdcterm->waarde)
            $tids[] = ['target_id' => $this->getTid((string)$ipdcterm->waarde, $termfield['vid'])];
        }
        
        $node->set('field_ipdc_' . $fieldname, $tids);
      }
      
      $nids = [];
      
      foreach($ipdcproduct->verwanteProducten->children() as $relatedproduct){
        
        $relatednode = $this->getProductNode((string)$relatedproduct->productId, FALSE, FALSE);
        
        if($relatednode)
          $nids[] = ['target_id' => $relatednode->id()];
        
      }
      
      $node->set('field_ipdc_reference', $nids);
      
      $node->set('field_timestamp', [['value' => (string)$attributes->datumLaatsteAanpassing]]);

      $node->save();
    }
    
  }
}

