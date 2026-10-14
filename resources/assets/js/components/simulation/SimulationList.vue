<template>
<div>
    <div v-if="localLoading == 'true'" class="simulation_loader">
      <img src="/userassets/images/loading-gif.gif" alt="loading" width ="50" />
    </div>
    <div v-else>
      <div v-if="sessionValue != ''" class="alert alert-success alert-dismissible">
        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
        <strong>Success! </strong>{{ sessionValue }}
      </div>
      <div v-if="simulations.length > 0">
          <div v-for="(simulation, index) in simulations" :key="simulation.id" class="si__NameBox">
            <ul class="list-unstyled checklist checklist2">
              <li>
                <label>
                  {{ simulation.simulation_name }}
                  <span style="position: absolute; left: 0">{{ index + 1 }}</span>
                </label>
                <a v-if="simulation.edit_status === 0" :href="'/simulationedit/' + simulation.id" style="margin-left: auto; margin-right: 10px;">
                  <button class="dltBtn"><i class="far fa-edit" style="color: darkgrey;"></i></button>
                </a>
                <a :href="'/simulationdelete/' + simulation.id">
                  <button class="dltBtn"><img :src="url + '/userassets/images/dlt.svg'"></button>
                </a>
              </li>
            </ul>
            <div class="urlDetails">
              <div class="ip_box">
                <div class="form-group" v-for="(tokens, index) in simulation.tokens_getter" :key="index">
                  <label v-if="index < 1">User Links</label>
                  <div class="form-group token-container">
                    <input class="form-control" :value="simulation.url + '/' + tokens.token" style="color:#080808" disabled>
                    <button class="copybtn"  v-if="canCopy" @click="copyToken(simulation.url + '/' + tokens.token)">Copy</button>
                  </div>
                  <br />
                </div>
              </div>
              <div class="ip_box">
                <div class="otpDetails">
                  <div class="si__btnGrp2">
                    <button v-if="!simulation.url"  :id="'generate_token_button' + simulation.id" :disabled="disable_generate_buttton === true" @click="token_generator(simulation.id,$event)">Generate Token</button>
                    <button v-else :id="'chat_start_button' + simulation.id" @click="start_chat(simulation.id)">Start Chat</button>
                    <button v-if="simulation.url" @click="print_token(simulation.id)" type="button">Print</button>
                  </div>
                </div>
              </div>
            </div>
          </div>
      </div>
      <div v-else>
        No active simulations found
      </div>
    </div>
   </div>
 </template>
 
 <script> 
 export default {
    props: {
      sessionValue: String, // The prop to receive the session value from the parent component
      loading: Boolean
    },
    data() {
      return {
        simulations: [],
        disable_generate_buttton :false,
        url: window.location.origin,
        localLoading: this.loading,
        canCopy:false
      };
    },
    mounted(){
      document.onreadystatechange =() =>{
        if(document.readyState =="complete"){
          this.onloaded = true;
        } 
      }
      
    },   

    created() {
      this.loadData();
      this.canCopy = !!navigator.clipboard;
    },
    
    methods: {
      async loadData() {
        try {
          const [simulationsResponse] = await Promise.all([axios.get('/get_simulation')]);
          this.simulations = simulationsResponse.data;
          this.localLoading = false;
        } catch (error) {
          console.error('Error fetching simulations:', error);
        }
      },
 
      print_token(id) {
        window.open("print_token/" + id, "_blank");
      },
 
      start_chat(simulationid) {
        window.location.href = 'start_chat/' + simulationid;
      },
      get_simulations(){
        axios.get('/get_simulation').
              then(response => {
                console.log(response.data);
                this.simulations=response.data;
              });
        },

      token_generator(id,event) {
        this.disable_generate_buttton = true;
          axios.get('/token_creation/'+id).
              then(response => {
                this.disable_generate_buttton = false;
                this.res=response.data;  
                this.get_simulations();          
                  
              }); 
          },

          async copyToken (testingCodeToCopy) {
            try {
                await navigator.clipboard.writeText(testingCodeToCopy);
              } catch (err) {
                alert('Oops, unable to copy');
              }
            /* unselect the range */
            window.getSelection().removeAllRanges()
          },
  

   },
 };
 </script>
 