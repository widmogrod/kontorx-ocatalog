var Message = new Class({
    initialize: function(element){
        this.element = element;
    },
    add: function(message, type) {
    	var class = '';
    	switch(type) {
    		case true:
    		case 'success':
    			class = 'msg-success';
    			break;
    		case 'warning':
    			class = 'msg-warning';
    			break;
    		case false:
    		case 'error':
    			class = 'msg-error';
    			break;
    		case undefined:
    		default:
    			class = 'msg-notice';
    			break;
    	}
    	
    	var li = new Element('li',{'class':class, 'text':message});
    	li.fade.bind(li,[0]);
    	var close = new Element('span',{'class':'close'});
    	close.addEvent('click',function(){ li.destroy(); });
    	this.element.grab(li.adopt(
    		close,
    		new Element('span',{'class':'arrow-lb'})
    	));
    }
});

window.addEvent('domready', function() {
	WMessage = new Message($('messages'));
});
