var RecordManager = new Class({

	// przekazanie elementu, ktory bedzie przechowywal liste rekordow
	initialize: function(holder, options){
        this.holder = holder;
        this.options = $chk(options) ? options: {};

         // budowanie podstawowego widoku dla rekordu
        this.list = new Element('ul', {'class':'record-list lists table'});
        this.holder.grab(this.list);
    },

    add: function(record){
    	this.list.adopt(record);
    },
    
    edit: function(){},
    
    delete: function(){},
    
    createRecord: function(options){
    	var self = this;
    	var options = $merge({
    		'title':'EE: nie podano tytulu',
    		'description':'EE: nie podano opisu',
    		'options': {
    			'edit' : 'edutuj'
    		}
    	}, options);

    	var record = new Element('li', {'class':'record-item options-holder'});
    	record.adopt(
    		new Element('h6', {'class':'record-title', 'text':options.title}),
    		new Element('p', {'class':'record-description', 'text':options.description}),
    		new Element('ul', {'class':'record-options options-list top-right'}).adopt(
    			new Element('li', {'class':'record-option'}).grab(
    				// delete
    				new Element('a', {'class':'action small ico delete', 'text':options.options.edit,'events':{
    					'click': function(){
    						self.onDelete(record);
    					}
    				}})
    			)
    		)
    	);
    	return record;
    },

	onDelete: function(record){
		// sprawdz czy jest zdefiniowany onDelete
		if ($chk(this.options.onDelete)) {
			// jezeli callback nie zwroci true, usun element z widoku
			if(this.options.onDelete(record) !== true) {
				return;
			}
		}

		record.destroy();
	}
});

window.addEvent('domready', function() {
	var galleryManager = new RecordManager($('category-list'));
	galleryManager.add(galleryManager.createRecord({}));
	galleryManager.add(galleryManager.createRecord({}));
	galleryManager.add(galleryManager.createRecord({}));
	galleryManager.add(galleryManager.createRecord({}));
	
	$('category-add').addEvent('click',function(){
		galleryManager.add(galleryManager.createRecord({}));
		return false;
	});
});