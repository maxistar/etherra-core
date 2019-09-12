<?php 
namespace etherra;

include 'BaseTest.php';

class HeadTest extends BaseTest
{
	var $pagination;
    public function setUp(){
        $this->pagination = new tools_Pagination(10);
    }
    public function tearDown(){}
    
    public function testSimple(){
    	$_GET = array('test'=>'test','test1'=>'test');
    	$url = $this->pagination->getUrl(array());
    	$this->assertTrue($url=='?test=test&amp;test1=test');
    }

    public function testArray(){
    	$_GET = array('test'=>array('1','2','3'));
    	$url = $this->pagination->getUrl(array());
    	$this->assertTrue($url=='?test[0]=1&amp;test[1]=2&amp;test[2]=3');
    }
    
    public function testArrayComplex(){
    	$_GET = array('test'=>array(0=>array('1','2'),'2','3'));
    	$url = $this->pagination->getUrl(array());
    	$this->assertTrue($url=='?test[0][0]=1&amp;test[0][1]=2&amp;test[1]=2&amp;test[2]=3');
    }
}