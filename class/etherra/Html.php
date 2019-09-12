<?php
namespace etherra;

class Html {
  static function Select($fieldName,$value1,$values,$additional=""){
	$s =  '<select name="'.$fieldName.'" '.$additional.'>';
	foreach ($values as $key=>$value){
		if ($key==$value1)
		$s .= '<option value="'.$key.'" selected="selected">'.$value.'</option>';
		else
		$s .= '<option value="'.$key.'">'.$value.'</option>';	
	}
  	$s .= "</select>";
	return $s;
  }
  
  static function SelectSql($fieldName,$value1,$values,$sql,$additional=""){
  	  $r = Db::query($sql);
  	  while($row=$r->next()){
  	      $values[$row[0]] = $row[1];
  	  }
      return self::Select($fieldName,$value1,$values,$additional);
  }
  
  static function Hidden($fieldName,$value){
  	return '<input type="hidden" name="'.$fieldName.'" value="'.htmlspecialchars($value).'">';
  }
  
  static function submit($name='',$value=''){
	if (!empty($value)){
		$value = 'value="'.$value.'"';
	}
	if (!empty($name)){
		$name = 'name="'.$name.'"';
	}
	return '<input type="submit" "'.$value.'" '.$name.'" />';
  }
  
	
}