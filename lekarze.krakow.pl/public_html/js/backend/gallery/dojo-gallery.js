/*
  Copyright (c) 2004-2008, The Dojo Foundation All Rights Reserved.
  Available via Academic Free License >= 2.1 OR the modified BSD license.
  see: http://dojotoolkit.org/license for details
*/


dojo.require("dojox.form.FileUploader");
dojo.require("dijit.form.Button"); 
dojo.require("dojo.parser");

//using this early for the forceNoFlash test:
dojo.require("dojox.embed.Flash");

var passthrough = function(msg){
  //for catching messages from Flash
  if(window.console){
    console.log(msg);
  }
}

var uploadUrl = "image/upload/format/dojo";
var rmFiles = "image/delete";
var fileMask = [
  ["All Images","*.jpg;*.JPG;*.jpeg;*.JPEG;*.gif;*.GIF;*.png;*.PNG"]
];

dojo.addOnLoad(function(){
  if(dojox.embed.Flash.available == 0){
    dojo.byId("hasFlash").style.display = "none";
  }else{
    dojo.byId("noFlash").style.display = "none";
  }

//  dojo.byId("uploadedFiles").value = "";
  dojo.byId("fileToUpload").value = "";

  console.log("LOC:", window.location)
  console.log("UPLOAD URL:",uploadUrl);
  var f0 = new dojox.form.FileUploader({
    button: dijit.byId("btn0"),
    degradable:true,
    uploadUrl:uploadUrl,
    uploadOnChange:false,
    selectMultipleFiles:true,
    fileMask:fileMask,
    isDebug:true
  });

  doUpload = function(){
    console.log("doUpload")
    dojo.byId("fileToUpload").innerHTML = "uploading...";
    f0.upload();
  }

  dojo.connect(f0, "onProgress", function(data){
    console.warn("onProgress", data);
    dojo.byId("fileToUpload").value = "";
    dojo.forEach(data, function(d){
            dojo.byId("fileToUpload").value += "("+d.percent+"%) "+d.name+" \n";

    });
  });

  dojo.connect(f0, "onComplete", function(data){
    console.warn("onComplete", data);
    dojo.forEach(data, function(d){
      dojo.byId("uploadedFiles").value += d.file+" \n";
//      dojo.byId("rgtCol").innerHTML += d;
    });
  });
});

var cleanUp = function(){
//  dojo.byId("rgtCol").innerHTML = "";
//  dojo.byId("uploadedFiles").value = "";
  dojo.byId("fileToUpload").value = "";
  dojo.xhrGet({
    url:uploadUrl,
    handleAs:"text",
    content:{
      rmFiles:rmFiles
    }
  });
  rmFiles = "";
}