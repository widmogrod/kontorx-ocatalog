/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

var swiffy = new FancyUpload2($('upload-status'), $('upload-list'), {
  'url': $('upload-form').action,
  'fieldName': 'photoupload',
  'limitFiles': 30,
  'path': 'js/mootools/FancyUpload2/Swiff.Uploader.swf',
  'fileCreate' : function(file){
          file.info = new Element('span', {'class': 'file-info'});
          file.element = new Element('li', {'class': 'file'}).adopt(
                  new Element('span', {'class': 'file-size', 'html': this.sizeToKB(file.size)}),
                  new Element('span', {'class': 'file-name', 'html': file.name}),
                  file.info
          ).inject(this.list);
  },
  'fileComplete' : function(file, response){
          console.log(response);
          var json = new Hash(JSON.decode(response, true));
          //WMessage.add(json.message, json.success);
          if (json.success) {
                  file.info.store('id', json.info.id);
                  file.info.grab(new Element('span',{ 'class': 'action ico small true' }));
          } else {
                  file.info.grab(new Element('span',{ 'class': 'action ico small false' }));
          }
  }
});

/**
* Various interactions
*/

$('demo-browse-images').addEvent('click', function() {
swiffy.browse({'Images (*.jpg, *.jpeg, *.gif, *.png)': '*.jpg; *.jpeg; *.gif; *.png'});
return false;
});

$('demo-clear').addEvent('click', function() {
swiffy.removeFile();
return false;
});

$('demo-upload').addEvent('click', function() {
swiffy.upload();
return false;
});

