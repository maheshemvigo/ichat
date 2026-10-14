<template>
   <div>
      <span id="ip-address-p" class="d-none">{{ipuser}}</span>
      
      <input id="ipaddresstest" type="hidden" :data-ip='ipuser' :value="ipuser">
      <span v-for="(message, index) in allMessages" :key="index">
         <div bind v-bind:class="[((user.id===message.user_id) && (message.user_id != '' ))? 'receiveBox' : 'sendBox']" class="">
            <span>{{message.created_at | moment('LLL')}}</span>
            <div class="content txt-formatin-p" v-if="message.message !== null && message.type === 3">
               <div v-if="message.link">
                  <a target="_blank" :href="message.link">
                  <!-- <img :src="url+'/userassets/images/links.svg'" alt="" width="30" height="20"> -->
                  {{ message.link }}
                  </a>
                  {{message.message}}                  
               </div>
               <div v-else v-html="message.message"></div>
               <div v-if="message.user_id != '' && user.id==message.user_id">
                  <img class="tick-mark-p" v-if="message.delivery_status==0" :src="url+'/userassets/images/tickashsingle.png'">
                  <img class="tick-mark-p" v-if="message.delivery_status==1" :src="url+'/userassets/images/tickash.png'">
                  <img class="tick-mark-p" v-if="message.delivery_status==2" :src="url+'/userassets/images/tickblue.png'">
               </div>   
            </div>
            <div class="content txt-formatin-p" v-if="message.message === null && message.type === 3">
               <div v-if="message.link">
                  <a target="_blank" :href="message.link">
                  <!-- <img :src="url+'/userassets/images/links.svg'" alt="" width="30" height="20"> -->
                  {{ message.link }}
                  </a>                
               </div>
               <div v-else v-html="message.message"></div>
               <div v-if="message.user_id != '' && user.id==message.user_id">
                  <img class="tick-mark-p" v-if="message.delivery_status==0" :src="url+'/userassets/images/tickashsingle.png'">
                  <img class="tick-mark-p" v-if="message.delivery_status==1" :src="url+'/userassets/images/tickash.png'">
                  <img class="tick-mark-p" v-if="message.delivery_status==2" :src="url+'/userassets/images/tickblue.png'">
               </div>   
            </div>
             <div class="content txt-formatin-p" v-if="message.message !== null && message.type === 0">
                <div>
                  <video v-if="message.video" width="200" height="140" controls>
                     <source :src="urls+'/uploads/videoupload/'+message.video" type="video/mp4">
                     <source :src="urls+'/uploads/videoupload/'+message.video" type="video/ogg">
                     Your browser does not support the video tag.
                  </video>
               </div>
               {{message.message}}
               <div v-if="message.user_id != '' && user.id==message.user_id">
                  <img class="tick-mark-p" v-if="message.delivery_status==0" :src="url+'/userassets/images/tickashsingle.png'">
                  <img class="tick-mark-p" v-if="message.delivery_status==1" :src="url+'/userassets/images/tickash.png'">
                  <img class="tick-mark-p" v-if="message.delivery_status==2" :src="url+'/userassets/images/tickblue.png'">
               </div>   
            </div>

            <div class="content txt-formatin-p" v-else-if="message.message === null && message.type === 0">
                <div>
                  <video v-if="message.video" width="200" height="140" controls>
                     <source :src="urls+'/uploads/videoupload/'+message.video" type="video/mp4">
                     <source :src="urls+'/uploads/videoupload/'+message.video" type="video/ogg">
                     Your browser does not support the video tag.
                  </video>
               </div>
               <div v-if="user.id==message.user_id">
                  <img class="tick-mark-p" v-if="message.delivery_status==0" :src="url+'/userassets/images/tickashsingle.png'">
                  <img class="tick-mark-p" v-if="message.delivery_status==1" :src="url+'/userassets/images/tickash.png'">
                  <img class="tick-mark-p" v-if="message.delivery_status==2" :src="url+'/userassets/images/tickblue.png'">
               </div>   
            </div>

             <div class="content txt-formatin-p" v-if="message.message !== null && message.type === 1">
                <div class="image-container">
                    <a target="_blank" :href="urls+'/uploads/imageupload/'+message.image">
                  <img v-if="message.image"  :src="urls+'/uploads/imageupload/'+message.image" alt="" width="200" height="140">
                  </a>
               </div>
               {{message.message}}
               <div v-if="message.user_id != '' && user.id==message.user_id">
                  <img class="tick-mark-p" v-if="message.delivery_status==0" :src="url+'/userassets/images/tickashsingle.png'">
                  <img class="tick-mark-p" v-if="message.delivery_status==1" :src="url+'/userassets/images/tickash.png'">
                  <img class="tick-mark-p" v-if="message.delivery_status==2" :src="url+'/userassets/images/tickblue.png'">
               </div>   
            </div>
            
            <div class="content txt-formatin-p" v-else-if="message.message === null && message.type === 1">
                <div class="image-container">
                     <a target="_blank" :href="urls+'/uploads/imageupload/'+message.image">
                  <img v-if="message.image"  :src="urls+'/uploads/imageupload/'+message.image" alt="" width="200" height="140">
                  </a>
               </div>
               <div v-if="message.user_id != '' && user.id==message.user_id">
                  <img class="tick-mark-p" v-if="message.delivery_status==0" :src="url+'/userassets/images/tickashsingle.png'">
                  <img class="tick-mark-p" v-if="message.delivery_status==1" :src="url+'/userassets/images/tickash.png'">
                  <img class="tick-mark-p" v-if="message.delivery_status==2" :src="url+'/userassets/images/tickblue.png'">
               </div>   
            </div>

            <div class="content txt-formatin-p" v-if="message.message !== null && message.type === 2">
               <div class="image-container">
                  <a target="_blank" :href="'/uploads/fileupload/'+message.doc">
                     <img v-if="message.ext === 'doc'" :src="url+'/userassets/images/docc.svg'" width="100" height="100">
                     <img v-else-if="message.ext === 'pdf'" :src="url+'/userassets/images/pdf.svg'" width="100" height="100">
                     <img v-else :src="url+'/userassets/images/images.png'" width="100" height="100">
                  </a>
               </div>
               {{message.message}}
               <div v-if="message.user_id != '' && user.id==message.user_id">
                  <img class="tick-mark-p" v-if="message.delivery_status==0" :src="url+'/userassets/images/tickashsingle.png'">
                  <img class="tick-mark-p" v-if="message.delivery_status==1" :src="url+'/userassets/images/tickash.png'">
                  <img class="tick-mark-p" v-if="message.delivery_status==2" :src="url+'/userassets/images/tickblue.png'">
               </div>   
            </div>

            <div class="content txt-formatin-p" v-else-if="message.message === null && message.type === 2">
               <div class="image-container">
                  <a target="_blank" :href="urls+'/uploads/fileupload/'+message.doc">
                     <img v-if="message.ext === 'doc'" :src="url+'/userassets/images/docc.svg'" width="100" height="100">
                     <img v-else-if="message.ext === 'pdf'" :src="url+'/userassets/images/pdf.svg'" width="100" height="100">
                     <img v-else :src="url+'/userassets/images/images.png'" width="100" height="100">
                  </a>
               </div>
               <div v-if="message.user_id != '' && user.id==message.user_id">
                  <img class="tick-mark-p" v-if="message.delivery_status==0" :src="url+'/userassets/images/tickashsingle.png'">
                  <img class="tick-mark-p" v-if="message.delivery_status==1" :src="url+'/userassets/images/tickash.png'">
                  <img class="tick-mark-p" v-if="message.delivery_status==2" :src="url+'/userassets/images/tickblue.png'">
               </div>   
            </div>

         </div>
      </span>
   </div>
</template>
<script>

  export default {
    props:['user','allMessages','typingFriend','ipuser'],
    created(){  
    },
     data() {
		return {
       url:window.location.origin,
       urls:'',
       ip:'',
    }
     
  },
     updated() {
      if(this.ipuser!= null && this.ipuser != undefined && this.ipuser != 0){
         this.urls='http://'+this.ipuser;
      }else{
         this.urls=window.location.origin;
      }
    
  },
  }

</script>

<style scoped>
.chat-card{
  margin-bottom:140px;
}
.floating-div{
    position: fixed;
}
.chat-card img {
    max-width: 300px;
    max-height: 200px;
}

</style>
