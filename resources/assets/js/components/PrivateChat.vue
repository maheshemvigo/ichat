<template>
  <section class="mainScreen">
     <div class="chatListwrp">
        <div class="searchBox">
           <div class="input-group">
              <div class="input-group-prepend">
                 <div class="input-group-text">
                    <i class="fa fa-search"></i>
                 </div>
              </div>
              <input type="text" class="form-control" placeholder="Search" v-model="user_search" id="user_search"/>
           </div>
        </div>
        <div class="chatList" id="search_div">
           <div
              class="chatUser"
              v-for="(friend, index) in friends"
              :color="(friend.user_id==activeFriend)?'red':''"
              :key="friend.id"
              @click="requestbutton(friend.id,friend.name,index)"
              :class="friend.name.split('-')[2]"
              >
              <div
                 class="userNum"
                 :style="'background-color:'+friend.color_code"
                 :data-color="friend.color_code"
                 >
                 {{friend.userfirstname}}
                 <img
                    v-if="usertype==0"
                    class="rounded-circle"
                    style="width:100%;height:100%;object-fit: cover;"
                    :src="url+'/uploads/'+friend.image"
                    />
                 <div v-if="friend.main_user_id">
                    <span v-if="(onlineFriends.find(user=>user.id==friend.user_id))"></span>
                    <span v-if="!(onlineFriends.find(user=>user.id==friend.user_id))" style="background-color:red"></span>
                 </div>
              </div>
              <div class="userDet">
                 <h6>
                    {{friend.name}}
                    <p v-if="usertype==0">({{friend.role}})</p>
                 </h6>
              </div>
              <div class="count-div-wrapper">
                  <div class="timebox" :data-item="index">
                    <h3 v-if="friend.message_count!=0">{{friend.message_count}}</h3>
                  </div>
                 <ul class="list-unstyled checklist check-box-clicked-p d-none">
                    <li>
                       <label>
                       <input type="checkbox"/>
                       <input class="user_new_id" type="hidden" :value="friend.id" />
                       <span class="checkmark"></span>
                       </label>
                    </li>
                 </ul>
              </div>
           </div>
        </div>
     </div>
     <div v-bind:class="[((usertype === 0) && (user_first_status === 1))? 'd-none' : 'chatArea']">
        <div class="userHead">
           <h3 class="selectusername-p">{{selectusername}}</h3>
           <div class="detBox">
              <div class="btnGrp">
                 <button
                    data-toggle="modal"
                    data-target="#exampleModalleaveusers"
                    v-if="usertype==1"
                    style="background: #E04F5F"
                    >End Simulation</button>
              </div>
              <div class="loginBox">
                 <ul class="list-unstyled">
                    <li v-if="usertype==1">
                       <button @click="logoutfun();">&nbsp &nbspLog out</button>
                    </li>
                    <li v-else>
                       <button @click="userlogoutfun();">Log out</button>
                    </li>
                    <li v-if="usertype==1">Welcome {{username}}</li>
                    <li v-if="usertype==1">
                       <figure>
                          <img class="img-fluid" :src="url+'/userassets/images/user.png'" />
                          <span></span>
                       </figure>
                    </li>
                 </ul>
              </div>
           </div>
        </div>
        <!-- <div v-bind:class="[((welcome_status == '0'))? 'welcomePage welcomePage2' : 'welcomePage welcomePage2 d-none']" v-if="usertype==1"> -->
          <div v-bind:class="'welcomePage welcomePage2'" v-if="usertype==1">
           <h1>Welcome Subadmin!</h1>
           <div style="width: 100px; height: 100px; border-radius: 50%; border: 2px solid #9e9292;">
           <figure style="width: 100%; height: 100%; margin: 0;">
              <img  :style="{ width: '100%', height: '100%', objectFit: 'contain', borderRadius: '50%' }" :src="url+'/userassets/images/imagelogo.png'" class="img-fluid">
              <span></span>
           </figure>
           </div>
        </div>
        <div  v-bind:class="[((welcome_status.welcome_status == '0'))? 'welcomePage welcomePage2' : 'welcomePage welcomePage2 d-none']" v-if="usertype==0">
           <h1>Welcome {{username.username}}</h1>
           <h5>Conversation yet to start</h5>
        </div>
        <div v-bind:class="[((welcome_status == '1') || (welcome_status.welcome_status == '1'))? 'botarea' : 'botarea d-none']">
           <div class="botarea__in" v-if="this.activeFriend">
              <div id="privateMessageBox" v-bind:class="[(usertype === 1)? 'chatTypingArea' : 'chatTypingArea studentChat']">
                 <div class="topArea" ref="messagesContainer">
                    <message-list :ipuser='ipaddress' :user="user" :allMessages="allMessages" :typingFriend="typingFriend"></message-list>
                 </div>
              </div>
              <div class="typingArea" ref="trackedElement">
                 <div :id="'typingfiled-p'" class="typingfiled-p">
                    <div :id="'append-fev-p'">
                    </div>
                    <ul :id="'privatechannel-p'" class="list-unstyled">
                       <li class="disble-p" @click="toggleEmo">
                          <img :src="url+'/userassets/images/smile.svg'" />
                       </li>
                       <li class="ip_li">
                          <textarea
                             class="form-control"
                             placeholder="Type a message"
                             v-model="message"
                             single-line
                             @keydown="handleKeyDown"
                             ></textarea>
                       </li>
                       <li @click="sendMessage" dark>
                          <img :src="url+'/userassets/images/send.svg'" />
                       </li>
                    </ul>
                 </div>
                   <div v-if="emoStatus" class="floating-div">
                     <picker set="emojione" @select="onInput" title="Pick your emoji…" />
                   <!-- <Emoji /> -->
                   </div>
                 <ul
                    :id="'check-all-p'"
                    class="list-unstyled checklist checklist2"
                    v-if="usertype==1"
                    >
                    <li :id="'checkbox-return-p'" @click="toggleMessage">
                       <label>
                       Broadcast Message
                       <input type="checkbox" id="broadcastCheckbox"/>
                       <span class="checkmark"></span>
                       </label>
                    </li>
                 </ul>
              </div>
           </div>
           <!-- media panel -->
           <media-searchlist
              :simulationid="simulationid"
              :usertype="usertype"
              :ipaddress='ipaddress'
              :username="username"
              :user="user"
              :all-messages="allMessages"
              :typingFriend="typingFriend"
              ></media-searchlist>
           <!-- media panel -->
        </div>
     </div>
     <div v-bind:class="[((usertype === 0) && (user_first_status === 1))? 'dashboardArea' : 'dashboardArea d-none']"
        >
        <div class="noMediawrp">
           <h1>Thank you!!</h1>
           <h2>The conversation is finished</h2>
           <figure>
              <img :src="url+'/userassets/images/thanku.png'" />
           </figure>
           <button v-if= "usertype === 0" @click="userlogoutfun();" class="upload-btn2">Exit</button>
           <button v-if ="usertype === 1" @click="logoutfun();" class="upload-btn2">Exit</button>
        </div>
     </div>
     <div class="modal fade mediaModal regenerate-p" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
           <div class="modal-content">
              <div class="modal-body">
                 <h4>Do you really want to Regenerate pin ?</h4>
                 <div class="si__btnGrp">
                    <button class="add-btn" data-dismiss="modal" @click="regenerate_pin();">Yes</button>
                    <button  class="add-btn" data-dismiss="modal">No</button>
                 </div>
              </div>
           </div>
        </div>
     </div>
     <div class="modal fade mediaModal leaveusers-p" id="exampleModalleaveusers" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
           <div class="modal-content">
              <div class="modal-body">
                 <h4>Do you really want to End Simulation ?</h4>
                 <div class="si__btnGrp">
                    <button class="add-btn" data-dismiss="modal"  @click="leaveusers();">Yes</button>
                    <button  class="add-btn" data-dismiss="modal">No</button>
                 </div>
              </div>
           </div>
        </div>
     </div>
  </section>
</template>
<script>
  import MessageList from "./_message-list";
  import MediaList from "./media-searchlist";
  import { Picker } from "emoji-mart-vue";
  import _debounce from "lodash.debounce";

  function debounce(func, wait) {
    let timeout;
    return function () {
      const context = this;
      const args = arguments;
      const later = function () {
        timeout = null;
        func.apply(context, args);
      };
      clearTimeout(timeout);
      timeout = setTimeout(later, wait);
    };
  }

  export default {
    props: ["user", "simulationid"],
    components: {
      Picker,
      MessageList,
      MediaList
    },

    data() {
      this.debouncedFetchMessages = debounce(this.fetchMessages, 500);
      return {
        message:'',
        // brocastmessage: null,
        files: [],
        activeFriend: null,
        typingFriend: {},
        onlineFriends: [],
        allMessages: [],
        typingClock: null,
        emoStatus: false,
        // emoStatusb: false,
        users: [],
        selectusername: "",
        user_first_status: "",
        usertype: "",
        welcome_status: "",
        username: "",
        url: window.location.origin,
        err: [],
        doubleclick: false,
        val: "",
        token: document.head.querySelector('meta[name="csrf-token"]').content,
        user_search:"",
        ipaddress:'',
        isSending: false,
        // isSendingb: false,
      };
    },

    computed: {
      friends() {
        return this.users.filter(user => {
          return user.user_id !== this.user.id;
        });
      }
    },

    watch: {
      files: {
        deep: true,
        handler() {
          let success = this.files[0].success;
          if (success) {
            // this.fetchMessages();
            this.debouncedFetchMessages();
          }
        }
      },
      activeFriend(val) {
        // this.fetchMessages();
        this.debouncedFetchMessages();
      },
      "$refs.upload"(val) {}
    },

    mounted() {
      this.listenlisten();
      this.brodcastlisten();
      document.addEventListener('click', this.handleOutsideClick);
    },

    beforeUnmount() {
      document.removeEventListener('click', this.handleOutsideClick);
    },

    updated() {
      if (this.activeFriend) {
        let c = this.friends.length;
        for(let i=0; i<=c; i++) {
          if(this.friends[i] !== undefined && (this.activeFriend == this.friends[i].id)) {
            this.friends[i].message_count = 0;
          }
        }
      }
    },

    created() {

      this.fetchUsers();

      Echo.join("plchat")
        .here(users => {
          this.onlineFriends = users;
        })
        .joining(user => {
           this.onlineFriends.push(user);
           if(user.type == 0){
              axios.post("/statuschange/", {
                roomstatus:'joining',
                name: user.name,
                simulation_id:user.simulation_id,
                _token: this.token
              });
           }
        })
        .leaving(user => {
          this.onlineFriends.splice(this.onlineFriends.indexOf(user), 1);

          if(user.type == 0){
             axios.post("/statuschange/", {
              roomstatus:'leaving',
              name: user.name,
              simulation_id:user.simulation_id,
              _token: this.token
            });
          }
        });

      Echo.private("privatechat." + this.user.id)
        .listen("PrivateMessageSent", e => {
          axios.post("/delivery_status/" + e.message.id + "/1", {
            message: e.message,
            _token: this.token
          });
          this.fetchUsers();
          if (((this.user.type == 1) && (this.activeFriend == e.message.admin_chat_id)) || ((this.user.type != 1) && (this.activeFriend == e.message.chat_user_identification_id))) {
             axios.post("/delivery_status/" + e.message.id + "/2", {
                message: e.message,
                _token: this.token
              });
              this.allMessages.push(e.message);
              this.debouncedFetchMessages();
            }
          // if (this.user.type == 1) {
          //   if (this.activeFriend == e.message.admin_chat_id) {
          //     axios.post("/delivery_status/" + e.message.id + "/2", {
          //       message: e.message,
          //       _token: this.token
          //     });
          //     // this.fetchMessages();
          //     // this.allMessages.push(e.message);
          //   }
          // } else {
          //   if () {
          //     // this.fetchMessages();
          //     // this.allMessages.push(e.message);

          //     axios.post("/delivery_status/" + e.message.id + "/2", {
          //       message: e.message,
          //       _token: this.token
          //     });
          //   }
          // }
          setTimeout(this.scrollToEnd, 100);
        })
        .listen("MessageDelivered", e => {
          // this.fetchMessages();
          this.debouncedFetchMessages();
        });
    },
    methods: {

      regenerate_pin() {

        axios.post("/regeneratepin/", {
            simulation_id: this.simulationid,
            _token: this.token
          }).then(response => {
            $(document).find('.regenrate-pin-p').removeClass('d-none');
            setTimeout(function(){
               $(document).find('.regenrate-pin-p').addClass('d-none');
             }, 2500);
          });
      },

      logoutfun() {
        $("#logout-form").submit();
      },

      userlogoutfun() {
        $("#userlogout-form-p").submit();
      },

      leaveusers() {
        axios.get("/leaveroom/" + this.simulationid).then(response => {
            if(this.user.admin_type==='super'){
                window.location = this.url+"/simulation_history?pending_report=" + this.simulationid;
            }else{
                window.location = this.url+"/simulationcreate/";

            }

             setTimeout(function(){
               $(document).find('.endsimulation-p').addClass('d-none');
             }, 2500);
       //   }
        });
      },

      listenlisten() {
        Echo.channel("exit").listen("ExitSimulation", e => {
           if(e.simulation_id == this.user.simulation_id)
           {
            $("body").find(".dashboardArea").removeClass("d-none");
            $("body").find(".chatArea").addClass("d-none");

           }

        });
      },

      brodcastlisten() {
        Echo.private("broadcastchat." + this.user.id).listen(
          "BroadcastMessageSent",
          e => {
            this.fetchUsers();
            // this.fetchMessages();
            this.debouncedFetchMessages();
          }
        );
      },

      requestbutton(id, value , index) {
        this.doubleclick = false;
        this.selectusername = value;
        this.activeFriend = id;
        this.$data.users[index].message_count=0
        if(this.usertype == 1){
          var ws = this.welcome_status;
        }else{
          var ws = this.welcome_status.welcome_status;
        }
        // if(ws === 0){
           axios.post("/welcomestatus/",{
            welcome_status: 1,
            _token: this.token
          }).then(response => {
           this.welcome_status = response.data.welcome_status;
          //  this.fetchMessages();
          });

        // }else{
          // this.fetchMessages();
        // }
        this.debouncedFetchMessages();
      },

      onTyping() {
        Echo.private("privatechat." + this.activeFriend).whisper("typing", {
            user: this.user,
        });
      },

      handleKeyDown(event) {
        if (event.key === 'Enter' && !event.shiftKey) {
          // Prevent sending a message when "Shift + Enter" is pressed
          event.preventDefault();
          this.sendMessage();
        }
      },

      sendMessage() {

      // if (this.isSending) return;

      if (!this.activeFriend) {
          event.preventDefault();
          var modalFooter = '<div class="modal-body">   <h4>Please select friend</h4>' +
            '  <div class="si__btnGrp"><button class="add-btn btn-danger" data-dismiss="modal">Okay</button></div>\n' +
            '            </div>';
          var modal = $("#confirm_modal");
          modal.find(".modal-title").remove();
          modal.find(".modal-footer").remove();
          modal.find(".modal-header").remove();
          $(".modal-body").html("").html(modalFooter);
          $("#confirm_modal").modal("show");
      }

      let usersid = [];
      let filetype = '';
      let filepath = '';
      let upload_id = '';
      let ext ='';
      let link = '';

      let url = `/private-messages/${this.activeFriend}/${this.user.simulation_id}`;
      if(this.isBroadcast) {
        $("body").find(".chatUser").each(function(index, value) {
          if($(this).find("input[type=checkbox]").prop("checked") == true) {
              this.val = $(this).find(".user_new_id").val();
              usersid.push(this.val);
          }
        });
        url = `/brodcastmessage/`;
      }

      const $vdodocImg = $('#append-fev-p #vdodoc-img-p');
      if ($vdodocImg.hasClass('fev-vdo-p')) {
        filetype = $vdodocImg.val();
        filepath = $vdodocImg.attr('data-vdo');
        upload_id = $vdodocImg.attr('data-upload_id');
        ext = $vdodocImg.attr('data-ext');
      } else if ($vdodocImg.hasClass('fev-doc-p')) {
        filetype = $vdodocImg.val();
        filepath = $vdodocImg.attr('data-doc');
        upload_id = $vdodocImg.attr('data-upload_id');
        ext = $vdodocImg.attr('data-ext');
      } else if ($vdodocImg.hasClass('fev-img-p')) {
        filetype = $vdodocImg.val();
        filepath = $vdodocImg.attr('data-img');
        upload_id = $vdodocImg.attr('data-upload_id');
        ext = $vdodocImg.attr('data-ext');
      }

        if($('#append-fev-p').find('#vdodoc-img-p').hasClass('fev-link-p')){
          link = $('#vdodoc-img-p').val();
        }

        if(filepath == '' && link == '' && (!this.message || this.message == null || this.message == ' ')) {
          event.preventDefault();
          var modalFooter = '<div class="modal-body">   <h4>Please enter message</h4>' +
            '  <div class="si__btnGrp"><button class="add-btn btn-danger" data-dismiss="modal">Okay</button></div>\n' +
            '            </div>';
          var modal = $("#confirm_modal");
          modal.find(".modal-title").remove();
          modal.find(".modal-footer").remove();
          modal.find(".modal-header").remove();
          modal.find(".modal-body").html("").html(modalFooter);
          $("#confirm_modal").modal("show");
          return null;
        }

        const data = {
          message: this.message,
          type: filetype,
          filepath: filepath,
          ext: ext,
          upload_id: upload_id,
          _token: this.token,
          link:link
        };
        if(this.isBroadcast){
          this.$set(data, 'usersid', usersid);
        }

        try {
          if (!this.isSending){
            this.isSending = true;
            axios
            .post(url,data)
              .then((response) => {
                this.message = null;
                this.allMessages.push(response.data.message);
                $('#append-fev-p').html('');
                $('#typingfiled-p').removeClass('active');
                $('#privateMessageBox').removeClass('hight__c');
                $('#privateMessageBox').removeClass('hight__cDoc');
                // this.fetchMessages();
                this.debouncedFetchMessages();
                $('#broadcastCheckbox').prop("checked", false);
                this.toggleMessage()
                setTimeout(this.scrollToEnd, 100);
              })
            .catch((error) => {
              console.error('Error sending message:', error);
              // Handle error: Show error message or take appropriate action
            })
            .finally(() => {
              this.isSending = false; // Reset the flag after the response is received
            });
            }
        }catch (error) {
          console.error('Error sending message:', error);
          // Handle error: Show error message or take appropriate action
          this.isSending = false; // Reset the flag in case of an error
        }

      },

      fetchMessages() {
        if (!this.activeFriend) {
          //return alert("Please select friend");
        }
        axios.get("/private-messages/" + this.activeFriend+"/"+this.user.simulation_id).then(response => {

          this.welcome_status = 1;
          this.allMessages = response.data.privateCommunication;

          if (this.doubleclick == false) {
            axios
              .get("/delivery_status_click/" + response.data.user_id)
              .then(response => {
                this.doubleclick = true;
              });
          }
          setTimeout(this.scrollToEnd, 100);
        });
      },

      fetchUsers() {
        axios.get("/users/" + this.simulationid).then(response => {
          this.usertype = response.data.user_type;
          this.username = response.data.username;
          this.welcome_status = response.data.welcome_status;
          this.user_first_status = response.data.status;
          this.users = response.data.var;
          this.ipaddress = response.data.ipaddress;
           if (this.friends.length > 0) {
              //this.activeFriend=this.friends[0].id;
           }
        });
      },

      scrollToEnd() {
        document.getElementById("privateMessageBox").scrollTo(0, 99999);
      },

      toggleEmo() {
        this.emoStatus = !this.emoStatus;
      },
    //  toggleEmob() {
    //     this.emoStatusb = !this.emoStatusb;
    //   },

      onInput(e) {
        if (!e) {
          return false;
        }
        if (!this.message) {
          this.message = e.native;
        } else {
          this.message = this.message + e.native;
        }
        this.emoStatus = false;
        // this.emoStatusb = false;
      },
      //  onInputb(e) {
      //   if (!e) {
      //     return false;
      //   }
      //   if (!this.brocastmessage) {
      //     this.brocastmessage=e.native
      //      this.message = '';
      //   } else {
      //     this.brocastmessage=this.brocastmessage+ e.native;
      //     this.message = '';
      //   }
      //   this.emoStatus = false;
      //   this.emoStatusb = false;
      // },

      onResponse(e) {
        console.log('on response file up',e);
      },

      toggleMessage() {
        const checkboxElement = document.getElementById('broadcastCheckbox');
        const isChecked = (typeof(checkboxElement) != 'undefined' && checkboxElement != null)?checkboxElement.checked:false;
        var filter_val = $('.selectusername-p').text(); 
        // var p_chat = $('#privatechannel-p > li > textarea');
		    // var b_chat = $('#brodcast-p > li > textarea');
        // var filter_val = $('.selectusername-p').text();


        if (isChecked) {
          this.isBroadcast = true;
          // if(p_chat.val() != '') {
          //   b_chat.val(p_chat.val());
          //   p_chat.val("");
          // }

          $('div.chatList > div > div > ul').removeClass('d-none');
          // $('.chatUser > .timebox').addClass('d-none');
          // $('#privatechannel-p').addClass('d-none');
          // $('#brodcast-p').removeClass('d-none');
          var res = filter_val.split("-");

          if (filter_val != null) {
            // $('.' + res[2]).find(".select-check").prop("checked", true);
            $('.chatUser:not(:contains(' + res[2] + '))').find("div > ul ").addClass('d-none');
            $('.chatUser:contains(' + res[2] + ')').find("div > ul > li > label > input[type=checkbox]").prop("checked", true);
          }
          // $('div.' + res[2]+' > div > ul > li > label > input[type=checkbox].select-check"').prop("checked", true);
        } else {
          this.isBroadcast = false;

          // $('.chatUser > .timebox').removeClass('d-none');
          $('div.chatList > div > div > ul').addClass('d-none');
          $('div> div > ul > li > label > input[type=checkbox]').prop("checked", false);
          // $('#brodcast-p').addClass('d-none');
          // $('#privatechannel-p').removeClass('d-none');
          // $('div.' + res[2]+' > div > ul > li > label > input[type=checkbox]').prop("checked", false);
        }
      },
      handleOutsideClick(event) {
        if (!this.$refs.trackedElement.contains(event.target)) {
          if(this.emoStatus == true) {
            this.emoStatus = false;
          }
        }
      },
    }
  };

  $(document).ready(function(){
     $(document).on('click','#linkid-p', function(e){
         e.preventDefault();
         let linkval= $(this).text();
         window.open(linkval, '_blank');
     });

     $("#user_search").on("keyup", function() {
         var value = $(this).val().toLowerCase();
         $(".chatUser").filter(function() {
           $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
         });
     });
  });
</script>
