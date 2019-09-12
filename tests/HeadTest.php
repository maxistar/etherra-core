<?php 
namespace etherra;

include 'BaseTest.php';

class HeadTest extends BaseTest
{
    public function setUp(){
        $this->head = new Head();
    }
    public function tearDown(){}
    public function testAddHead(){
        $this->head->addHead('somevalue');
        $head_str = $this->head->getHtml();
        $this->assertTrue(strpos($head_str,'somevalue')!==false);
    }
    
    public function testAddScript(){
        $this->head->addScript('etherra/core/jquery');
        $head_str = $this->head->getHtml();
        $this->assertTrue(strpos($head_str,'etherra/core/jquery')!==false);
    }
    
    public function testAddScriptCond(){
        $this->head->addScriptCond('etherra/core/jquery','lt IE 9');
        $head_str = $this->head->getHtml();
        $this->assertTrue(strpos($head_str,'core/jquery')!==false);
        $this->assertTrue(strpos($head_str,'lt IE 9')!==false);    
    }
    
    public function testAddCss(){
        $this->head->addCss('etherra/core/site');
        $head_str = $this->head->getHtml();
        $this->assertTrue(strpos($head_str,'core/site')!==false);
    }
    
    public function testAddCssCond(){
        $this->head->addCssCond('etherra/core/site','lt IE 9');
        $head_str = $this->head->getHtml();
        $this->assertTrue(strpos($head_str,'core/site')!==false);
        $this->assertTrue(strpos($head_str,'lt IE 9')!==false);
    }
}