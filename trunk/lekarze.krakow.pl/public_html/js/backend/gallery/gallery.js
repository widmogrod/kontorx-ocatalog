/**
 * Gallery manager
 */
var GalleryManager = new Class({
	Implements: Options,

	options: {
		'url': {
			'getAllCategories':'?getAllCategories',
			'getGalleriesForCategory':'?getGalleriesForCategory={id}',
			'getImagesForGallery':'?getImagesForGallery={id}',
			'deleteImage':'?deleteImage={id}',
			'addImageToGallery':'?addImageToGallery:image_id={image_id}&gallery_id={gallery_id}',
			'removeImageFromGallery':'?removeImageFromGallery={id}'
		},
		'loading' : 'loading.gif'
	},
	
	initialize: function(options) {
		this.setOptions(options);
		
		// inicjowanie zmiennych instancji
		this.galleries 	= new Hash();
		this.categories = new Hash();
		this.images		= new Hash();
		
		this.activeGalleryID = null;
	},

	requestStart: function(){
		$('loading').setStyle('display','inline');
	},
	
	requestEnd: function(){
		$('loading').setStyle('display','none');
	},

	setActiveGalleryID: function(id){
		this.activeGalleryID = id;
	},

	getActiveGalleryID: function(){
		return this.activeGalleryID;
	},

	loadCategories: function(){
		var self = this;
		var jsonRequest = new Request.JSON({
			url: this.options.url.getAllCategories,
			async: false,
			onRequest: this.requestStart,
			onComplete: function(categories){
		    	self.categories = categories;
		    	self.requestEnd();
			}
		}).get();
	},

	getCategories: function() {
		return this.categories;
	},

	loadGalleryForCategory: function(id){
		var self = this;

		if (this.galleries.has(id)) {
			return;
		}

		var jsonRequest = new Request.JSON({
			url: this.options.url.getGalleriesForCategory.replace('{id}',id),
			async: false,
			onRequest: this.requestStart,
			onComplete: function(galleries){
		    	self.galleries.set(id,galleries);
		    	self.requestEnd();
			}
		}).get();
	},

	getGalleryForCategory: function(id) {
		return this.galleries.get(id);
	},

	loadImagesForGallery: function(id, forse){
		var self = this;

		if (this.images.has(id) && forse == 'undefined') {
			return;
		}

		var jsonRequest = new Request.JSON({
			url: this.options.url.getImagesForGallery.replace('{id}',id),
			async: false,
			onRequest: this.requestStart,
			onComplete: function(images){
		    	self.images.set(id, images);
		    	self.requestEnd();
			}
		}).get();
	},

	getImagesForGallery: function(id) {
		return this.images.get(id);
	},

	addImageToGallery: function(image_id, gallery_id){
		var self = this;

		var result = false;
		var jsonRequest = new Request.JSON({
			url: this.options.url.addImageToGallery.replace('{image_id}',image_id).replace('{gallery_id}',gallery_id),
			async: false,
			onRequest: this.requestStart,
			onComplete: function(response){
		    	result = response.success;
		    	self.requestEnd();
			}
		}).get();
		return result;
	},

	deleteImage: function(image_id){
		var self = this;

		var result = false;
		var jsonRequest = new Request.JSON({
			url: this.options.url.deleteImage.replace('{image_id}',image_id),
			async: false,
			onRequest: this.requestStart,
			onComplete: function(response){
		    	result = response.success;
		    	self.requestEnd();
			}
		}).get();
		return result;
	},

	removeImageFromGallery: function(id) {
		var self = this;

		var result = false;
		var jsonRequest = new Request.JSON({
			url: this.options.url.removeImageFromGallery.replace('{id}',id),
			async: false,
			onRequest: this.requestStart,
			onComplete: function(response){
		    	result = response.success;
		    	self.requestEnd();
			}
		}).get();
		return result;
	},

	createOptionList: function(json, options) {
		var opt = new Hash({
			'name':'name',
			'value':'value'
		});
		if($chk(options)) {
			opt.extend(options);
		}
	
		var el,name,value = null;
		var result = [];

		if (typeof json != 'object' || json == null) {
			return result;
		}

		json.each(function(item){
			item = new Hash(item);

			name = item.get(opt.name);
			value = item.get(opt.value);
	
			el = new Element('option',{
				'text':name,
				'value':value
			});
	
			result.push(el);
		});

		return result;
	},

	createImagesList: function(json, options) {
		var self = this;

		var opt = new Hash({
			'id':'id',
			'image':'image',
			'path':'/',
			'onElement':$empty
		});
		if($chk(options)) {
			opt.extend(options);
		}
	
		var el,image,path,id = null;
		var result = [];

		if (typeof json != 'object' || json == null || json === {}) {
			return result;
		}

		json.each(function(item){
			item = new Hash(item);

			id    = item.get(opt.id);
			image = item.get(opt.image);
			path  = item.has(opt.path) ? item.get(opt.path) : opt.path;

			imageEl = new Element('img',{
				'src': (path + image)
			});

			var bindOnElement = opt.onElement.bind(self);
			el = bindOnElement(imageEl, item, {
				'id':id,
				'image':image,
				'path':path
			});
	
			result.push(el);
		});
		
		return result;
	}
});

window.addEvent('domready', function() {
  /**
   * Menager galerii
   */

  var galleryManager = new GalleryManager({
          'url': {
                  'getAllCategories':'gallery/category/list/rowCount/300?format=json',
                  'getGalleriesForCategory':'gallery/gallery/list/gallery_category_id/{id}/rowCount/300?format=json',
                  'getImagesForGallery':'gallery/image/list/rowCount/300/gallery_id/{id}/?format=json',
                  'deleteImage':'gallery/image/delete/id/{image_id}?format=json',
                  'addImageToGallery':'gallery/image/imagetogallery/image_id/{image_id}/gallery_id/{gallery_id}/',
                  'removeImageFromGallery':'gallery/image/removeimagefromgallery/id/{id}/'
          }
  });

  // wczytywanie kategorii
  var categoryJSON 	= galleryManager.loadCategories();
  categoryJSON 		= galleryManager.getCategories()
  // tworzenie z nich elemntow <option>
  var categoryOptionList = galleryManager.createOptionList(categoryJSON, {'value':'id'});
  // wstrzykiwanie elementu option do pola select
  $('gallery-category')
          .empty()
          .adopt(categoryOptionList)
          .addEvent('change', function(){
                  var id = this.value;
                  // wczytywanie galerii dla kategorii o danym `id`
                  galleryManager.loadGalleryForCategory(id);
                  galleryJSON = galleryManager.getGalleryForCategory(id);
                  // tworzenie z nich elemntow <option>
                  var galleryOptionList = galleryManager.createOptionList(galleryJSON, {'value':'id'});
                  // dodanie Eventow
                  galleryOptionList.each(function(item){
                          item.addEvent('click',function(){
                                  // ustawienie ID aktywnej galerii
                                  galleryManager.setActiveGalleryID(this.value);
                                  renderImagesForGallery(this.value);
                          })
                  });
                  // wstrzykiwanie elementu option do pola select
                  $('gallery-gallery')
                          .empty()
                          .adopt(galleryOptionList);
          });

  renderImagesFree();


  $('gallery-images-refresh').addEvent('click', function(e){
          e.stop();
          var galleryID = galleryManager.getActiveGalleryID();
          renderImagesForGallery(galleryID, true);
  });

  $('gallery-images-free-refresh').addEvent('click', function(e){
          e.stop();
          renderImagesFree(true);
  });

  // tworzenie listy grafik które są przypisane do tej galerii
  function renderImagesForGallery(id, forse) {
          galleryManager.loadImagesForGallery(id);
          var imagesJSON 		= galleryManager.getImagesForGallery(id, forse);
          var imagesListFree 	= galleryManager.createImagesList(imagesJSON, {
                  'path':'/upload/gallery/thumb/',
                  'onElement': onElementImage
          });
          $('gallery-images')
                  .empty()
                  .adopt(imagesListFree);
  }

  // tworzenie listy grafik które są nie przypisane do żadnej galerii
  function renderImagesFree(forse) {
          galleryManager.loadImagesForGallery('null',forse);
          var imagesJSON 		= galleryManager.getImagesForGallery('null');
          var imagesListFree 	= galleryManager.createImagesList(imagesJSON, {
                  'path':'/upload/gallery/thumb/',
                  'onElement': onElementImageFree
          });
          $('gallery-images-free')
                  .empty()
                  .adopt(imagesListFree);
  }

  function onElementImage(imageEl, item, opt) {
          var self = this;

          el = new Element('div', {'class':'options-holder','id':'image-'+opt.id});

          optionsEl = new Element('ul',{'class':'options-list top-right'}).adopt(
                  new Element('li',{}).adopt(
                          // usun dowiazanie do galerii
                          new Element('a',{
                                  'class':'action icon small attach',
                                  'events': {
                                          'click':function(e){
                                                  e.stop();
                                                  if(self.removeImageFromGallery(opt.id) === true) {
                                                          // odwolywanie sie do elementu div poprzez id dlatego ze `el` cos nie dziala ..
                                                          $('image-'+opt.id).destroy();
                                                  } else {
                                                          alert('Powiązanie grafiki z galerią NIE zostało usunięte');
                                                  }
                                          }
                                  }
                          })
                  )
          );

          el.adopt(imageEl, optionsEl);
          return el;
  }

  function onElementImageFree(imageEl, item, opt) {
          var self = this;

          el = new Element('div', {'class':'options-holder','id':'image-free'+opt.id});

          optionsEl = new Element('ul',{'class':'options-list top-right'}).adopt(
                  new Element('li',{}).adopt(
                          // dodaj do galerii
                          new Element('a',{
                                  'class':'action icon small add',
                                  'events': {
                                          'click':function(e){
                                                  e.stop();

                                                  var galleryID = self.getActiveGalleryID();
                                                  if(galleryID == null) {
                                                          alert('Nie wybrano galerii!');
                                                  } else {
                                                          if (self.addImageToGallery(opt.id, galleryID) === true) {
                                                                  $('image-free'+opt.id).destroy();
                                                          } else {
                                                                  alert('Grafika NIE została powiązana z galerią');
                                                          }
                                                  }
                                          }
                                  }
                          })
                  ),
                  new Element('li',{}).adopt(
                          // dodaj do galerii
                          new Element('a',{
                                  'class':'action icon small trash',
                                  'events': {
                                          'click':function(e){
                                                  e.stop();
                                                  if (self.deleteImage(opt.id) === true) {
                                                          $('image-free'+opt.id).destroy();
                                                  } else {
                                                          alert('Grafika NIE została usunięta z galerią');
                                                  }
                                          }
                                  }
                          })
                  )
          );

          el.adopt(imageEl, optionsEl);
          return el;
  }
});