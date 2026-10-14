<template>
   <div class="mediaMenu" v-if="usertype==1">
    
      <!--add Class "open" to "mediaMenuCont" to show--> 
      <div :id="'mediaMenuCont-p'" class="mediaMenuCont">
         <!-- <div class="videoBox" style="display: none;">
            <div class="headB">
               <input type="hidden" v-model="simulationid">
               <input type="hidden" v-model="ipaddress">
               <h3>Videos <i style="float:right" class="fas fa-angle-right diasbleplace-p"></i></h3>
               <div class="input-group">
                  <div class="input-group-prepend">
                     <div class="input-group-text"><i class="fa fa-search"></i></div>
                  </div>
                  <input class="form-control" v-model="vdokeywords"  placeholder="Search videos">
               </div>
            </div>
            <div class="fileBoxwrp">
               <div v-for="result in results" :class="'fileBox'" @dblclick="sendvdo(result.ext,result.upload_file_id,result.path_name, result.id, result.type);">
                  <span>
                  <img :src="urls+'/userassets/images/video2.svg'">
                  </span>
                  <h5>{{result.name}}</h5>
               </div>
            </div>
         </div> -->
         <div class="documentsBox" style="display: none;">
            <div class="headB">
               <h3>Documents <i style="float:right" class="fas fa-angle-right diasbleplace-p"></i></h3>
               <div class="input-group">
                  <div class="input-group-prepend">
                     <div class="input-group-text"><i class="fa fa-search"></i></div>
                  </div>
                  <input class="form-control" v-model="dockeywords" placeholder="Search Documents">
               </div>
            </div>
            <div  class="fileBoxwrp">
               <div v-for="docresult in docresults" :class="'fileBox'" @dblclick="senddoc(docresult.ext,docresult.upload_file_id,docresult.path_name, docresult.id, docresult.type);">
                  <span>
                  <img :src="urls+'/userassets/images/doc.svg'">
                  </span>
                  <h5>{{docresult.name}}</h5>
               </div>
            </div>
         </div>
         <div class="imagesBox" style="display: none;">
            <div class="headB">
               <h3>Images <i style="float:right" class="fas fa-angle-right diasbleplace-p"></i></h3>
               <div class="input-group">
                  <div class="input-group-prepend">
                     <div class="input-group-text"><i class="fa fa-search"></i></div>
                  </div>
                  <input class="form-control" v-model="imgkeywords" placeholder="Search Images">
               </div>
            </div>
            <div  class="fileBoxwrp">
               <div v-for="imgresult in imgresults" :class="'fileBox'" @dblclick="sendimg(imgresult.ext,imgresult.upload_file_id,imgresult.path_name, imgresult.id, imgresult.type);">
                  <span>
                  <img :src="urls+'/userassets/images/img.svg'">
                  </span>
                  <h5>{{imgresult.name}}</h5>
               </div>
            </div>
         </div>
         <div class="linksBox" style="display: none;">
            <div class="headB">
               <h3>Video Links <i style="float:right" class="fas fa-angle-right diasbleplace-p"></i></h3>
               <div class="input-group">
                  <div class="input-group-prepend">
                     <div class="input-group-text"><i class="fa fa-search"></i></div>
                  </div>
                  <input class="form-control" v-model="linkkeywords" placeholder="Search Links">
               </div>
            </div>
            <div  class="fileBoxwrp">
               <div v-for="linkresult in linkresults" :class="'fileBox'" @dblclick="sendlink(linkresult.link);">
                  <span>
                  <img :src="urls+'/userassets/images/links.svg'" width="20px">
                  </span>
                  <h5>{{linkresult.name}}</h5>
               </div>
            </div>
         </div>
      </div>
        <!--add Class "open" to "ul" to show-->
      <ul :id="'list-unstyled-P'" class="list-unstyled open">
         <li id="link-toggle"><img :src="urls+'/userassets/images/links2.svg'" width="25px"><span>Video Links</span></li>
         <li id="image-toggle"><img :src="urls+'/userassets/images/image.svg'"><span>Images</span></li>
         <li id="doc-toggle"><img :src="urls+'/userassets/images/file.svg'"><span>Documents</span></li>
         <!-- <li id="video-toggle"><img :src="urls+'/userassets/images/video.svg'"><span>Videos</span></li> -->
      </ul>
   </div>
</template>
<script>

  export default {
    props:['usertype','username','user','allMessages','typingFriend','simulationid','ipaddress'],
     data() {
		return {
      vdokeywords: null,
      results: [],
      dockeywords:null,
      docresults: [],
      imgkeywords:null,
      imgresults:[],
      linkkeywords:null,
      linkresults:[],
      vdo:'',
      img:'',
      doc:'',
      docname:'',
      link:'',
      new_url:'',
      urls:window.location.origin,
      token: document.head.querySelector('meta[name="csrf-token"]').content,
    }
  },
  mounted() {
   this.fetchdoc();
   this.fetch();
   this.fetchimg();
   this.fetchlink();
  },
   watch: {
      dockeywords(after, before) {
         this.fetchdoc();
      },
      vdokeywords(after, before) {
         this.fetch();
      }, 
      imgkeywords(after, before) {
         this.fetchimg();
      },
      linkkeywords(after, before) {
         this.fetchlink();
      }
    },
   methods: {   
        fetch() {
            axios.post('/featuredvideo/search', { 
                  keywords: this.vdokeywords,
                  simulationid: this.simulationid,
                   _token: this.token    
                  })
                .then(response => {    
                   this.results = response.data;
                })
                .catch(error => {});
        },
        
        fetchdoc() {
            axios.post('/featureddoc/search', { 
                  keywords: this.dockeywords,
                  simulationid: this.simulationid,
                   _token: this.token    
                  })
                .then(response => {    
                   this.docresults = response.data;
                })
                .catch(error => {});
        },

        fetchimg() {
            axios.post('/featuredimg/search', { 
                  keywords: this.imgkeywords,
                  simulationid: this.simulationid,
                   _token: this.token    
                  })
                .then(response => {    
                   this.imgresults = response.data;
                })
                .catch(error => {});
        },

        fetchlink() {
            axios.post('/featuredlink/search', { 
                  keywords: this.linkkeywords,
                  simulationid: this.simulationid,
                   _token: this.token    
                  })
                .then(response => {  
                   this.linkresults = response.data;
                })
                .catch(error => {});
        },

         sendvdo(ext, upload_id, vdopath, vdoid, type){
            if(this.ipaddress != null && this.ipaddress != undefined && this.ipaddress != 0){

               this.new_url =  'http://'+this.ipaddress;
            }else{
               this.new_url =  this.urls;
            }
   
            this.vdo='<div class="newmediabox-p">'+
            '<span id="new-remove-p" class="closeSpan">'+
            '<img src="'+this.urls+'/userassets/images/close2.svg">'+
            '</span>'+
            '<video  width="200" height="140" controls>'+
            '<source src="'+this.new_url+'/uploads/videoupload/'+vdopath+'">'+
            '<source src="'+this.new_url+'/uploads/videoupload/'+vdopath+'">'+
            '</video>'+
            '<input id="vdodoc-img-p" class="fev-vdo-p" type="hidden" data-ext="'+ext+'" data-upload_id="'+upload_id+'" data-vdo="'+vdopath+'" value="'+type+'">'+
            '</div>';
            $('#append-fev-p').html(this.vdo);
            $('#typingfiled-p').addClass('active');
            $('#privateMessageBox').addClass('hight__c');
         },
         senddoc(ext, upload_id, docpath, docid, type){
            if(this.ipaddress != null && this.ipaddress != undefined && this.ipaddress != 0){

               this.new_url =  'http://'+this.ipaddress;
            }else{
               this.new_url =  this.urls;
            }
            if(ext === "pdf"){
               this.docname="pdf.svg";

            }else if(ext === "doc"){
                 this.docname="docc.svg";
            }else{
               this.docname="images.png";
            }

            this.doc=  '<div class="newmediabox-p">'+
             '<span id="new-remove-p" class="closeSpan closeSpan2">'+
            '<img src="'+this.urls+'/userassets/images/close2.svg">'+
            '</span>'+
            '<a target="_blank" href="'+this.new_url+'/uploads/imageupload/'+docpath+'">'+
            '<img src="'+this.urls+'/userassets/images/'+ this.docname+'" width="75" height="75" alt="docpath">'+
             '</a>'+
            '<input id="vdodoc-img-p" class="fev-doc-p" type="hidden" data-ext="'+ext+'" data-upload_id="'+upload_id+'" data-doc="'+docpath+'" value="'+type+'">'+
            '</div>';
            $('#append-fev-p').html(this.doc);
            $('#typingfiled-p').addClass('active');
            $('#privateMessageBox').addClass('hight__c');
            // $('#privateMessageBox').addClass('hight__cDoc');
         },
         sendimg(ext, upload_id,imgpath, imgid, type){
            if(this.ipaddress != null && this.ipaddress != undefined && this.ipaddress != 0){

            this.new_url =  'http://'+this.ipaddress;
            }else{
            this.new_url =  this.urls;
            }
            this.img= '<div class="newmediabox-p">'+
             '<span id="new-remove-p" class="closeSpan">'+
            '<img src="'+this.urls+'/userassets/images/close2.svg">'+
            '</span>'+
            '<a target="_blank" href="'+this.new_url+'/uploads/imageupload/'+imgpath+'">'+
            '<img src="'+this.new_url+'/uploads/imageupload/'+imgpath+'" width="100" height="70" alt="imgpath">'+
            '</a>'+
            '<input id="vdodoc-img-p" class="fev-img-p" type="hidden" data-ext="'+ext+'" data-upload_id="'+upload_id+'" data-img="'+imgpath+'" value="'+type+'">'+
            '</div>';
            $('#append-fev-p').html(this.img);
            $('#typingfiled-p').addClass('active');
            $('#privateMessageBox').addClass('hight__c');
         },
         sendlink(url){
            this.link= '<div class="newmediabox-p">'+            
            '<div class="link-wrp"><a target="_blank" href="#">'+
            '<img src="'+this.urls+'/userassets/images/links.svg" width="30" height="20" alt="imgpath">'+
            '<span>'+url+'</span></a>'+
            '<span id="new-remove-link" class="closeSpan">'+
            '<img src="'+this.urls+'/userassets/images/close2.svg">'+
            '</span></div>'+
            '<input id="vdodoc-img-p" class="fev-link-p" type="hidden" value="'+url+'">'+
            '</div>';
            $('#append-fev-p').html(this.link);
            // this.link = '<input id="vdodoc-img-p" class="fev-link-p" type="hidden" value="'+url+'">'+
            // '</div>'
            // $('li.ip_li textarea').val(url);
            $('#append-fev-p').html(this.link);
            $('#typingfiled-p').addClass('active');
            $('#privateMessageBox').addClass('hight__c');
            // $('#privateMessageBox').addClass('hight__cDoc');
         },
    }

  }
 
</script>
