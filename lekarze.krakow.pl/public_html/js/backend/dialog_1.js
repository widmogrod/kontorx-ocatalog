/**
 * Dialog
 *
 * @author Marcin `widmogrod` Habryn, widmogrod@gmail.com
 * @license GNU GPL
 */
var Dialog = new Class({
	options: {
		'type': '',
		'title': '',
		'content': '',
		'types': ['info','notice','warning','error'],
		'buttons': ['ok','cancel','close']
	},

    initialize: function(options){
    	if ($chk(options)) {
    		this.options = $extend(this.options, options);
    	}

		this.element = element = new Element('div', {'class':'dialog'});
    	var close = new Element('span', {'class':'dialog-close', 'events':{
			'click' : function (){
				element.destroy();
			}
		}});

		this.title = title = new Element('div', {'class' : 'dialog-title', 'text' : this.options.title});
		var titleContainer = new Element('div', {'class' : 'dialog-title-container'}).adopt(
			title,
			close
		);
		this.content = new Element('div', {'class':'dialog-content', 'text' : this.options.content});
		this.buttins = new Element('div', {'class':'dialog-buttons'});

    	this.element.adopt(
    		titleContainer,
    		this.content,
    		this.buttins
    	);
    	
    	this.setType(this.options.type);
    },

    setType: function(type) {
    	if (this.options.types.indexOf(type) < 0) { return false; }
		this.element.set('class', 'dialog ' + type);
    },

    getType: function() {
    	var classes = this.element.get('class').split(' ');
    	for (i=0; i <= classes.lenght; i++) {
    		if (this.options.types.indexOf(type) < 0) {
    			return type;
    		}
    	}
    	return false;
    },

    setTitle: function(title) {
    	this.title.set('text',title);
    },

    getTitle: function() {
    	return this.title.get('text');
    },
    
    setContent: function(content) {
    	this.content.set('text',content);
    },
    
    getContent: function() {
    	return this.content.get('text');
    },
    
    setButtons: function() {},
    
    addButton: function(button) {
    	this.buttins.grab(button);
    },
    
    getButtons: function() {},

    destroy: function() {
    	this.element.destroy();
    }
});

window.addEvent('domready', function() {
	var d = new Dialog({
		'type': 'error',
		'title':'Tak to jest tytul',
		'content':'Popelniles fatala!'
	});
	d.addButton(new Element('a',{'class':'button action small false','text':'Anuluj','events':{'click':function(){d.destroy();}}}));
	$(document.body).grab(d.element);
});

