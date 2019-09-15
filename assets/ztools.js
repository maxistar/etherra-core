/**
 * base.js - Base of ztools library
 * 
 * 
 * The main file of the library. It contains some useful functions that can be used together with
 * another parts of the library.
 *
 * @example examples/core/empty
 *   
 */


/**
 * z(s,defaults) getter of z.Element object
 *
 * this function contains all other functions of library 
 * universal entry point for all other objects  
 * 
 * @param s ID or actual dom element
 * @param defaults default values of object to be constructed to be passed to constructor as second arguments
 * @returns z.Element object
 */
function z(s,defaults){ //
  if (typeof(s)==='string'){
    return z.id(s,defaults);
  }
  else {
    return z.get(s,defaults);
  }
}

/**
 * z.copy(dst,src) copy properties from source to destination
 * 
 * copy all properties except prototype
 * @param dst destination object
 * @param src source object
 */
z.copy = function(dst,src){
      for(key in src){
        if (key!=='prototype'){
          dst[key] = src[key];
        }
      }
};

/**
 * z.ccopy(dst,src) copy all properties and build links to inherited methods
 * 
 *  * 
 * @param dst destination object
 * @param src source object 
 */
z.ccopy = function(dst,src){
    for(key in src){
      if (key!=='prototype'){
       	  if ((typeof(dst[key])=='function') && typeof(src[key])=='function'){
       		 var back = dst[key];
       		 dst[key] = src[key]
       		 dst[key].base = back;        
          }
          else {
        	 dst[key] = src[key];
          }
      }
    }
};

/**
 * z.each(obj,fn) iterates trough an object
 * 
 * 
 * @param obj itable object
 * @param fn function to be called on each iteration
 */
z.each = function(obj,fn){
    for(key in obj){
        if (obj.constructor.prototype[key]!==obj[key]){
            if (fn(key,obj[key])===false) break;
        }
    }
};

/**
 * z.bind(fn,obj) binds the function with object
 * 
 * 
 * @param fn function to bind
 * @param obj object to be bound
 * @returns bound function
 */
z.bind = function(fn,obj){
    return function(){
        return fn.apply(obj,arguments);
    }
}
/**
 * class.js - Inheritance implementation

 * 
 *
 * 
 * @depends base.js
 * @example examples/core/empty
 *  
 */
  
/**
 * z.Class
 *
 * base class of all classes
 */
z.Class = function(){};
/**
 * z.Class.base
 *
 * access to parent method
 */
z.Class.prototype.base = function(){
	return arguments.callee.caller.base.apply(this,arguments);
}

/**
 * z.Class.extend
 *
 * extends class
 *
 * @returns constructor for newly created class
 * @param obj class definition 
 */
z.Class.extend = function(obj){
	var this1 = this;
    var obj1 = function(){
    	var c = obj.construct || this1.prototype.construct || z.Class;
    	c.apply(this,arguments);
    }
    z.copy(obj1.prototype,this.prototype); //copy inherited properties
    z.ccopy(obj1.prototype,obj); //add new properties
    z.ccopy(obj1,this); //copy static properties
    return obj1;
}

/**
 * z.Class.implement
 *
 * implements new methods for class
 */
z.Class.implement = function(obj){
    z.ccopy(this.prototype,obj);
}

/**
 * z.Class.stat
 *
 * adds definition of static functions of class
 */
z.Class.stat = function(obj){
	z.ccopy(this,obj);
}   
/**
 * browser.js - Browser detection
 *
 * 
 * @depends base.js
 * @example examples/core/empty
 * 
 */

z.browser = { 
    platform: (navigator.platform.match(/mac|win|linux/i) || ['other'])[0].toLowerCase(),
    version: (navigator.userAgent.toLowerCase().match(/.+(?:rv|it|ra|ie|me)[\/: ]([\d.]+)/) || [-1,-1])[1],
    engine: 'unknown' //presto, trident, webkit, gecko
};

if (window.opera) z.browser.engine = 'presto';
else if (window.ActiveXObject) z.browser.engine = 'trident';
else if (!navigator.taintEnabled) z.browser.engine = 'webkit';
else if (document.getBoxObjectFor != null) z.browser.engine = 'gecko';

/**
 * element.js - Crossbrowser manitulations with element properties, events, styles
 * 
 * 
 * @depends base.js, class.js, browser.js
 * @example examples/core/empty
 * @author Maxim Starikov 
 */
 
 
/**
 * z.Element helper class for dom elements
 *
 * 
 * 
 */
z.Element = z.Class.extend({
   /**
    * z.Element::dom - link to native dom object
    * link to originla dom object
    */
    dom:null,
   /**
    * z.Element.construct(params) conctuctor of z.Element class
    *
    * 
    * @params params object containing setup parameters
    * 
    * parameters could be following:<br />
    * dom - dom element should be applied<br />
    * id - ID of element to be used<br />
    * tag - tag name of element to be created if defined "id" or "dom" should not be defined<br />
    * attr - attributes to be used<br />
    * prop - properties of the element to be used<br />
    * css - css styles to be applied here<br />
    * defaults - default properties of class created<br />
    */
    construct:function(params){
		params = params||{};
	    var doc = document;
	    this.dom = params.dom?params.dom:
	    	       (params.id?doc.getElementById(params.id):
	    	       (params.tag?doc.createElement(params.tag):doc.createElement('div')));
	    if (params.attr) this.attr(params.attr);
	    if (params.prop) this.prop(params.prop);
	    if (params.css) this.css(params.css);
	    if (params.defaults) z.copy(this,params.defaults);
	    if (params.children) this.appendChild(params.children);
	    
	    this.dom[this.constructor.zname] = this;
    },
       
   /**
    * z.Element.css(key_name,key_value) sets or gets style
    * 
    * 
    */
    css:function(key_name,key_value){
        if(arguments.length==2){
            try {
                this.setStyle(key_name,key_value);
            }
            catch(e){
                alert("wrong property:"+key_name+":"+key_value);
            }
        }
        else {
            if (typeof(key_name)=='object'){
                var this1 = this;
                z.each(key_name,function(key){
                    this1.css(key,key_name[key]);
                });
            } 
            else {
                return this.getStyle(key_name);
            }
        }
    },    
   /**
    * z.Element.setStyle(key_name,key_value) sets style
    */
    setStyle:function(key_name,key_value){
        if ((key_name=='opacity')){
            if (z.browser.engine == 'trident'){
                if ((key_value==1) && (typeof(this.dom.filters['alpha'])!=='undefined')){//remove alpha filter
                    this.dom.filters['alpha'].enabled = false;
                }
                else {
                    if ((typeof(this.dom.filters['alpha'])!=='undefined') && (!this.dom.filters['alpha'].enabled)){//enable aplha filter if disables
                        this.dom.filters['alpha'].enabled = true;
                    }
                    this.dom.style.filter = "alpha(opacity:"+(Math.round(key_value*100))+")";
                }
            }
            else {
                this.dom.style[key_name] = key_value;
            }
        }
        else if((key_name=='float') || (key_name=='cssFloat') || (key_name=='styleFloat')){
            if (z.browser.engine == 'trident'){
                this.dom.style.styleFloat = key_value;
            }
            else {
                this.dom.style.cssFloat = key_value;
            }
        }
        else {
            this.dom.style[key_name] = key_value;
        }
    },
   /**
    * z.Element.getStyle(key_name)
    * returns style
    * TODO make it crossbrowser too?
    */
    getStyle:function(key_name){
        return this.dom.style[key_name];
    },
    
   /**
    * z.Element.prop(key_name,key_value)
    * sets or retrieve property
    *
    */
    prop:function(key_name,key_value){
        if(arguments.length==2){
            this.setProp(key_name,key_value);                
        }
        else {
            if (typeof(key_name)=='object'){
                var this1 = this;
                z.each(key_name,function(key){
                    this1.prop(key,key_name[key]);
                });
            } 
            else {
                return this.getProp(key_name);
            }
        }    
    },

   /**
    * z.Element.setProp(key_name,key_value) sets property
    * 
    */
    setProp:function(key_name,key_value){
        try {
            this.dom[key_name] = key_value;        
        }
        catch(e){
            alert("wrong property:"+key_name+":"+key_value);
        }
    },
   /**
    * z.Element.getProp(key_name) returns property
    * 
    * TODO make it crossbrowser too?
    */
    getProp:function(key_name){
        if (key_name=='text') return (this.dom['text'] || this.dom['innerText']); 
        return this.dom[key_name];
    },
    
    /**
     * z.Element.attr(key_name,key_value) sets or retrieve property
     * 
     *
     */
     attr:function(key_name,key_value){
         if(arguments.length==2){
             this.setAttr(key_name,key_value);                
         }
         else {
             if (typeof(key_name)=='object'){
                 var this1 = this;
                 z.each(key_name,function(key){
                     this1.attr(key,key_name[key]);
                 });
             } 
             else {
                 return this.getAttr(key_name);
             }
         }    
     },

    /**
     * z.Element.setAttr(key_name,key_value) sets property
     * 
     */
     setAttr:function(key_name,key_value){
         try {
             this.dom.setAttribute(key_name,key_value);        
         }
         catch(e){
             alert("wrong property:"+key_name+":"+key_value);
         }
     },
    /**
     * z.Element.getAttr(key_name) returns property
     * 
     * TODO make it crossbrowser too?
     */
     getAttr:function(key_name){
         return this.dom.getAttribute(key_name);
     },
    
   /**
    * z.Element.hasClass(className)
    */    
    hasClass:function(className){
        return z.Element.hasClass(this.dom.className,className)
    },
   /**
    * z.Element.addClass(className)
    */
    addClass:function(className){
        if (!this.hasClass(className)){
            this.dom.className = this.dom.className+' '+className;
        }
    },
   /**
    * z.Element.removeClass(className)
    */    
    removeClass:function(className){
        this.dom.className = this.dom.className.replace(new RegExp('(^|\\s)' + className + '(?:\\s|$)'), '$1');
    },
       
   /**
    * z.Element.putBefore(el)
    */          
    putBefore:function(el){
        if (el.dom) el = el.dom //it's z.Element
        el.parentNode.insertBefore(this.dom,el);
    },
   /**
    * z.Element.putAfter(el)
    */     
    putAfter:function(el){
        if (el.dom) el = el.dom //it's z.Element
        if (el.nextSibling){
            el.parentNode.insertBefore(this.dom,el.nextSibling);
        }
        else {
            el.parentNode.appendChild(this.dom);
        }
    },
   /**
    * z.Element.putInto(el)
    */     
    putInto:function(el){
        if (el.dom) el = el.dom //it's z.Element
        el.appendChild(this.dom);
    },
   /**
    * z.Element.appendChild(el)    
    * the same as ordinal appendChild but argument can be also an array
    */ 
    appendChild:function(el){
        if (el.dom) el = el.dom //it's z.Element
        if (el.constructor === Array) {
          var i,len = el.length;
          for(i=0;i<len;i++){
            this.appendChild(el[i]);
          }
          return;
        }
        this.dom.appendChild(el);
    },
   /**
    * z.Element.remove(el)    
    * 
    */     
    remove:function(el){
        if (el) {
            if (el.dom)  el = el.dom //it's z.Element
        }
        else el = this.dom;        
        el.parentNode.removeChild(el);
    },
   /**
    * z.Element.getCoordinates()  
    * 
    */         
    getCoordinates:function(){
        function getPOL(obj){
            var x;
            x = obj.offsetLeft;
            if (obj.offsetParent != null)
            x += getPOL(obj.offsetParent);
            return x;       
        };
        function getPOT(obj){
            var y;
            y = obj.offsetTop;
            if (obj.offsetParent != null)
            y += getPOT(obj.offsetParent);
            return y;
        };
        return {top:getPOT(this.dom),left:getPOL(this.dom),width:this.dom.offsetWidth,height:this.dom.offsetHeight};
    }    
});


z.Element.stat({
   /**
	* z.Element::zname reference property name to connect z.Element object with dom object
	* 
	* there should be several objects derived from z.Element class connected with the same dom objects in this case
	* different z classes should have different values of zname property.
	*  
	*  
	*/
	zname:'_z',

   /**
	* z.Element::get(e,defaults) getter for z.Element
	* 
	* 
	*/   
	get:function (e,defaults){
 	    if (e[this.zname]!==undefined) return e[this.zname];
        return new this({'dom':e,'defaults':defaults});
    },

   /**
	* z.Element::id(id,defaults) returns z.Element by ID
	* 
	*  
	*/   
    id:function(id,defaults){
        var e = document.getElementById(id);
        if (e){
            return this.get(e,defaults);
        }
        return null;
    },

   /**
	* z.Element::hasClass(elementClassName,className) checks if className is part of elementClassName
	*/   
    hasClass:function(elementClassName,className){
        return (elementClassName.length > 0 && (elementClassName == className ||
            new RegExp("(^|\\s)" + className + "(\\s|$)").test(elementClassName)));
    },

   /**
	* z.Element::create(props) creates z.Element object
	* deprecated now 
	*/
    create:function(props){
        var el = new this(props);
        return el;
    },

    /**
	* z.Element::synonyms(name,names) creates synonyme for prototype function
	* 
	*/    
	synonyms:function(name,names){
        for(var i=0;i<names.length;i++){
            this.prototype[names[i]] = this.prototype[name];
        }
    }    
});

z.Element.synonyms('prop',['p']);
z.Element.synonyms('css',['s','style','c']);


/**
 * z.id(id,defaults) returns z.Element object by element ID
 * 
 * 
 * @param id - ID of element
 * @param defaults - default values of zelement to be constructed
 * @returns z.Element object
 */
z.id = function(id,defaults){
     return z.Element.id(id,defaults);
};

/**
 * z.get(el,defaults) returns z.Element object for dom element
 * 
 * 
 * @param id - ID of element
 * @param defaults - default values of zelement to be constructed
 * @returns z.Element object
 */
z.get = function(el,defaults){
     return z.Element.get(el,defaults);
};

/**
 * event.js - Crossbrowser events support
 * 
 * @depends base.js,element.js
 * @example examples/core/empty
 * 
 */

/**
 * class z.Event
 */
z.Event = z.Class.extend({
    dom:null,
    owner:null,
   /**
    * method construct - constructor of z.Event 
    */
    construct:function(e){
		with (this){
			this.dom = e;
			if (!e) return;
			e._z = this;
			this.shift = e.shiftKey;
			this.ctrl  = e.ctrlKey;
			this.alt   = e.altKey;
			this.meta  = e.metaKey;
			this.type  = e.type;
        
			var doc = document;
			doc = (!doc.compatMode || doc.compatMode == 'CSS1Compat') ? doc.documentElement : doc.body;
			this.page = {};
			this.page.x = dom.pageX || dom.clientX + doc.scrollLeft;
			this.page.y = dom.pageY || dom.clientY + doc.scrollTop;
        
			this.client = {};
			this.client.x = dom.layerX || dom.offsetX;
			this.client.y = dom.layerY || dom.offsetY;
        
			this.code = dom.which || dom.keyCode; 
			if (code > 111 && code < 124) this.key = 'f' + (code-111);
			this.key = this.key || keyCodes[code] || String.fromCharCode(code).toLowerCase();
		}
    },
   
   /**
    * 
    */
    keyCodes:{
        '13':'enter',
        '38':'up',
        '40':'down',
        '37':'left',
        '39':'right',
        '27':'esc',
        '32':'space',
        '8':'backspace',
        '9':'tab',
        '46':'delete'
    },
    

   /**
    * method stop
    * 
    * stops booble
    */
    stop:function(){
        if (this.dom.stopPropagation){
            this.dom.stopPropagation();
        }
        else {
            this.dom.cancelBubble = true;//ie
        }
    },
    
   /**
    * method cancel - cancels event
    * cancels default behaviour
    */
    cancel:function(){
        if (this.dom.stopPropagation){
            this.dom.preventDefault();
        }
        else {
            this.dom.returnValue = false;//ie
        }
    },
   /**
    * method halt
    * stops buble and cancels event 
    */
    halt:function(){
        this.stop();
        this.cancel();
    }
});

/**
 * static z.Event.get
 * 
 * returns z.Event object for given native Event object
 * 
 * @param e native Event object
 * @return instance of z.Event class
 */
z.Event.get = function(e){
  if (e._z) return e._z;
  return new z.Event(e);
}

/**
 * class z.Element - extended class of z.Element with crosbrowser events
 * 
 */
z.Element = z.Element.extend({
		events:null,
		construct:function(props){
			this.base(props);
			this.events = {};
		},
	   /**
	    * add event listener
	    */   
	    on:function(eventname,func,remove,custom){
        	if (remove) return this.removeEvent(eventname,func);
        	if (!this.events[eventname]) {	
     	    	this.events[eventname] = [];
     	    	var t1 = this;
     	    	if (!custom){
     	    	    this.addEvent(eventname, function(e){t1.callEvent(eventname,e)});
     	    	}
        	};
        	this.events[eventname].push(func);
        	return this;
		},
    
    	addEvent:function(name,func){
			if (this.dom.addEventListener){
				this.dom.addEventListener(name,func,false);
			}        
			else {
				this.dom.attachEvent('on'+name, func);
			}
    	},
     
	   
	    callEvent:function(eventname,e){
	        e = e?z.Event.get(e):z.Event.get(window.event);
	        e.owner = this;
	        if (this.events[eventname]) {
	        	//copy because even handlers can add or remove itself
	        	var hooks = this.events[eventname].slice(0),
	        	    len = hooks.length
	     	    for(var i=0;i<len;i++){
	     		    hooks[i].apply(this,[e]);
	     	    }
	        }
	    },
	    
	    fireEvent:function(eventname,e){
	    	e = e || new z.Event();
	    	if (hooks = this.events[eventname]) {
	     	    for(var i=0;i<hooks.length;i++){
	     		    hooks[i].apply(this,[e]);
	     	    }
	        }
	    },
	   
	  /**
	   * removes event
	   */
	    removeEvent:function(eventname,func){
	        if (this.events[eventname]){
	            var i,len = this.events[eventname].length;
	            for (i=0;i<len;i++){                
	                if (this.events[eventname][i]==func){
	                	this.events[eventname].splice(i,1);
		                return true;
	                }
	            }
	        }
	        return false;
	    }	
})

z.Element.stat({	
    shortEvents:function(names){
        var events = {}
        z.each(names,
	    function(key,event){
	    	events[event] = new Function("fn","remove","return this.on('"+[event]+"',fn,remove)");
	    }
        );
        this.implement(events);
    }
});

z.Element.shortEvents(['click','mousedown','mouseup','mousemove','mouseover','mouseout','focus','blur','change','keypress','keydown','keyup']);
/**
 * selector.js Non CSS XHTML Selector
 *
 * @depends base.js, element.js
 * @example examples/core/empty
 * 
 */
(function(){
	function hasClass(className,classNames){
	    if (typeof(classNames.push)=='function'){
		    for(var i=0;i<classNames.length;i++) if (hasClass(className,classNames[i])) return true;
		    return false;
	    }
	    else {
	    	return z.Element.hasClass(className,classNames);
	    }
	}
	
	function isTagName(tagName,tagNames){
		if (typeof(tagNames.push)=='function'){
		    for(var i=0;i<tagNames.length;i++) if (isTagName(tagName,tagNames[i])) return true;
		    return false;
	    }
	    else { 
	    	return (tagName.toUpperCase() == tagNames.toUpperCase());
	    }
	}
	
	function hasProperty(el,key,value){
		if (typeof(value.push)=='function'){
			for(var i=0;i<value.length;i++) if (hasProperty(el,key,value[i])) return true;
		    return false;
		}
		else {
			if (!el[key]) return false;
			if (el[key]!==value) return false;
			return true;
		}
	}
	
   /**
    * if element is complies with search it get applied to res
    *
    */
    function appendResult(search,el,res,constr,defaults){//private function
        var fail = false;
        z.each(search, function(key){
            if (key=='depth') return;
            if (key=='_t' || key=='tagName' || key=='tag') { //tag name is case insensitive
                if (!isTagName(el.tagName,search[key])){
                  fail = true;
                  return false;
                }
                return;
            }
            if (key=='_c' || key=='class'){
                if (!hasClass(el.className,search[key])){
                  fail = true;
                  return false;
                }
                return;
            }
            if (!hasProperty(el,key,search[key])){
            	fail = true; return false;
            }
        });
        if (fail) return;
        res.elements.push(constr.get(el,defaults));
        
    };
    
    function selectElement(el, search, res, constr, defaults){
    	constr = constr || z.Element;
    	defaults = defaults || {};
        var tagname = search['tagName'] || search['_t'] || search['tag'];
        var depth = search['depth'] || -1; //-1 recursive, 1 - only first level childs, 2 frist level and second level and so on
        if ((tagname) && (depth===-1)){
        	var num = 0;
        	var tn = tagname;
        	//alert(typeof(tagname.push));
        	while(((typeof(tagname.push)=='function') && (tn=tagname[num++])) || tn){
        		var els = el.getElementsByTagName(tn);
        		tn = false; //to stop cycle
        		var els_l = els.length;        
        		for(var i=0;i<els_l;i++){
        			appendResult(search,els[i],res,constr,defaults);     
        		}
        	}
        }
        else { //all elements
            var child;
            for(child = el.firstChild;child;child = child.nextSibling){
                if (child.nodeType!=1) continue; //need only elements
                appendResult(search,child,res,constr,defaults);
                if (depth === -1) selectElement(child, search, res, constr);
                else if (depth > 1) {search[depth] = depth--; selectElement(child, search, res, constr,defaults)} 
            }
        }
    };
    
    z.Element.implement({
        select:function(search,result,constr,defaults){
            var res = result || (new z.CompoundElement());
            selectElement(this.dom,search,res,constr,defaults);
            return res;
        },
        children:function(search,result,constr,defaults){
            search['depth'] = 1;
            return this.select(search,result,constr,defaults); 
        }    
    });
    
    z.Element.select = function(search,defaults){
    	return z.document.select(search,null,this,defaults); //
    }
    
})();
 
z.select = function(search){
    return z.document.select(search);
} 
 
z.CompoundElement = z.Class.extend({
    elements:[], //list of z.Element items
    construct:function(){
        this.elements = [];
    },
   /**
    * selects among all children
    */
    select:function(search, result, cons, defaults){
        var result = result || new z.CompoundElement();
        var i,len = this.elements.length;
        for(i=0;i<len;i++){
            this.elements[i].select(search, result, cons, defaults);
        }
        return result;
    },
   /**
    * selects among all children
    */
    children:function(search, result, cons, defaults){
        search['depth'] = 1;
        return this.select(search,result1,cons, defaults); 
    },    
   /**
    * call function for each element
    */
    each:function(fn){
        //var args = args || []
        var i,len = this.elements.length;
        for(i=0;i<len;i++){
            if (fn.apply(this.elements[i],[this.elements[i]])===false) 
                break;
        }
    },
   /**
    *
    */
    get:function(key){
        return this.elements[key];
    } 
}); 
/**
 * 
 */ 
(function(){
    var events = {
		on:function(eventname,fn,remove){
			this.each(function(el){
				el.on(eventname,fn,remove);
			});
			return this;
		},		
		addClass:function(className){
		    this.each(function(el){
                el.addClass(className);
            })
		},
	    removeClass:function(className){
            this.each(function(el){
                el.removeClass(className);
            })
        }		        
    }
	z.each(['click','mousedown','mouseup','mousemove','mouseover','mouseout','focus','blur','change','keypress','keydown','keyup'],
	    function(key,event){
	    	events[event] = new Function("fn","remove","return this.on('"+[event]+"',fn,remove)");
	    }
	)
	z.CompoundElement.implement(events);
})();
/**
 * document.js Domready event implementation
 *
 * @depends base.js, element.js, event.js
 * @example examples/core/empty
 *  
 */
z.document = {}
z.copy(z.document,new z.Element({'dom':document}));
z.ccopy(z.document,{
    isReady:false,
  	on:function(name,func,remove){
  		if (name=='ready' && this.isReady){
			func.apply(this);
  			return;
  		}
  		this.base(name,func,remove);
  	},
  	ready:function(func){
  		this.on('ready',func);
  	},
  	
  	getHeight:function(){
  	    var de = this.dom.body.parentNode;
        var db = this.dom.body;
        return ((db.scrollHeight>de.scrolleight)?db.scrollHeight:de.scrollHeight);
  	},
  	getWidth:function(){
        var de = this.dom.body.parentNode;
        var db = this.dom.body;
        return ((db.scrollWidth>de.scrollWidth)?db.clientWidth:de.scrollWidth);
  	},
  	
  	getWindowHeight:function(){
        var de = this.dom.body.parentNode;
        var db = this.dom.body;
        if (window.opera) {
            return db.clientHeight;     
        }
        if (document.compatMode=='CSS1Compat'){
            return de.clientHeight;     
        }
        else {
            return db.clientHeight;
        }  	
  	},
  	
  	getWindowWidth:function(){
        var de = this.dom.body.parentNode;
        var db = this.dom.body;
        if(window.opera){
            return db.clientWidth;
        }
        if (document.compatMode=='CSS1Compat'){
            return de.clientWidth;
        }
        else {
            return db.clientWidth;
        }  	
  	},

	getScrollTop:function(){
    	return document.documentElement.scrollTop || document.body.scrollTop;
	},

    getScrollLeft:function(){
    	return document.documentElement.scrollLeft || document.body.scrollLeft;
	}
});
document._z = z.document;

(function(){
	
	var domready = function(){
		if (z.document.isReady) return;
		z.document.isReady = true;
		z.document.fireEvent('ready');
	};
	
	if (z.browser.engine == 'trident'){
		if (window == top){
			var temp = document.createElement('div');
			(function(){
			try {
				temp.doScroll(); // Technique by Diego Perini
				domready();
			} 
			catch(e){
				setTimeout(arguments.callee,50);
			};
			})();
		}
		else {
			z(window).addEvent('load', domready);
		}
	} else if (z.browser.engine == 'webkit'){
		(function(){
			if ( document.readyState != "loaded" && document.readyState != "complete" ) {
					setTimeout(arguments.callee,50);
					return;
			}
			domready();
		})();
	} else {
		z.document.addEvent('DOMContentLoaded', domready);
	}
})(); 

/**
 * json.js - JSON functions
 *
 * @depends base.js
 * @example examples/core/empty
 * 
 */
 
z.json = {
/**
 * z.json.toJSONString(value)
 * 
 */		
  toJSONString:function(value){
  		if (value==null) return 'null';
        switch (typeof value) {
        case 'object':
        	if (value.constructor === Array){
				return this.arrayToJSONString(value);
			}
			else {
				return this.objectToJSONString(value);
			}
        case 'string':
           	return this.stringToJSONString(value);
        case 'number':
           	return this.numberToJSONString(value);
        case 'boolean':
           	return this.booleanToJSONString(value);
        }  
  },
/**
 * z.json.arrayToJSONString(value) 
 */
  arrayToJSONString:function(value) {
        var a = [], i,          // Loop counter.
            l = value.length,
            v;          // The value to be stringified.
        for (i = 0; i < l; i += 1) {
            v = value[i];
            a.push(this.toJSONString(v));
        }
        return '[' + a.join(',') + ']';
  },
  /**
   * z.json.booleanToJSONString(value) 
   */  
  booleanToJSONString:function (value) {
        return String(value);
  },
  /**
   * z.json.dateToJSONString(value) 
   */    
  dateToJSONString:function (value) {
        function f(n) {
            return n < 10 ? '0' + n : n;
        }
        return '"' + value.getUTCFullYear() + '-' +
                f(value.getUTCMonth() + 1) + '-' +
                f(value.getUTCDate()) + 'T' +
                f(value.getUTCHours()) + ':' +
                f(value.getUTCMinutes()) + ':' +
                f(value.getUTCSeconds()) + 'Z"';
  },
  /**
   * z.json.numberToJSONString(value) 
   */      
  numberToJSONString:function (value) {
        return isFinite(value) ? String(value) : 'null';
  },
  /**
   * z.json.objectToJSONString(value) 
   */        
  objectToJSONString:function (value) {
        var a = [],     // The array holding the partial texts.
            k,          // The current key.
            v;          // The current value.
        if (value==null) return 'null';
        for (k in value) {
            if (typeof k === 'string' && Object.prototype.hasOwnProperty.apply(value, [k])) {
                v = value[k];
                switch (typeof v) {
                case 'object':
                	if (v.constructor === Array){
						a.push(this.stringToJSONString(k) + ':' + this.arrayToJSONString(v));
					}
					else {
						a.push(this.stringToJSONString(k) + ':' + this.objectToJSONString(v));
					}
				break;
                case 'string':
                	a.push(this.stringToJSONString(k) + ':' + this.stringToJSONString(v));
                break;
                case 'number':
                	a.push(this.stringToJSONString(k) + ':' + this.numberToJSONString(v));
                break;
                case 'boolean':
                    a.push(this.stringToJSONString(k) + ':' + this.booleanToJSONString(v));
                break;
                }
            }
        }
        return '{' + a.join(',') + '}';
  },
  /**
   * z.json.stringToJSONString(value) 
   */          
  stringToJSONString:function (value) {
        var m = {
            '\b': '\\b',
            '\t': '\\t',
            '\n': '\\n',
            '\f': '\\f',
            '\r': '\\r',
            '"' : '\\"',
            '\\': '\\\\'
        };
        if (/["\\\x00-\x1f]/.test(value)) {
                return '"' + value.replace(/[\x00-\x1f\\"]/g, function (a) {
                    var c = m[a];
                    if (c) {
                        return c;
                    }
                    c = a.charCodeAt();
                    return '\\u00' +
                        Math.floor(c / 16).toString(16) +
                        (c % 16).toString(16);
                }) + '"';
            }
            return '"' + value + '"';
  },
  /**
   * z.json.parseJSON(value,filter) 
   */            
  parseJSON:function (value,filter) {
    var j;
    function walk(k, v) {
                var i;
                if (v && typeof v === 'object') {
                    for (i in v) {
                        if (Object.prototype.hasOwnProperty.apply(v, [i])) {
                            v[i] = walk(i, v[i]);
                        }
                    }
                }
                return filter(k, v);
    }
    if (/^[,:{}\[\]0-9.\-+Eaeflnr-u \n\r\t]*$/.test(value.
                    replace(/\\./g, '@').replace(/"[^"\\\n\r]*"/g, ''))) {
                j = eval('(' + value + ')');
                return typeof filter === 'function' ? walk('', j) : j;
    }
    else {
    	if (this.debug){
    		alert(value);
    	}
    	throw new SyntaxError('parseJSON');
    }
  }
}
/**
 * ajax.js - AJAX implementation
 *
 * @depends json.js, base.js
 * @example examples/core/empty
 * 
 */

z.Ajax = z.Class.extend({
  server:'',
  debug:true,
  requests:{},
  requests_num:0,
  construct:function(server){
		this.server = server;  
  },
  callFunction:function(){
	var r = new z.AjaxRequest(this.server); // this.getTransport();
	r.callFunction(arguments);
  },
  
  callFunctionArg:function(func_name,args){
	var r = new z.AjaxRequest(this.server); // this.getTransport();
	r.callFunctionArg(func_name,args);
  },   
  initForm:function(form_id){
      var that = this; 
	  z.document.ready(function(){
		  var form = z(form_id);
		  if (form){
			  that.setupForm(form,form_id);
		  }
	  });
  },
  setupForm:function(form,form_id){
	  var name = '_ajax_'+Math.ceil(Math.random()*10000);
	  // add additional field
	  var value = z.json.toJSONString({'o':form_id});
	  var input = new z.Element({'tag':'INPUT','prop':{'type':'hidden','name':'r','value':value}})
	  input.putInto(form);
	  form.dom.action = this.server;
	  form.dom.target = name;
	  // create iframe
	  if (z.browser.engine == 'trident'){
		  var tagname = '<iframe name="'+name+'">';
	  }
	  else {
		  var tagname = 'IFRAME';
	  }
      var iframe = new z.Element({'tag':tagname,'prop':{'name':name},'css':{'width':'1px','height':'1px','position':'absolute','top':'0px','left':'-10000px'}});
	  iframe.putInto(document.body);	  
  	}
});

z.Ajax.stat({
    renderResponses:function(responses){
        for(var i=0;i<responses.length;i++){
            var response = responses[i];
            this.renderResponse(response);
        }
    },
    
    renderResponse:function(response){
        if (response.c=='cm'){ // call method
            var params = [];
            for(var i=0;i<response.a.length;i++){
                params[i] = "response.a["+i+"]";
            }
            var s = response.o+"."+response.m+"("+params.join(",")+")";
            eval(s);
        }
        if (response.c=='sc'){ // script call
            var params = [];
            for(var i=0;i<response.a.length;i++){
                params[i] = "response.a["+i+"]";
            }
            var s = response.f+"("+params.join(",")+")";
            eval(s);
        }    
        else if (response.c=='a'){ // call method
            alert(response.m);
        }
        else if (response.c=='e'){ // eval script
            eval(response.s);
        }
        else if (response.c=='s'){ // aSsign
            var obj = document.getElementById(response.e);
            if (obj) obj[response.p] = response.v;
        }         
    }
});

z.AjaxRequest = z.Class.extend({
	server:'',
	construct:function(server){
		this.xmlhttp = this.getTransport();
		this.server = server;
	},
	getTransport:function(){
  		try {
			return new ActiveXObject("Microsoft.XMLHTTP");
		} 
		catch (e) {}
		try {
			return new XMLHttpRequest();
		} 
		catch (e) {}		
    },

	callFunction:function(args){
		var func = args[0];
		var el_argx = [];
		for(var i=1;i<args.length;i++){
			el_argx.push(args[i]); 
		}
		var request = {'f':func,'a':el_argx};
		var json_request = z.json.toJSONString(request);
		this.xmlhttp.open('post',this.server,true);
		this.xmlhttp.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
		this.xmlhttp.send('r='+encodeURIComponent(json_request));
		var this1 = this;
		this.xmlhttp.onreadystatechange = function(){
			if (this1.xmlhttp.readyState==4){
				var responses = z.json.parseJSON(this1.xmlhttp.responseText);
				z.Ajax.renderResponses(responses);
			}
		}
	},
	
	callFunctionArg:function(func_name,args){
		var el_argx = [];
		for(var i=0;i<args.length;i++){
			el_argx.push(args[i]); 
		}
		var request = {'f':func_name,'a':el_argx};
		var json_request = z.json.toJSONString(request);
		this.xmlhttp.open('post',this.server,true);
		this.xmlhttp.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
		this.xmlhttp.send('r='+encodeURIComponent(json_request));
		var this1 = this;
		this.xmlhttp.onreadystatechange = function(){
			if (this1.xmlhttp.readyState==4){
				var responses = z.json.parseJSON(this1.xmlhttp.responseText);
				z.Ajax.renderResponses(responses);
			}
		}
	}
});

/**
 * logger.js Debugging messages
 * 
 * it uses console log in mozilla or KHTML based browsers and creates floating div to show messages 
 * in IE based browsers
 *
 * @depends base.js, element.js
 * @example examples/core/empty
 * 
 */



z.Logger = {
  panel:null,
/**
 * z.Logger(m) shows debug message in IE
 */
  log:function(m){
    if(!this.panel){
    	this.panel = new z.Element({'tag':'DIV','css':{'position':'absolute','background':'white','overflow':'scroll','width':'300px','height':'100px','top':'10px','left':'10px'}});
    	this.panel.putInto(document.body);
    }
    this.panel.dom.innerHTML = ''+m+'<br>'+this.panel.dom.innerHTML;
  }
}

/**
 * z.log(msg) shows debug log
 * 
 * @param msg debug message
 */
z.log = function(msg){
    if (typeof(console)!=='undefined'){
        console.log("%s",msg);
    } 
    else {
        z.Logger.log(msg);
    }
};
/**
 * form.js Form serialization and events
 * thanks to prototype
 * @depends base.js, element.js
 * @example examples/core/empty
 * 
 */
z.Form = z.Element.extend({
    getValues:function(){
        //var objForm;
        var submitDisabledElements=false;
        if(arguments.length > 1 && arguments[1]==true)
            submitDisabledElements=true;var prefix="";
        if(arguments.length > 2)
            prefix=arguments[2];
        var values = {};
            var formElements=this.dom.elements;
            for(var i=0;i < formElements.length;i++){
                if(!formElements[i].name) continue;
                if(formElements[i].name.substring(0,prefix.length)!=prefix) continue;
                if(formElements[i].type && (formElements[i].type=='radio' 
                || formElements[i].type=='checkbox') && formElements[i].checked==false) continue;
                if(formElements[i].disabled && formElements[i].disabled==true && submitDisabledElements==false) continue;
                var name=formElements[i].name;
                if(name){
                    values[name] = formElements[i].value;
                }
            }
        //}
        return values;
    }
});
z.Form.zname = '_zf';
z.Form.shortEvents(['submit','reset']); //onsubmit event


/**
 * delayer.js - Execution of function with delay
 * autostart defaultvalue is = true
 * @depends base.js
 * @example examples/core/empty
 *  
 */

z.delay = function(fn,timeout,autostart,repeat){
    var d = new z.Delayer(fn, timeout,(autostart===false?false:true), repeat);
    return d;
};

z.Delayer = z.Class.extend({
    fn:null,
    delay:null,
    repeat:false,
    timeout:null,
    construct:function(fn, delay, autostart, repeat){
        this.fn = fn;
        this.delay = delay;
        this.repeat = repeat;
        if (autostart) this.call();
    },
    start:function(){
        this.stop();
        this.call();
    },
    call:function(){
        var this1 = this;
        this.timeout = window.setTimeout(function(){
        	this1.callTimeout();
        },this.delay);
    },
    callTimeout:function(){    
        this.fn();
        if (this.repeat && (this.timeout!==null)){ //if timeout==null that means amimation was stoppend
            this.call();
        }
        else {
            this.timeout = null;
        }
    },
    stop:function(){
        if (this.timeout){
            window.clearTimeout(this.timeout);
            this.timeout = null;
        }
    }
});

/**
 * animation.js - Animation toolkit
 * @depends base.js
 * @example examples/core/empty
 * 
 */



 
z.Animation = z.Class.extend({
    onStart:function(){},
    onStop:function(){},    
    onStep:function(){},
    
    animation:null,
    duration:300, //duration in ms
    rate:24, //count of frames per second
    step_number:0,
    count_steps:-1,
    data:null, //array of animation data
    timing:null, //array of animation intervals between frames
    timer_id:false,
    animator:false, //default anumator
    construct:function(props){
      z.copy(this,props);
      this.data = [];
      this.timing = [];
      this.animator = this.animator || z.Animation.Ease;
      this.animator.calculate(this.animation, this.data, this.duration, this.rate,this.timing);
    },
    start:function(){
      this.stop(); 
      this.step_number = 0;
      this.onStart();
      this.doStep();
    },
    stop:function(){
    	if (!this.timer_id){
    		clearTimeout(this.timer_id);
    		this.timer_id = false;
    	}
    },
    doStep:function(){
    	var data = this.data[this.step_number];
        this.onStep(data);
    	if (typeof(this.timing[this.step_number])=='undefined'){
    		this.onStop();
    		this.timer_id = false;
    		return;
    	}
    	var this1 = this;
    	var timeout = this.timing[this.step_number];
    	this.timer_id = setTimeout(function(){this1.doStep()},Math.round(timeout));
        this.step_number++;
    }
});

/**
 * values should be two items array - first and last points
 */
z.Animation.Line = {
   calculate:function(animation,data,duration,rate,timing){
	  var dt = 1000/rate,t=0,i=0;
	  
	  for(;;t+=dt){ //calculate timings
		  data[i++] = {};
		  if (t+dt>=duration){
			  timing.push(duration-t);  
			  break;
		  }
		  timing.push(dt);
	  }
	  data[i++] = {}; //count of items alway more then timings
	  z.each(animation,function(value_name,values){
		    var i=0,t=0, b=values[0], a = (values[1]-values[0])/duration;
		    for(;;t+=dt){
		    	data[i++][value_name] = a*t+b;
				if (t+dt>=duration){
					//add final point
					data[i++][value_name] = a*duration+b; 
					break;
				}
		    }
      });
   }
};

z.Animation.Ease = {
	calculate:function(animation,data,duration,rate,timing){
		var dt = 1000/rate,t=0,i=0;
		for(;;t+=dt){ //calculate timings
			data[i++] = {};
			if (t+dt>=duration){
				timing.push(duration-t);  
				break;
			}
			timing.push(dt);
		}
		data[i++] = {}; //count of items alway more then timings
		z.each(animation,function(value_name,values){
			var i=0,t=0, c=values[0], a = (values[0]-values[1])/(duration*duration), b=2*(values[1]-values[0])/duration;
			for(;;t+=dt){
				data[i++][value_name] = a*t*t+b*t+c;
				if (t+dt>=duration){
					//add final point
					data[i++][value_name] = a*duration*duration+b*duration+c; 
					break;
				}
			}
		});
	}
};


/**
 * imagecache.js - Caching of images
 *
 * @depends base.js
 * @example examples/core/empty
 *  
 */
z.ImageCache = {
	list:[],
	add:function(url){
	    var v = new Image();
		    v.src = url;
		this.list.push(v);
	}
}

/**
 * effects.js Transition effects
 */
z.Element = z.Element.extend({
	fadeIn:function (callback){
		var that = this;
		var a = new z.Animation({
			'duration':500,
			'animation':{
				'opacity':[0,1]
			},
			'onStep':function(data){
				that.css('opacity',data.opacity);
			},
			'onStop':function(data){
				if (callback) callback();
			}			
		});
		a.start();		
	
	},
	
	fadeOut:function(callback){
		var that = this;
		var a = new z.Animation({
			'duration':500,
			'animation':{
				'opacity':[1,0]
			},
			'onStep':function(data){
				that.css('opacity',data.opacity);
			},
			'onStop':function(data){
				if (callback) callback();
			}
		});
		a.start();		
	}
	
});